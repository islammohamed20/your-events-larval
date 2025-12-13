<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\ServiceVariation;
use App\Models\Category;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

// Artisan command to set price by service "type" field or by category name
Artisan::command('services:set-price-by-type {type} {value=1} {--dry-run}', function (string $type, string $value) {
    $this->info("Preparing to set price for services of type/category: {$type}");

    $price = is_numeric($value) ? (float) $value : 1.0;

    // Normalize input (support Arabic or English; case-insensitive for English)
    $typeNormalized = trim($type);

    // Strategy 1: Match by Service.type column (if populated)
    $servicesByType = \App\Models\Service::query()
        ->where('type', $typeNormalized)
        ->get();

    // Strategy 2: If none, fallback to category matching (Arabic/English)
    $category = null;
    if ($servicesByType->count() === 0) {
        $category = Category::where('name', $typeNormalized)
            ->orWhere('name_en', $typeNormalized)
            ->first();
    }

    $services = $servicesByType;
    if ($services->count() === 0 && $category) {
        $services = \App\Models\Service::where('category_id', $category->id)->get();
    }

    $count = $services->count();
    if ($count === 0) {
        $this->warn('No services found matching the given type or category.');
        return 0;
    }

    $this->info("Found {$count} services to update.");

    if ($this->option('dry-run')) {
        foreach ($services as $service) {
            $this->line("- [ID {$service->id}] {$service->name} (current price: " . ($service->isVariable() ? $service->min_price . '-' . $service->max_price : $service->price) . ")");
        }
        $this->info('Dry-run complete. No updates performed.');
        return 0;
    }

    $servicesUpdated = 0;
    $variationsUpdated = 0;

    DB::transaction(function () use ($services, $price, &$servicesUpdated, &$variationsUpdated) {
        foreach ($services as $service) {
            if ($service->isVariable()) {
                $affected = \App\Models\ServiceVariation::where('service_id', $service->id)->update([
                    'price' => $price,
                    'sale_price' => $price,
                ]);
                $variationsUpdated += $affected;
                // Optionally set service base price for consistency
                $service->price = $price;
                $service->save();
                $servicesUpdated++;
            } else {
                $service->price = $price;
                $service->save();
                $servicesUpdated++;
            }
        }
    });

    $this->info("Updated services: {$servicesUpdated}");
    $this->info("Updated variations: {$variationsUpdated}");
    $this->info('Done.');
    return 0;
})->purpose('Set price for services by type or category name');

// Artisan command to delete services by category name (Arabic or English)
Artisan::command('services:delete-by-category {name} {--dry-run}', function (string $name) {
    $this->info("Preparing to delete services in category: {$name}");

    // Find category by Arabic or English name
    $category = Category::where('name', $name)
        ->orWhere('name_en', $name)
        ->first();

    if (!$category) {
        $this->error('Category not found by name or name_en.');
        return 1;
    }

    // Fetch services for this category
    $services = Service::where('category_id', $category->id)->get();
    $count = $services->count();

    if ($count === 0) {
        $this->info('No services found for the given category.');
        return 0;
    }

    $this->info("Found {$count} services in category '{$category->name}' (ID: {$category->id}).");

    $dryRun = $this->option('dry-run');
    if ($dryRun) {
        // List service IDs and names without deleting
        foreach ($services as $service) {
            $this->line("- [ID {$service->id}] {$service->name}");
        }
        $this->info('Dry-run complete. No deletions were performed.');
        return 0;
    }

    // Perform deletions in a transaction for safety
    DB::transaction(function () use ($services) {
        foreach ($services as $service) {
            // Delete main image file if present
            if ($service->image) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($service->image);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to delete service image: ' . $service->image . ' error: ' . $e->getMessage());
                }
            }
            // Delete the service; related images are handled in model events
            $service->delete();
        }
    });

    $this->info("Deleted {$count} services successfully.");
    return 0;
})->purpose('Delete all services belonging to a category by name');

// Artisan command to convert a user to supplier (quick utility)
Artisan::command('user:convert-to-supplier {email} {--force-password=}', function (string $email) {
    $this->info("Converting user {$email} to supplier...");

    $user = User::where('email', $email)->first();
    if (!$user) {
        $this->error('User not found.');
        return 1;
    }

    $existingSupplier = Supplier::where('email', $email)->first();

    $passwordHash = request()->has('force-password') && request()->get('force-password')
        ? Hash::make(request()->get('force-password'))
        : $user->password;

    if ($existingSupplier) {
        $existingSupplier->update([
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $supplier = $existingSupplier;
        $this->warn('Supplier already exists. Updated status and verification.');
    } else {
        $supplier = Supplier::create([
            'supplier_type' => 'company',
            'name' => $user->company_name ?: $user->name,
            'email' => $user->email,
            'password' => $passwordHash,
            'primary_phone' => $user->phone ?: 'N/A',
            'headquarters_city' => 'غير محدد',
            'tax_number' => $user->tax_number,
            'status' => 'approved',
            'email_verified_at' => now(),
            'terms_accepted' => true,
            'privacy_accepted' => true,
        ]);
    }

    $user->forceFill([
        'role' => 'user',
        'is_admin' => false,
    ])->save();

    $this->info('Done. Supplier ID: ' . $supplier->id);
    return 0;
})->purpose('Convert existing user to supplier and revoke admin privileges');
