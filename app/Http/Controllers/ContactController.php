<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|in:booking,packages,services,complaint,other',
            'message' => 'required|string|min:10',
        ]);

        try {
            DB::table('contact_messages')->insert([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to save contact message', [
                'error' => $e->getMessage(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إرسال رسالتك. حاول مرة أخرى لاحقاً.');
        }

        return back()->with('success', 'تم إرسال رسالتك بنجاح! سنقوم بالرد عليك قريباً.');
    }
}

