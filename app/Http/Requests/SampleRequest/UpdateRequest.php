<?php

namespace App\Http\Requests\SampleRequest;

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
            'customer_id' => ['required'],
            'type' => ['required'],
            'status' => ['nullable']
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => __('The Customer field is required.'),
            'type.required' => __('The Type field is required.'),
        ];
    }
}
