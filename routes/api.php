<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Controllers\Api\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Get services by category
Route::get('/services', function (Request $request) {
    $categoryId = $request->query('category_id');
    
    if (!$categoryId) {
        return response()->json([]);
    }

    $services = Service::where('category_id', $categoryId)
        ->where('is_active', true)
        ->whereNotNull('name')
        ->select('id', 'name', 'subtitle')
        ->get()
        ->filter(function($service) {
            return $service->id && $service->name;
        })
        ->values();

    return response()->json($services);
});

// Order endpoints
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::get('/orders/{id}/accept', [OrderController::class, 'accept']);
