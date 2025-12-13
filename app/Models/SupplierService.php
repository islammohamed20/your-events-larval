<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierService extends Model
{
    protected $fillable = [
        'supplier_id',
        'category_id',
        'service_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
