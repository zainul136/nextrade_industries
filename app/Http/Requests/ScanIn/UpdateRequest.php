<?php

namespace App\Http\Requests\ScanIn;

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
        return [
            'supplier_id' => ['required'],
            'warehouse_id' => ['required'],
            'nexpac_bill' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'warehouse_id.required' => __('The Warehouse field is required.'),
            'supplier_id.required' => __('The Supplier field is required.'),
        ];
    }
}
