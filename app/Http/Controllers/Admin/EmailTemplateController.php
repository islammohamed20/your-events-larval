<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates
     */
    public function index()
    {
        $templates = EmailTemplate::orderBy('type')->orderBy('name')->get();
        return view('admin.email-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        return view('admin.email-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:booking,welcome,reset_password,invoice,custom',
            'description' => 'nullable|string',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'تم إنشاء قالب البريد الإلكتروني بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        $variables = $emailTemplate->getDefaultVariables();
        $sampleData = [];
        
        foreach ($variables as $key => $label) {
            $sampleData[$key] = $label;
        }

        $rendered = $emailTemplate->render($sampleData);

        return view('admin.email-templates.preview', [
            'template' => $emailTemplate,
            'rendered' => $rendered,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug,' . $emailTemplate->id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:booking,welcome,reset_password,invoice,custom',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'تم تحديث قالب البريد الإلكتروني بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'تم حذف قالب البريد الإلكتروني بنجاح');
    }

    /**
     * Toggle template active status
     */
    public function toggleActive(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $emailTemplate->is_active,
            'message' => $emailTemplate->is_active ? 'تم تفعيل القالب' : 'تم تعطيل القالب'
        ]);
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'to_email' => 'required|email',
        ]);

        $variables = $emailTemplate->getDefaultVariables();
        $sampleData = [];
        
        foreach ($variables as $key => $label) {
            $sampleData[$key] = $label;
        }

        $rendered = $emailTemplate->render($sampleData);

        try {
            Mail::send([], [], function ($message) use ($request, $rendered) {
                $message->to($request->to_email)
                    ->subject($rendered['subject'])
                    ->html($rendered['body']);
            });

            return back()->with('success', 'تم إرسال البريد التجريبي بنجاح! ✅');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إرسال البريد: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate template
     */
    public function duplicate(EmailTemplate $emailTemplate)
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name . ' (نسخة)';
        $newTemplate->slug = $emailTemplate->slug . '-copy-' . time();
        $newTemplate->is_active = false;
        $newTemplate->save();

        return redirect()->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'تم نسخ القالب بنجاح');
    }
}
