<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::orderBy('display_name')->with('couponRules')->with('discountRules')->orderBy('id')->get();

        return response()->json($coupons, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CouponRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(CouponRequest $request)
    {
        $coupon = new Coupon();

        $coupon->coupon_id    = Str::uuid();
        $coupon->display_name = $request->input('display_name');
        $coupon->coupon_code  = $request->input('coupon_code');
        $coupon->order        = $request->input('order');

        $didSave = $coupon->save();

        if (!$didSave) {
            throw new Exception("Unable to save coupon with code " . $request->input('coupon_code'));
        }

        $coupon->fresh()->load(['couponRules', 'discountRules']);

        return response()->json($coupon, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['couponRules', 'discountRules']);

        return response()->json($coupon, 200);
    }

    /**
     * Find by coupon code.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getByCouponCode(Request $request)
    {
        if (!$request->has('coupon_code')) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $couponCode = $request->input('coupon_code');
        $coupon = Coupon::where('coupon_code', $couponCode);

        if (!$coupon->exists()) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($coupon->first(), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Coupon       $coupon
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request, Coupon $coupon)
    {
        $coupon->display_name = $request->input('display_name');
        $coupon->coupon_code  = $request->input('coupon_code');
        $coupon->order        = $request->input('order');

        $didSave = $coupon->save();

        if (!$didSave) {
            throw new Exception("Unable to update coupon with code " . $request->input('coupon_code'));
        }

        $coupon->fresh()->with(['couponRules', 'discountRules']);

        return response()->json($coupon, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Coupon $coupon
     * @return void
     * @throws \Exception
     */
    public function destroy(Coupon $coupon)
    {
        $didDelete = $coupon->delete();

        if (!$didDelete) {
            throw new Exception("Unable to delete coupon " . $coupon->id);
        }

        return response()->json('ok', 200);
    }
}
