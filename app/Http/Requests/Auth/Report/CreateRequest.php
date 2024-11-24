<?php

namespace App\Http\Requests\Auth\Report;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>['required', 'min:2', 'max:255'],
            'sdg'=>['required'],
            'is_publish'=>['required'],
            'image'=>['required', 'image', 'mimes:png,jpg,jpeg,gif,svg,webp'],
            'description'=>['required', 'min:10']
        ];
    }
}
