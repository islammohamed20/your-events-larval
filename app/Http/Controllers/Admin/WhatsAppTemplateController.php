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

                $languageCode = strtolower((string) ($ft['language'] ?? $ft['lang'] ?? $ft['locale'] ?? 'ar'));
                $namespace = (string) ($ft['namespace'] ?? $ft['template_namespace'] ?? '');

                // 1. Try to get param labels from Meta's example
                $paramsSchema = [];
                foreach (($ft['components'] ?? []) as $comp) {
                    if (($comp['type'] ?? '') === 'BODY' && !empty($comp['example']['body_text'][0]) && is_array($comp['example']['body_text'][0])) {
                        $paramsSchema = array_values($comp['example']['body_text'][0]);
                        break;
                    }
                }

                // 2. Fallback: count {{1}}, {{2}} … placeholders in BODY text and build generic labels
                if (empty($paramsSchema) && preg_match_all('/{{\s*(\d+)\s*}}/', $content, $matches)) {
                    $maxIndex = max(array_map('intval', $matches[1]));
                    for ($i = 1; $i <= $maxIndex; $i++) {
                        $paramsSchema[] = 'المتغير ' . $i;
                    }
                }

                MessageTemplate::updateOrCreate(
                    ['name' => $name],
                    [
                        'content' => $content,
                        'type' => $type,
                        'faalwa_namespace' => $namespace !== '' ? $namespace : null,
                        'language_code' => $languageCode !== '' ? $languageCode : 'ar',
                        'params_schema' => $paramsSchema ?: null,
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
            'faalwa_namespace' => 'nullable|string|max:255',
            'language_code' => 'nullable|string|max:10',
            'params_schema' => 'nullable|array',
            'params_schema.*' => 'nullable|string|max:255',
        ]);

        $validated['language_code'] = ($validated['language_code'] ?? null) ?: 'ar';

        // Auto-extract params_schema from {{1}}, {{2}} … placeholders if not provided
        $content = $validated['content'] ?? '';
        if (empty($validated['params_schema']) && preg_match_all('/{{\s*(\d+)\s*}}/', $content, $matches)) {
            $maxIndex = max(array_map('intval', $matches[1]));
            $extracted = [];
            for ($i = 1; $i <= $maxIndex; $i++) {
                $extracted[] = 'المتغير ' . $i;
            }
            $validated['params_schema'] = $extracted ?: null;
        }

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
            'faalwa_namespace' => 'nullable|string|max:255',
            'language_code' => 'nullable|string|max:10',
            'params_schema' => 'nullable|array',
            'params_schema.*' => 'nullable|string|max:255',
        ]);

        $validated['language_code'] = ($validated['language_code'] ?? null) ?: 'ar';

        // Auto-extract params_schema from {{1}}, {{2}} … placeholders if not provided
        $content = $validated['content'] ?? '';
        if (empty($validated['params_schema']) && preg_match_all('/{{\s*(\d+)\s*}}/', $content, $matches)) {
            $maxIndex = max(array_map('intval', $matches[1]));
            $extracted = [];
            for ($i = 1; $i <= $maxIndex; $i++) {
                $extracted[] = 'المتغير ' . $i;
            }
            $validated['params_schema'] = $extracted ?: null;
        }

        $template->update($validated);

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم تحديث القالب بنجاح.');
    }

    public function destroy(MessageTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم حذف القالب بنجاح.');
    }
}