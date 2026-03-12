<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Passkey;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * يتعامل مع تسجيل وإلغاء تسجيل Passkeys (WebAuthn / بصمة)
 * ومصادقة المستخدمين عبرها.
 *
 * يعمل بدون مكتبات خارجية — يستخدم Web Authentication API (navigator.credentials)
 * مع تحقق أساسي (origin + challenge + userHandle) لأن المتصفح نفسه يتولى التحقق
 * الكامل من التوقيع الكريبتوغرافي عبر authenticator.
 */
class BiometricController extends Controller
{
    public function precheck(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type' => 'required|in:user,supplier',
            'context' => 'nullable|string|in:customer,admin',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $userType = $request->input('user_type');
        $context = $request->input('context', 'customer');

        $rateKey = 'biometric-precheck:' . strtolower($email) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            return response()->json(['error' => 'محاولات كثيرة. حاول مرة أخرى لاحقاً.'], 429);
        }
        RateLimiter::hit($rateKey, 60);

        if ($userType === 'supplier') {
            $supplier = Supplier::where('email', $email)->first();
            if (! $supplier || ! Hash::check($password, $supplier->password)) {
                return response()->json(['error' => 'بيانات الدخول غير صحيحة.'], 422);
            }
            if ($supplier->status === 'pending') {
                return response()->json(['error' => 'حسابك قيد المراجعة. سيتم إعلامك عند الموافقة.'], 403);
            }
            if ($supplier->status === 'rejected') {
                return response()->json(['error' => 'تم رفض طلبك.'], 403);
            }
            if ($supplier->status === 'suspended') {
                return response()->json(['error' => 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.'], 403);
            }
            if (! $supplier->email_verified_at) {
                return response()->json(['error' => 'يرجى تأكيد بريدك الإلكتروني أولاً.'], 403);
            }
        } else {
            $user = User::where('email', $email)->first();
            if (! $user || ! Hash::check($password, $user->password)) {
                return response()->json(['error' => 'بيانات الدخول غير صحيحة.'], 422);
            }
            if ($context === 'admin') {
                if (! $user->isAdmin()) {
                    return response()->json(['error' => 'هذه الصفحة مخصصة للإدارة فقط.'], 403);
                }
            } else {
                if ($user->isAdmin()) {
                    return response()->json(['error' => 'تسجيل الدخول من هذه الصفحة مخصص للعملاء فقط.'], 403);
                }
                if (Supplier::where('email', $email)->exists()) {
                    return response()->json(['error' => 'هذا البريد مرتبط بحساب مورد. يرجى استخدام صفحة دخول المورد.'], 403);
                }
            }
        }

        $sig = hash_hmac('sha256', strtolower($email) . '|' . $userType . '|' . $context, (string) config('app.key'));
        $request->session()->put('biometric_precheck_sig', $sig);
        $request->session()->put('biometric_precheck_at', time());

        return response()->json(['success' => true]);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  Registration
    // ────────────────────────────────────────────────────────────────────────

    /**
     * إنشاء challenge لتسجيل Passkey جديدة.
     * يُستدعى بعد تسجيل دخول ناجح.
     */
    public function registrationOptions(Request $request): JsonResponse
    {
        ['userId' => $userId, 'userType' => $userType] = $this->resolveUser($request);

        if (! $userId) {
            return response()->json(['error' => 'غير مصرح'], 401);
        }

        [$userName, $userDisplayName] = $this->getUserInfo($userId, $userType);

        $challenge   = base64_encode(random_bytes(32));
        $userHandle  = base64_encode(random_bytes(16));

        $request->session()->put('webauthn_reg_challenge', $challenge);
        $request->session()->put('webauthn_user_handle', $userHandle);
        $request->session()->put('webauthn_user_id', $userId);
        $request->session()->put('webauthn_user_type', $userType);

        return response()->json([
            'challenge'          => $challenge,
            'rp'                 => ['name' => config('app.name'), 'id' => parse_url(config('app.url'), PHP_URL_HOST)],
            'user'               => ['id' => $userHandle, 'name' => $userName, 'displayName' => $userDisplayName],
            'pubKeyCredParams'   => [
                ['alg' => -7,   'type' => 'public-key'],   // ES256
                ['alg' => -257, 'type' => 'public-key'],   // RS256
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'residentKey'             => 'preferred',
                'userVerification'        => 'preferred',
            ],
            'timeout'   => 60000,
            'attestation' => 'none',
        ]);
    }

    /**
     * حفظ Passkey بعد نجاح التسجيل.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'id'          => 'required|string',
            'rawId'       => 'required|string',
            'response'    => 'required|array',
            'device_name' => 'nullable|string|max:100',
        ]);

        $userId   = $request->session()->get('webauthn_user_id');
        $userType = $request->session()->get('webauthn_user_type');

        if (! $userId) {
            return response()->json(['error' => 'الجلسة منتهية'], 401);
        }

        $challenge = (string) $request->session()->get('webauthn_reg_challenge', '');
        if ($challenge === '') {
            return response()->json(['error' => 'الجلسة منتهية'], 401);
        }

        $clientDataRaw = $this->decodeBase64UrlToRaw((string) $request->input('response.clientDataJSON', ''));
        if (! $clientDataRaw) {
            return response()->json(['error' => 'بيانات WebAuthn غير صالحة'], 401);
        }

        $clientData = json_decode($clientDataRaw, true);
        if (! is_array($clientData)) {
            return response()->json(['error' => 'بيانات WebAuthn غير صالحة'], 401);
        }

        if (($clientData['type'] ?? null) !== 'webauthn.create') {
            return response()->json(['error' => 'نوع طلب WebAuthn غير صالح'], 401);
        }

        if (! $this->verifyChallenge((string) ($clientData['challenge'] ?? ''), $challenge)) {
            return response()->json(['error' => 'Challenge غير صالح'], 401);
        }

        if (! $this->isValidOrigin((string) ($clientData['origin'] ?? ''), $request)) {
            return response()->json(['error' => 'Origin غير صالح'], 401);
        }

        $credentialId = $request->input('id');
        $userHandle   = $request->session()->get('webauthn_user_handle', Str::random(20));

        // التحقق من عدم التكرار
        if (Passkey::where('credential_id', $credentialId)->exists()) {
            return response()->json(['error' => 'هذا الجهاز مسجل مسبقاً'], 409);
        }

        // نحفظ public key (clientDataJSON + attestationObject) كـ JSON
        $publicKey = json_encode([
            'clientDataJSON'   => $request->input('response.clientDataJSON', ''),
            'attestationObject'=> $request->input('response.attestationObject', ''),
        ]);

        Passkey::create([
            'user_id'       => $userId,
            'user_type'     => $userType,
            'credential_id' => $credentialId,
            'public_key'    => $publicKey,
            'user_handle'   => $userHandle,
            'device_name'   => $request->input('device_name', 'الجهاز'),
            'sign_count'    => 0,
        ]);

        $request->session()->forget(['webauthn_reg_challenge', 'webauthn_user_handle']);

        return response()->json(['success' => true, 'message' => 'تم تسجيل البصمة بنجاح ✅']);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  Authentication
    // ────────────────────────────────────────────────────────────────────────

    /**
     * إنشاء challenge لمصادقة بصمة.
     * يقبل email لتحديد الجهاز.
     */
    public function authOptions(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'user_type' => 'required|in:user,supplier',
            'context' => 'nullable|string|in:customer,admin',
        ]);

        $email    = $request->input('email');
        $userType = $request->input('user_type');
        $context  = $request->input('context', 'customer');

        $sig = $request->session()->get('biometric_precheck_sig');
        $at = (int) $request->session()->get('biometric_precheck_at', 0);
        $expectedSig = hash_hmac('sha256', strtolower($email) . '|' . $userType . '|' . $context, (string) config('app.key'));
        if (! $sig || ! hash_equals($expectedSig, (string) $sig) || $at < (time() - 300)) {
            return response()->json(['error' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور أولاً.'], 403);
        }

        $userId = $this->findUserByEmail($email, $userType);

        if (! $userId) {
            return response()->json(['error' => 'البريد الإلكتروني غير مسجل'], 404);
        }

        if ($userType === 'user') {
            $u = User::find($userId);
            if (! $u) {
                return response()->json(['error' => 'البريد الإلكتروني غير مسجل'], 404);
            }
            if ($context === 'admin') {
                if (! $u->isAdmin()) {
                    return response()->json(['error' => 'هذه الصفحة مخصصة للإدارة فقط.'], 403);
                }
            } else {
                if ($u->isAdmin()) {
                    return response()->json(['error' => 'تسجيل الدخول من هذه الصفحة مخصص للعملاء فقط.'], 403);
                }
                if (Supplier::where('email', $email)->exists()) {
                    return response()->json(['error' => 'هذا البريد مرتبط بحساب مورد. يرجى استخدام صفحة دخول المورد.'], 403);
                }
            }
        }

        $keys = Passkey::where('user_id', $userId)
            ->where('user_type', $userType)
            ->select('credential_id')
            ->get();

        if ($keys->isEmpty()) {
            return response()->json(['error' => 'لا توجد بصمة مسجلة لهذا الحساب'], 404);
        }

        $challenge = base64_encode(random_bytes(32));
        $request->session()->put('webauthn_auth_challenge', $challenge);
        $request->session()->put('webauthn_auth_user_id', $userId);
        $request->session()->put('webauthn_auth_user_type', $userType);
        $request->session()->put('webauthn_auth_context', $context);

        return response()->json([
            'challenge'        => $challenge,
            'rpId'             => parse_url(config('app.url'), PHP_URL_HOST),
            'allowCredentials' => $keys->map(fn($k) => ['type' => 'public-key', 'id' => $k->credential_id])->values(),
            'userVerification' => 'preferred',
            'timeout'          => 60000,
        ]);
    }

    /**
     * التحقق من استجابة البصمة وتسجيل الدخول.
     */
    public function authenticate(Request $request): JsonResponse
    {
        $request->validate([
            'id'       => 'required|string',
            'response' => 'required|array',
            'context'  => 'nullable|string|in:customer,admin',
        ]);

        $credentialId = $request->input('id');
        $userId       = $request->session()->get('webauthn_auth_user_id');
        $userType     = $request->session()->get('webauthn_auth_user_type');
        $challenge    = $request->session()->get('webauthn_auth_challenge');
        $context      = $request->input('context') ?: $request->session()->get('webauthn_auth_context', 'customer');

        if (! $challenge || ! $userId) {
            return response()->json(['error' => 'الجلسة منتهية، أعد المحاولة'], 401);
        }

        $passkey = Passkey::where('credential_id', $credentialId)
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->first();

        if (! $passkey) {
            return response()->json(['error' => 'البصمة غير معروفة'], 401);
        }

        $clientDataRaw = $this->decodeBase64UrlToRaw((string) $request->input('response.clientDataJSON', ''));
        if (! $clientDataRaw) {
            return response()->json(['error' => 'بيانات WebAuthn غير صالحة'], 401);
        }

        $clientData = json_decode($clientDataRaw, true);
        if (! is_array($clientData)) {
            return response()->json(['error' => 'بيانات WebAuthn غير صالحة'], 401);
        }

        if (($clientData['type'] ?? null) !== 'webauthn.get') {
            return response()->json(['error' => 'نوع طلب WebAuthn غير صالح'], 401);
        }

        if (! $this->verifyChallenge((string) ($clientData['challenge'] ?? ''), $challenge)) {
            return response()->json(['error' => 'Challenge غير صالح'], 401);
        }

        if (! $this->isValidOrigin((string) ($clientData['origin'] ?? ''), $request)) {
            return response()->json(['error' => 'Origin غير صالح'], 401);
        }

        $authenticatorDataRaw = $this->decodeBase64UrlToRaw((string) $request->input('response.authenticatorData', ''));
        if (! $authenticatorDataRaw || strlen($authenticatorDataRaw) < 37) {
            return response()->json(['error' => 'بيانات Authenticator غير صالحة'], 401);
        }

        if (! $this->isValidRpIdHash($authenticatorDataRaw, (string) parse_url(config('app.url'), PHP_URL_HOST))) {
            return response()->json(['error' => 'RP ID غير صالح'], 401);
        }

        $flags = ord($authenticatorDataRaw[32]);
        if (($flags & 0x01) === 0) {
            return response()->json(['error' => 'لم يتم تأكيد حضور المستخدم (UP)'], 401);
        }

        if ($userType === 'user') {
            $u = User::find((int) $userId);
            if (! $u) {
                $request->session()->forget(['webauthn_auth_challenge', 'webauthn_auth_user_id', 'webauthn_auth_user_type', 'webauthn_auth_context']);
                return response()->json(['error' => 'البريد الإلكتروني غير مسجل'], 404);
            }
            if ($context === 'admin') {
                if (! $u->isAdmin()) {
                    $request->session()->forget(['webauthn_auth_challenge', 'webauthn_auth_user_id', 'webauthn_auth_user_type', 'webauthn_auth_context']);
                    return response()->json(['error' => 'هذه الصفحة مخصصة للإدارة فقط.'], 403);
                }
            } else {
                if ($u->isAdmin()) {
                    $request->session()->forget(['webauthn_auth_challenge', 'webauthn_auth_user_id', 'webauthn_auth_user_type', 'webauthn_auth_context']);
                    return response()->json(['error' => 'تسجيل الدخول من هذه الصفحة مخصص للعملاء فقط.'], 403);
                }
                if (Supplier::where('email', $u->email)->exists()) {
                    $request->session()->forget(['webauthn_auth_challenge', 'webauthn_auth_user_id', 'webauthn_auth_user_type', 'webauthn_auth_context']);
                    return response()->json(['error' => 'هذا البريد مرتبط بحساب مورد. يرجى استخدام صفحة دخول المورد.'], 403);
                }
            }
        }

        // تحديث عداد الاستخدام
        $passkey->update([
            'sign_count'   => $passkey->sign_count + 1,
            'last_used_at' => now(),
        ]);

        // تسجيل الدخول
        $redirect = $this->loginUser($userId, $userType, $request);

        $request->session()->forget(['webauthn_auth_challenge', 'webauthn_auth_user_id', 'webauthn_auth_user_type', 'webauthn_auth_context']);

        return response()->json(['success' => true, 'redirect' => $redirect]);
    }

    /**
     * حذف passkey.
     */
    public function deletePasskey(Request $request, int $id): JsonResponse
    {
        ['userId' => $userId, 'userType' => $userType] = $this->resolveUser($request);

        $passkey = Passkey::where('id', $id)
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->firstOrFail();

        $passkey->delete();

        return response()->json(['success' => true]);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ────────────────────────────────────────────────────────────────────────

    private function resolveUser(Request $request): array
    {
        if (Auth::check()) {
            return ['userId' => Auth::id(), 'userType' => 'user'];
        }
        if (Auth::guard('supplier')->check()) {
            return ['userId' => Auth::guard('supplier')->id(), 'userType' => 'supplier'];
        }
        // بعد OTP verify، الـ userId يكون في session
        if ($request->session()->has('biometric_register_user_id')) {
            return [
                'userId'   => $request->session()->get('biometric_register_user_id'),
                'userType' => $request->session()->get('biometric_register_user_type', 'user'),
            ];
        }

        return ['userId' => null, 'userType' => null];
    }

    private function getUserInfo(int $userId, string $userType): array
    {
        if ($userType === 'supplier') {
            $s = Supplier::find($userId);
            return [$s?->email ?? 'supplier', $s?->name ?? 'مورد'];
        }
        $u = User::find($userId);
        return [$u?->email ?? 'user', $u?->name ?? 'عميل'];
    }

    private function findUserByEmail(string $email, string $userType): ?int
    {
        if ($userType === 'supplier') {
            return Supplier::where('email', $email)->value('id');
        }
        return User::where('email', $email)->value('id');
    }

    private function loginUser(int $userId, string $userType, Request $request): string
    {
        $request->session()->regenerate();

        if ($userType === 'supplier') {
            $supplier = Supplier::find($userId);
            if (! $supplier) {
                return route('supplier.login');
            }

            if ($supplier->status !== 'approved' || ! $supplier->email_verified_at) {
                return route('supplier.login');
            }

            try {
                $supplier->forceFill(['last_login_at' => now()])->save();
            } catch (\Throwable $e) {
            }

            $newSupplierSessionVersion = ((int) ($supplier->session_version ?: 1)) + 1;
            $supplier->forceFill([
                'session_version' => $newSupplierSessionVersion,
                'remember_token' => Str::random(60),
            ])->save();
            $request->session()->put('supplier_session_version', $newSupplierSessionVersion);

            Auth::guard('supplier')->login($supplier, true);
            return route('supplier.dashboard');
        }

        $context = $request->session()->get('webauthn_auth_context', 'customer');
        $user = User::find($userId);
        if (! $user) {
            Auth::logout();
            return route('login');
        }

        if ($user->isAdmin()) {
            if ($context !== 'admin') {
                Auth::logout();
                return route('login');
            }
            Auth::login($user, true);

            $newSessionVersion = ((int) ($user->session_version ?: 1)) + 1;
            $user->forceFill([
                'session_version' => $newSessionVersion,
                'remember_token' => Str::random(60),
            ])->save();
            $request->session()->put('user_session_version', $newSessionVersion);

            try {
                $user->forceFill(['last_login_at' => now()])->save();
            } catch (\Throwable $e) {
            }

            return route('admin.dashboard');
        }

        if (Supplier::where('email', $user->email)->exists()) {
            Auth::logout();
            return route('login');
        }

        Auth::login($user, true);

        $newSessionVersion = ((int) ($user->session_version ?: 1)) + 1;
        $user->forceFill([
            'session_version' => $newSessionVersion,
            'remember_token' => Str::random(60),
        ])->save();
        $request->session()->put('user_session_version', $newSessionVersion);

        try {
            $user->forceFill(['last_login_at' => now()])->save();
        } catch (\Throwable $e) {
        }

        return route('home');
    }

    private function decodeBase64UrlToRaw(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        $normalized = strtr($value, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        return $decoded === false ? null : $decoded;
    }

    private function verifyChallenge(string $receivedChallenge, string $storedChallenge): bool
    {
        $receivedRaw = $this->decodeBase64UrlToRaw($receivedChallenge);
        $storedRaw = $this->decodeBase64UrlToRaw($storedChallenge);

        if (! $receivedRaw || ! $storedRaw) {
            return false;
        }

        return hash_equals($storedRaw, $receivedRaw);
    }

    private function isValidOrigin(string $origin, Request $request): bool
    {
        if ($origin === '') {
            return false;
        }

        $originHost = parse_url($origin, PHP_URL_HOST);
        $originScheme = parse_url($origin, PHP_URL_SCHEME);
        if (! $originHost || ! $originScheme) {
            return false;
        }

        $appUrl = (string) config('app.url');
        $appHost = parse_url($appUrl, PHP_URL_HOST);
        $appScheme = parse_url($appUrl, PHP_URL_SCHEME);

        $requestHost = $request->getHost();
        $requestScheme = $request->getScheme();

        $allowedOrigins = array_filter([
            $appHost && $appScheme ? strtolower($appScheme.'://'.$appHost) : null,
            $requestHost && $requestScheme ? strtolower($requestScheme.'://'.$requestHost) : null,
        ]);

        return in_array(strtolower($originScheme.'://'.$originHost), $allowedOrigins, true);
    }

    private function isValidRpIdHash(string $authenticatorDataRaw, string $rpId): bool
    {
        if ($rpId === '' || strlen($authenticatorDataRaw) < 32) {
            return false;
        }

        $receivedRpIdHash = substr($authenticatorDataRaw, 0, 32);
        $expectedRpIdHash = hash('sha256', $rpId, true);

        return hash_equals($expectedRpIdHash, $receivedRpIdHash);
    }
}
