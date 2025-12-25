<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('contact_messages')->orderByDesc('created_at');
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        $messages = $query->paginate(15);

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show($id)
    {
        $message = DB::table('contact_messages')->find($id);
        abort_unless($message, 404);

        return view('admin.contact-messages.show', compact('message'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,closed',
        ]);

        DB::table('contact_messages')->where('id', $id)->update([
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.contact-messages.show', $id)
            ->with('success', 'تم تحديث حالة الرسالة بنجاح');
    }

    public function destroy($id)
    {
        DB::table('contact_messages')->where('id', $id)->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }
}
