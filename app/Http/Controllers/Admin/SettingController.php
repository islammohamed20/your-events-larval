<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->input('settings', []) as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                continue;
            }

            // Handle file uploads
            if ($request->hasFile("settings.{$key}")) {
                // Delete old file if exists
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                
                $file = $request->file("settings.{$key}");
                $path = $file->store('settings', 'public');
                $setting->value = $path;
                $setting->save();
            } else {
                // Regular text value
                $setting->value = $value;
                $setting->save();
            }
        }

        // Clear settings cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
                       ->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
