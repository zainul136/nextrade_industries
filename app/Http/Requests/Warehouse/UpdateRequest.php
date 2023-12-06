<?php

namespace App\Http\Requests\Warehouse;

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
            'contact' => ['required', 'unique:warehouses,contact,' . decrypt($this->id)],
            'location' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The warehouse name field is required.'),
            'contact.required' => __('The contact field is required.'),
            'contact.unique' => __('The contact field has already been taken.'),
            'location.required' => __('The location field is required.')
        ];
    }
}
