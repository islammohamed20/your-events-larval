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

    public function sync(\App\Services\FaalwaService $faalwaService)
    {
        try {
            $response = $faalwaService->getTemplates(200);
            $faalwaTemplates = $response['raw']['data'] ?? [];

            $syncedCount = 0;
            foreach ($faalwaTemplates as $ft) {
                $name = $ft['name'] ?? null;
                if (!$name) continue;

                // Attempt to extract text from template components
                $content = '';
                if (isset($ft['components']) && is_array($ft['components'])) {
                    foreach ($ft['components'] as $comp) {
                        if (($comp['type'] ?? '') === 'BODY') {
                            $content = $comp['text'] ?? '';
                            break;
                        }
                    }
                }

                // If content is still empty, put a placeholder
                if (trim($content) === '') {
                    $content = 'محتوى القالب (تم جلبه من Faalwa)';
                }

                $type = strtolower($ft['category'] ?? 'utility');
                if (!in_array($type, ['marketing', 'utility', 'authentication'])) {
                    $type = 'utility';
                }

                MessageTemplate::updateOrCreate(
                    ['name' => $name],
                    [
                        'content' => $content,
                        'type' => $type,
                    ]
                );
                $syncedCount++;
            }

            return redirect()->route('admin.whatsapp.templates.index')
                ->with('success', "تم جلب وتحديث {$syncedCount} قالب من Faalwa بنجاح.");
        } catch (\Exception $e) {
            return redirect()->route('admin.whatsapp.templates.index')
                ->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
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