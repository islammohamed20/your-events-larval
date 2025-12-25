<?php

use App\Models\Supplier;
use App\Models\SupplierService;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Suppliers and Services...\n";

$suppliers = Supplier::with('services')->take(5)->get();

foreach ($suppliers as $supplier) {
    echo "Supplier ID: {$supplier->id}, Name: {$supplier->name}\n";
    echo 'Services Count (via relation): '.$supplier->services->count()."\n";

    // Check raw query
    $servicesCount = SupplierService::where('supplier_id', $supplier->id)->count();
    echo 'Services Count (raw query on supplier_id): '.$servicesCount."\n";

    echo 'Services Offered (JSON): '.json_encode($supplier->services_offered)."\n";

    if ($supplier->services->count() > 0) {
        foreach ($supplier->services as $service) {
            echo "  - Service ID: {$service->id}, Service Name: ".($service->service ? $service->service->name : 'N/A')."\n";
        }
    }
    echo "--------------------------------\n";
}

echo 'Total SupplierService records: '.SupplierService::count()."\n";

echo 'First 3 User IDs: '.implode(', ', User::pluck('id')->take(3)->toArray())."\n";
echo 'First 3 Supplier IDs: '.implode(', ', Supplier::pluck('id')->take(3)->toArray())."\n";

// Check a random SupplierService
$service = SupplierService::first();
if ($service) {
    echo "Sample Service ID: {$service->id}, supplier_id: {$service->supplier_id}\n";
    $supplier = Supplier::find($service->supplier_id);

    echo "Supplier with ID {$service->supplier_id}: ".($supplier ? 'Found' : 'Not Found')."\n";
}
