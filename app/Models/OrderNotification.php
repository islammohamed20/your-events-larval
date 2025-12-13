<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNotification extends Model
{
    protected $fillable = [
        'competitive_order_id',
        'supplier_id',
        'notified_at',
        'viewed_at',
        'responded_at',
        'response',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'viewed_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(CompetitiveOrder::class, 'competitive_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function markAsViewed()
    {
        if (!$this->viewed_at) {
            $this->viewed_at = now();
            $this->save();
        }
    }
}
