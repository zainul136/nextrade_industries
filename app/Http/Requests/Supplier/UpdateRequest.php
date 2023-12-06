<?php

namespace App\Http\Requests\Supplier;

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
            'contact' => ['required', 'unique:suppliers,contact,' . decrypt($this->id)],
            'address' => ['required'],
            'email' => ['required', 'email', 'unique:suppliers,email,' . decrypt($this->id)],
            'country' => ['required'],
            'product' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The supplier name field is required.'),
            'contact.required' => __('The contact field is required.'),
            'contact.unique' => __('The contact has already been registered.'),
            'address.required' => __('The address field is required.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email address must be valid.'),
            'email.unique' => __('The email has already been taken.'),
            'country.required' => __('Country field is required.'),
            'product.required' => __('Product field is required.')
        ];
    }
}
