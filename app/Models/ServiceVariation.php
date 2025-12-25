<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'sku',
        'attributes',
        'attribute_value_ids',
        'price',
        'sale_price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
        'attribute_value_ids' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the service that owns this variation
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the attribute values for this variation
     * Note: This is not a real relation, just returns query builder
     */
    public function attributeValues()
    {
        if (empty($this->attribute_value_ids) || ! is_array($this->attribute_value_ids)) {
            // Return empty query builder
            return AttributeValue::query()->whereRaw('1 = 0');
        }

        return AttributeValue::query()->whereIn('id', $this->attribute_value_ids);
    }

    /**
     * Get attribute values as collection (helper method)
     */
    public function getAttributeValuesListAttribute()
    {
        if (empty($this->attribute_value_ids) || ! is_array($this->attribute_value_ids)) {
            return collect([]);
        }

        return AttributeValue::with('attribute')
            ->whereIn('id', $this->attribute_value_ids)
            ->get();
    }

    /**
     * Get the active price (sale price if available, otherwise regular price)
     */
    public function getActivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if this variation is on sale
     */
    public function getOnSaleAttribute()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Get formatted attributes for display
     */
    public function getFormattedAttributesAttribute()
    {
        // Prefer attribute_value_ids when available
        if (! empty($this->attribute_value_ids) && is_array($this->attribute_value_ids)) {
            $formatted = [];
            foreach ($this->attribute_value_ids as $valId) {
                $val = \App\Models\AttributeValue::find($valId);
                if ($val && $val->attribute) {
                    $formatted[$val->attribute->name] = $val->value;
                }
            }

            return $formatted;
        }

        // Fallback to legacy attributes {slug => valueSlug}
        if (! $this->attributes) {
            return [];
        }

        $formatted = [];
        foreach ($this->attributes as $key => $value) {
            $attribute = \App\Models\Attribute::where('slug', $key)->first();
            if ($attribute) {
                $attributeValue = \App\Models\AttributeValue::where('attribute_id', $attribute->id)
                    ->where('slug', $value)
                    ->first();
                if ($attributeValue) {
                    $formatted[$attribute->name] = $attributeValue->value;
                }
            }
        }

        return $formatted;
    }

    /**
     * Scope for active variations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to find variation by attribute combination
     */
    public function scopeByAttributes($query, array $attributes)
    {
        return $query->where('attributes', json_encode($attributes));
    }
}
