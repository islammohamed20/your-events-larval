<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class WhatsAppTemplateController extends Controller
{
    public function index()
    {
        $templates = MessageTemplate::orderBy('type')->orderBy('name')->paginate(20);

        return view('admin.whatsapp.templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:message_templates,name',
            'content' => 'required|string',
            'type' => 'required|in:marketing,utility,authentication',
        ]);

        MessageTemplate::create($validated);

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم إنشاء القالب بنجاح.');
    }

    public function edit(MessageTemplate $template)
    {
        return view('admin.whatsapp.templates.edit', compact('template'));
    }

    public function update(Request $request, MessageTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:message_templates,name,'.$template->id,
            'content' => 'required|string',
            'type' => 'required|in:marketing,utility,authentication',
        ]);

        $template->update($validated);

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم تحديث القالب بنجاح.');
    }

    public function destroy(MessageTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم حذف القالب بنجاح.');
    }
}