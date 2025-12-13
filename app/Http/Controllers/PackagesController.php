<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    public function index()
    {
        $packages = Package::active()->with('images')->get();
        return view('packages.index', compact('packages'));
    }

    public function show($id)
    {
        $package = Package::with('images')->findOrFail($id);
        return view('packages.show', compact('package'));
    }
}
