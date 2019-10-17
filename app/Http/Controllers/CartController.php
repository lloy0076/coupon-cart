<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     * @throws \Exception
     */
    public function index()
    {
        $cart = $this->getUserCart();

        return response()->json($cart->load(['cartItems.product', 'coupon']));
    }

    /**
     * @param \App\Models\Product $product
     * @param int                 $quantity
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function addItem(Product $product, $quantity = 1)
    {
        $cart = $this->getUserCart();

        $cart->addItem($product, $quantity);

        return response()->json($cart->load(['cartItems.product', 'coupon']));
    }

    /**
     * Apply the given coupon.
     *
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function applyCoupon(Coupon $coupon)
    {
        $cart = $this->getUserCart();

        $cart->applyCoupon($coupon);

        return response()->json($cart->load(['cartItems.product', 'coupon']));
    }

    /**
     * Finalises the cart by setting the submitted date.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function finaliseCart(Cart $cart)
    {
        DB::transaction(function () use ($cart) {
            $cart->submitted_date = Carbon::now();

            $didSave = $cart->save();

            if (!$didSave) {
                throw new Exception("Unable to finalise cart id: " . $cart->id);
            }

            // Clear out the session.
            $sessionKey = config('cart_session_key', 'cc_session_key');
            Session::remove($sessionKey);
        });

        $cart->refresh();

        return response()->json("ok", 200);
    }

    /**
     * Removes the coupon(s).
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function removeCoupons()
    {
        $cart = $this->getUserCart();

        $cart->removeCoupons();

        return response()->json($cart->load(['cartItems.product', 'coupon']));
    }

    /**
     * @param \App\Models\CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function removeItem(CartItem $cartItem)
    {
        $cart = $this->getUserCart();

        $cart->removeItem($cartItem);

        return response()->json($cart->load(['cartItems.product', 'coupon']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        if (request()->expectsJson()) {
            return response()->json($cart->load(['cartItems'], 200));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cart         $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }

    /**
     * @return \App\Models\Cart
     * @throws \Exception
     */
    protected function getUserCart(): Cart
    {
        $user = Auth::user();

        $sessionKey = config('cart_session_key', 'cc_session_key');
        $orderId    = Session::get($sessionKey, null);

        // If we can find it by ID, use it.
        $cart = Cart::where('order_id', $orderId)->first();
//        $cart = Cart::where('id', 3)->first();

        if (!isset($cart)) {
            // Or we'll make a new one.
            $cart = new Cart();

            $cart->order_id = Str::uuid();
            $cart->user_id  = $user->id;

            $didSave = $cart->save();

            if (!$didSave) {
                throw new Exception("Unable to make a new cart for " . $user->id);
            }

            $cart->fresh();

            Session::put($sessionKey, $cart->order_id);
        }

        return $cart;
    }
}
