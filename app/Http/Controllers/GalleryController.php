<?php

namespace App\Http\Controllers;

use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Gallery::images()->get();
        $videos = Gallery::videos()->get();

        return view('gallery.index', compact('images', 'videos'));
    }
}
