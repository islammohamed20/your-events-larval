<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $gallery = Gallery::ordered()->get();
        return view('admin.gallery.index', compact('gallery'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mov,webm|max:51200', // 50MB
            'category' => 'nullable|string|in:events,vr_experiences,behind_scenes,client_moments,equipment,team,other',
            'is_featured' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('gallery', 'public');
            
            // Determine file type
            $mimeType = $file->getMimeType();
            $type = Str::startsWith($mimeType, 'image/') ? 'image' : 'video';
            
            $galleryData = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'file_path' => $path,
                // legacy column to satisfy NOT NULL constraint if still present
                'path' => $path,
                'type' => $type,
                'category' => $validated['category'],
                'is_featured' => $request->has('is_featured'),
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
                'alt_text' => $validated['title'] ?: 'صورة من معرض Your Events'
            ];

            Gallery::create($galleryData);
        }

        return redirect()->route('admin.gallery.index')
                         ->with('success', 'تم إضافة العنصر إلى المعرض بنجاح!');
    }

    public function destroy(Gallery $gallery)
    {
        // File will be deleted automatically via model boot method
        $gallery->delete();

        return redirect()->route('admin.gallery.index')
                         ->with('success', 'تم حذف العنصر من المعرض بنجاح!');
    }

    public function toggleFeatured(Gallery $gallery)
    {
        $gallery->toggleFeatured();
        
        $message = $gallery->is_featured ? 'تم تمييز العنصر بنجاح!' : 'تم إلغاء تمييز العنصر!';

        return redirect()->back()
                         ->with('success', $message);
    }
}
