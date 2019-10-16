<?php

namespace App\Http\Requests;

use App\Constants;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CouponRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRole(Constants::ROLE_ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_id'        => 'required|exists:coupons,id',
            'rule_type'        => [
                'required',
                'string',
                Rule::in(['coupon', 'discount', 'other']),
            ],
            'rule'             => 'required|string|max:255',
            'rule_description' => 'nullable|string|max:65536',
            'rule_order'       => 'nullable|integer',
            'rule_not'         => 'nullable|boolean',
            'rule_operator'    => [
                'nullable',
                'string',
                'max:255',
                Rule::in(['and', 'or']),
            ],
        ];
    }

    /**
     * Perform further validations.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    protected function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            // Create a dummy cart to do the test.
            $cart = new Cart();
            $interpreter = new ExpressionLanguage();

            $rule = request()->input('rule');

            Log::info($rule);

            try {
                $interpreter->evaluate($rule, ['cart' => $cart]);
            } catch (\Exception $e) {
                $validator->errors()->add('rule_description', $e->getMessage());
                Log::debug($e->getMessage());
                Log::debug($e->getTraceAsString());
            }
        });
    }

}
