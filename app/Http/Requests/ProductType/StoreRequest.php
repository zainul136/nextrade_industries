<?php

namespace App\Http\Requests\ProductType;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'product_type' => ['required'],
            'slug' => ['required', 'unique:product_types,slug'],
        ];
    }

    public function messages()
    {
        return [
            'product_type.required' => __('The product type field is required.'),
            'slug.required' => __('The slug field is required.'),
            'slug.unique' => __('The slug has already been taken.')
        ];
    }
}
