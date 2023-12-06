<?php

namespace App\Http\Requests\Color;

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
            'name' => ['required', 'string', 'unique:colors,name,' . decrypt($this->id)],
            'slug' => ['required', 'string', 'unique:colors,slug,' . decrypt($this->id)]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The color name field is required.'),
            'name.unique' => __('The color name has already been taken.'),
            'slug.required' => __('The slug field is required.'),
            'slug.unique' => __('The slug has already been taken.'),
        ];
    }
}
