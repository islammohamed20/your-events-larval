<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailTestController extends Controller
{
    /**
     * Display the email test page
     */
    public function index()
    {
        return view('admin.email-test');
    }

    /**
     * Send a test email
     */
    public function send(Request $request)
    {
        $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::raw($request->message, function ($mail) use ($request) {
                $mail->to($request->to_email)
                    ->subject($request->subject);
            });

            return back()->with('success', 'تم إرسال البريد الإلكتروني بنجاح! ✅');
        } catch (Exception $e) {
            return back()->with('error', 'فشل إرسال البريد: '.$e->getMessage());
        }
    }

    /**
     * Get current mail configuration
     */
    public function config()
    {
        $config = [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        return response()->json($config);
    }
}
