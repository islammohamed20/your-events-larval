<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Login;

class LogLoginActivity
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            $request = request();
            LoginActivity::create([
                'user_id' => $event->user->id,
                'ip_address' => $request->ip(),
                'country' => null, // could be enhanced with GeoIP later
                'successful' => true,
                'method' => 'password',
            ]);
        } catch (\Throwable $e) {
            // swallow errors; logging should not break auth flow
        }
    }
}
