<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::active()->get();

        return view('services.index', compact('services'));
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);

        return view('services.show', compact('service'));
    }
}
