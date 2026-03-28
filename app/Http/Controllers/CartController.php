<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Service;
use App\Models\ServiceVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display cart
     */
    public function index()
    {
        $cartItems = CartItem::getCartItems();
        $total = CartItem::getCartTotal();
        $tax = $total * 0.15; // 15% ضريبة
        $grandTotal = $total + $tax;

        return view('cart.index', compact('cartItems', 'total', 'tax', 'grandTotal'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request, Service $service)
    {
        try {
            if ($service->suppliers()->count() === 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'هذه الخدمة غير متوفرة حالياً ولا يمكن إضافتها إلى السلة.',
                    ], 422);
                }

                return redirect()->back()->with('error', 'هذه الخدمة غير متوفرة حالياً ولا يمكن إضافتها إلى السلة.');
            }

            $validated = $request->validate([
                'quantity' => 'nullable|integer|min:1|max:100',
                'customer_notes' => 'nullable|string|max:1000',
                'selections' => 'nullable|array',
                'selected_variation_id' => 'nullable|integer',
                'booking_date' => 'nullable|date|after:today',
            ]);

            $quantity = $validated['quantity'] ?? 1;
            $selections = $validated['selections'] ?? null;
            $bookingDate = $validated['booking_date'] ?? null;

            // Variation requirement and price determination
            $itemPrice = $service->price ?? 0;
            if ($service->has_variations) {
                $variationId = $validated['selected_variation_id'] ?? null;
                if (! $variationId) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => 'يرجى اختيار جميع الخيارات قبل الإضافة للسلة.'], 422);
                    }

                    return redirect()->route('services.show', $service->id)->with('error', 'يرجى اختيار جميع الخيارات قبل الإضافة للسلة.');
                }
                $variation = ServiceVariation::where('service_id', $service->id)
                    ->where('id', $variationId)
                    ->where('is_active', true)
                    ->first();
                if (! $variation) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => 'التركيبة المختارة غير متاحة.'], 422);
                    }

                    return redirect()->back()->with('error', 'التركيبة المختارة غير متاحة.');
                }
                // استخدم السعر الفعّال للتنويعة (sale_price إن وجد وإلا السعر العادي)
                $itemPrice = (float) ($variation->active_price ?? $variation->price);
                // Ensure selections include variation_id so identical items merge correctly
                if (! is_array($selections)) {
                    $selections = [];
                }
                $selections['_variation_id'] = (string) $variationId;
            }

            // Normalize selections (remove empty, sort arrays)
            if (is_array($selections)) {
                $normalized = [];
                foreach ($selections as $key => $value) {
                    if (is_array($value)) {
                        $vals = array_values(array_filter($value, function ($v) {
                            return $v !== null && $v !== '';
                        }));
                        sort($vals);
                        if (count($vals) > 0) {
                            $normalized[$key] = $vals;
                        }
                    } else {
                        if ($value !== null && $value !== '') {
                            $normalized[$key] = (string) $value;
                        }
                    }
                }
                $selections = count($normalized) > 0 ? $normalized : null;
            }

            // Build base query for existing items of this service for current user/session
            $cartItemQuery = CartItem::where('service_id', $service->id)
                ->where(function ($query) {
                    if (Auth::check()) {
                        $query->where('user_id', Auth::id());
                    } else {
                        $query->where('session_id', session()->getId());
                    }
                });

            // Find an existing item with EXACT same selections AND same booking_date
            $existingItems = $cartItemQuery->get();
            $cartItem = null;
            foreach ($existingItems as $item) {
                $sameDateRaw = $item->getRawOriginal('booking_date');
                $incomingDate = $bookingDate ? date('Y-m-d', strtotime($bookingDate)) : null;
                $sameDate = $sameDateRaw === $incomingDate;
                if ($item->selections == $selections && $sameDate) {
                    $cartItem = $item;
                    break;
                }
            }

            if ($cartItem) {
                // Update quantity only when selections match
                $cartItem->quantity += $quantity;
                if (isset($validated['customer_notes'])) {
                    $cartItem->customer_notes = $validated['customer_notes'];
                }
                $cartItem->save();
            } else {
                // Create new cart item
                CartItem::create([
                    'user_id' => Auth::id(),
                    'session_id' => ! Auth::check() ? session()->getId() : null,
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'customer_notes' => $validated['customer_notes'] ?? null,
                    'selections' => $selections,
                    'booking_date' => $bookingDate,
                ]);
            }

            // إذا كان الطلب AJAX، أرجع JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تمت إضافة الخدمة إلى السلة بنجاح',
                    'cart_count' => CartItem::getCartCount(),
                ]);
            }

            // إذا كان طلب عادي، أرجع redirect مع رسالة
            return redirect()->back()->with('success', 'تمت إضافة الخدمة إلى السلة بنجاح');

        } catch (\Exception $e) {
            \Log::error('Cart add error: '.$e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الإضافة للسلة. حاول مرة أخرى.',
                ], 500);
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء الإضافة للسلة. حاول مرة أخرى.');
        }
    }

    /**
     * Update cart item
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        $cartItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث السلة بنجاح',
            'subtotal' => $cartItem->subtotal,
            'total' => CartItem::getCartTotal(),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخدمة من السلة',
            'cart_count' => CartItem::getCartCount(),
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        CartItem::clearCart();

        return redirect()->route('cart.index')->with('success', 'تم تفريغ السلة بنجاح');
    }

    /**
     * Get cart count (for AJAX)
     */
    public function count()
    {
        return response()->json([
            'count' => CartItem::getCartCount(),
        ]);
    }

    /**
     * Get cart dropdown HTML (for AJAX)
     */
    public function getDropdownHtml()
    {
        $cartItems = CartItem::getCartItems();
        $cartTotal = CartItem::getCartTotal();
        $cartCount = CartItem::getCartCount();

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'html' => view('partials.cart-dropdown', compact('cartItems', 'cartTotal', 'cartCount'))->render(),
        ]);
    }
}
