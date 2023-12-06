<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // if ($this->input('is_order_pending') == 0) {
            $rules = [
                'customer_id' => ['required'],
                'warehouse_id' => ['required'],
                'container' => ['nullable'],
                'tear_factor' => ['nullable'],
                'seal' => ['nullable'],
                'pallet_weight' => ['nullable'],
                'tear_factor_weight' => ['nullable'],
                'scale_discrepancy' => ['nullable'],
                'pallet_on_container' => ['nullable', 'numeric']
            ];
        // } else {
        //     $rules = [
        //         'customer_id' => ['required'],
        //         'warehouse_id' => ['required'],
        //     ];
        // }
        return $rules;
    }

    public function messages()
    {
        return [
            'warehouse_id.required' => __('The Warehouse field is required.'),
            'customer_id.required' => __('The Customer field is required.'),
            // 'container.required' => __('The Container field is required.'),
            // 'tear_factor.required' => __('The Tare Factor is required.'),
            // 'seal.required' => __('The Seal field is required.'),
            // 'pallet_weight.required' => __('The Pallet Tare is required.'),
            // 'tear_factor_weight.required' => __('The Tare Factor Weight is required.'),
            // 'scale_discrepancy.required' => __('The Scale Tickets Weight is required.'),
        ];
    }
}
