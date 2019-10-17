<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartCreationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        $user = Auth::user();

        $sessionKey = config('cart_session_key', 'cc_session_key');

        $orderId  = Session::get($sessionKey, null);

        // If we can find it by ID, use it.
        if (isset($orderId ) && Cart::where('order_id', $orderId )->first()) {
            return $next($request);
        }

        // Otherwise we'll see if there is one just "lying" about.
        $cart = Cart::where(['user_id' => $user->id])->whereNull('submitted_date')->orderByDesc('updated_at')->first();

        if ($cart) {
            Session::put($sessionKey, $cart->order_id);

            return $next($request);
        }

        // Or we'll make a new one.
        $cart = new Cart();

        $cart->order_id = Str::uuid();
        $cart->user_id  = $user->id;

        $didSave = $cart->save();

        if (!$didSave) {
            throw new \Exception("Unable to make a new cart for " . $user->id);
        }

        $cart->fresh();

        Session::put($sessionKey, $cart->order_id);

        return $next($request);
    }
}
