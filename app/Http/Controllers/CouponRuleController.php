<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRuleRequest;
use App\Models\CouponRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \App\Http\Requests\CouponRuleRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(CouponRuleRequest $request)
    {
        $couponRule = new CouponRule();

        $couponRule->coupon_id     = $request->input('coupon_id');
        $couponRule->rule_type     = $request->input('rule_type');
        $couponRule->rule          = $request->input('rule');
        $couponRule->description   = $request->input('rule_description');
        $couponRule->rule_order    = $request->input('rule_order');
        $couponRule->rule_not      = $request->input('rule_not') ? 1 : 0;
        $couponRule->rule_operator = $request->input('rule_operator');

        $didSave = $couponRule->save();

        if (!$didSave) {
            throw new \Exception("Failed to save a rule for coupon id " . $couponRule->coupon_id);
        }

        $couponRule->fresh();

        return response()->json($couponRule, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CouponRule $couponRule
     * @return \Illuminate\Http\Response
     */
    public function show(CouponRule $couponRule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CouponRule $couponRule
     * @return \Illuminate\Http\Response
     */
    public function edit(CouponRule $couponRule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CouponRule   $couponRule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CouponRule $couponRule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CouponRule $couponRule
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(CouponRule $couponRule)
    {
        Log::debug("Deleteing coupon rule " . $couponRule->id);

        $didDelete = $couponRule->delete();

        if (!$didDelete) {
            throw new Exception("Unable to delete coupon rule " . $couponRule->id);
        }

        return response()->json('ok', 200);
    }
}
