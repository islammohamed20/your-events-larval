<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginActivityController extends Controller
{
    /**
     * Display login activities list with filters and stats.
     */
    public function index(Request $request)
    {
        $query = LoginActivity::query()->with('user');

        // Filters
        if ($request->filled('email')) {
            $email = $request->get('email');
            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'like', '%' . $email . '%');
            });
        }

        if ($request->filled('name')) {
            $name = $request->get('name');
            $query->whereHas('user', function ($q) use ($name) {
                $q->where('name', 'like', '%' . $name . '%');
            });
        }

        if ($request->filled('method')) {
            $query->where('method', $request->get('method'));
        }

        if ($request->filled('successful')) {
            $val = $request->get('successful');
            if ($val === '1' || $val === '0') {
                $query->where('successful', (bool) ((int) $val));
            }
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->get('ip') . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Pagination
        $activities = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => LoginActivity::count(),
            'today' => LoginActivity::whereDate('created_at', today())->count(),
            'successful' => LoginActivity::where('successful', true)->count(),
            'failed' => LoginActivity::where('successful', false)->count(),
        ];
        $stats['success_rate'] = $stats['total'] > 0
            ? round(($stats['successful'] / $stats['total']) * 100, 2)
            : 0;

        $byMethod = LoginActivity::select('method', DB::raw('count(*) as count'))
            ->groupBy('method')
            ->get()
            ->pluck('count', 'method');

        return view('admin.login-activities.index', compact('activities', 'stats', 'byMethod'));
    }
}

