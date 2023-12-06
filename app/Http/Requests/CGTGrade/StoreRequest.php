<?php

namespace App\Http\Requests\CGTGrade;

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
            'grade_name' => ['required', 'unique:c_g_t_grades,grade_name'],
            'slug' => ['required', 'unique:c_g_t_grades,slug'],
            'price' => ['required'],
            'billing_code' => ['nullable'],
            // 'pnl' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'grade_name.required' => __('The grade name field is required.'),
            'grade_name.unique' => __('The grade name has already been taken.'),
            'slug.required' => __('The slug field is required.'),
            'slug.unique' => __('The slug has already been taken.'),
            'price.required' => __('The Price field is required.'),
        ];
    }
}
