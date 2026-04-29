<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaalwaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppCustomerController extends Controller
{
    public function index(Request $request, FaalwaService $faalwa): View
    {
        $page = $request->integer('page', 1);
        $search = $request->string('search')->trim()->toString();

        $params = [
            'page' => $page,
            'limit' => 20,
        ];

        if ($search !== '') {
            $params['name'] = $search; // Faalwa supports name, phone, email etc. We'll pass it to name for simplicity, or we can let Faalwa handle it.
        }

        $response = $faalwa->getSubscribers($params);
        $customers = $response['raw']['data'] ?? [];
        $pagination = $response['raw']['pagination'] ?? ['current_page' => 1, 'total_pages' => 1];

        return view('admin.whatsapp.customers.index', compact('customers', 'pagination', 'search'));
    }
}
