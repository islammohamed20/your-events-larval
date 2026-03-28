<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'service_id',
        'quantity',
        'price',
        'customer_notes',
        'selections',
        'selected_variation_id',
        'booking_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'selections' => 'array',
        'booking_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function variation()
    {
        return $this->belongsTo(ServiceVariation::class, 'selected_variation_id');
    }

    /**
     * Get variation from selections dynamically
     */
    public function getVariation()
    {
        // إذا كان هناك variation_id في selections
        if (is_array($this->selections) && isset($this->selections['_variation_id'])) {
            // لا يمكن استخدام eager loading لعلاقة attributeValues لأنها ليست علاقة Eloquent فعلية
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

    /**
     * Get subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get cart items for current user/session
     */
    public static function getCartItems()
    {
        if (Auth::check()) {
            return self::where('user_id', Auth::id())
                ->with(['service.thumbnailImage', 'service.images'])
                ->get();
        } else {
            return self::where('session_id', session()->getId())
                ->with(['service.thumbnailImage', 'service.images'])
                ->get();
        }
    }

    /**
     * Get cart count
     */
    public static function getCartCount()
    {
        if (Auth::check()) {
            return self::where('user_id', Auth::id())->sum('quantity');
        } else {
            return self::where('session_id', session()->getId())->sum('quantity');
        }
    }

    /**
     * Get cart total
     */
    public static function getCartTotal()
    {
        $items = self::getCartItems();

        return $items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Clear cart
     */
    public static function clearCart()
    {
        if (Auth::check()) {
            self::where('user_id', Auth::id())->delete();
        } else {
            self::where('session_id', session()->getId())->delete();
        }
    }
}
