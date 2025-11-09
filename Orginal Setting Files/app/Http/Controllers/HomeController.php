<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Service;
use App\Models\Gallery;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $packages = Package::active()->take(3)->get();
        $services = Service::active()->take(6)->get();
        $gallery = Gallery::featured()->take(8)->get();
        $reviews = Review::approved()->take(3)->get();

        return view('welcome', compact('packages', 'services', 'gallery', 'reviews'));
    }
}
