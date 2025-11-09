<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\ServiceVariation;

// Artisan command to set price for all services and variations
Artisan::command('services:set-price {value=1}', function (string $value) {
    $this->info("Setting price to {$value} for all services and variations...");

    $price = is_numeric($value) ? (float) $value : 1.0;

    $servicesUpdated = 0;
    $variationsUpdated = 0;

    DB::transaction(function () use ($price, &$servicesUpdated, &$variationsUpdated) {
        // Update price for all services (simple/variable)
        $servicesUpdated = Service::query()->update(['price' => $price]);

        // Update price (and sale_price) for all variations to ensure active_price = price
        $variationsUpdated = ServiceVariation::query()->update([
            'price' => $price,
            'sale_price' => $price,
        ]);
    });

    $this->info("Updated services: {$servicesUpdated}");
    $this->info("Updated variations: {$variationsUpdated}");
    $this->info('Done.');
})->purpose('Set price for all services and variations to the given value');