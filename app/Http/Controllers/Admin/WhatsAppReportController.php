<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaalwaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppReportController extends Controller
{
    public function index(Request $request, FaalwaService $faalwa): View
    {
        $range = $request->string('range', 'last_30_days')->toString();
        $validRanges = ['yesterday', 'last_7_days', 'last_week', 'last_30_days', 'last_month', 'last_3_months'];

        if (!in_array($range, $validRanges)) {
            $range = 'last_30_days';
        }

        $response = $faalwa->getAnalyticsSummary($range);
        $summary = $response['raw']['data'] ?? [];

        return view('admin.whatsapp.reports.index', compact('summary', 'range', 'validRanges'));
    }
}
