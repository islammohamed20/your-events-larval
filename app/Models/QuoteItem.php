<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'service_id',
        'service_name',
        'service_description',
        'price',
        'quantity',
        'subtotal',
        'customer_notes',
        'selections',
        'booking_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
        'selections' => 'array',
        'booking_date' => 'date',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->price * $item->quantity;
        });
    }

    /**
     * Relationships
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get variation from selections dynamically
     */
    public function getVariation()
    {
        // إذا كان هناك variation_id في selections
        if (is_array($this->selections) && isset($this->selections['_variation_id'])) {
            // 'attributeValuesList' هو Accessor وليس علاقة فعلية؛ لا يمكن استخدام eager loading هنا
            // نعيد الـ Variation فقط، والواجهة ستستخدم accessor attributeValuesList لتحميل القيم مع العلاقة attribute
            return ServiceVariation::find($this->selections['_variation_id']);
        }

        return null;
    }

    /**
     * Get selected variation ID from selections
     */
    public function getSelectedVariationId()
    {
        // من selections أولاً
        if (is_array($this->selections) && isset($this->selections['_variation_id'])) {
            return $this->selections['_variation_id'];
        }

        return null;
    }
}
