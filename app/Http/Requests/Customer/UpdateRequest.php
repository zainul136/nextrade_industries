<?php

namespace App\Http\Requests\Customer;

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
            'name' => ['required'],
            'contact' => ['required'],
            'address' => ['required'],
            'email' => ['required', 'email'],
            'country' => ['required'],
            'product' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The name field is required.'),
            'contact.required' => __('The contact field is required.'),
            'address.required' => __('The address field is required.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email address must be a valid.'),
            'country.required' => __('The country field is required.'),
            'product.required' => __('The product field is required.'),
        ];
    }
}
