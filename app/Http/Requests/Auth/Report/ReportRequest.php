<?php

namespace App\Http\Requests\Auth\Report;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
        $rules = [
            'title' => ['required', 'min:2', 'max:255'],
            'is_publish' => ['nullable'],
            'review_status_id' => ['nullable'],            
            'related_type' => ['required', 'in:project,research'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg,gif,svg,webp|max:2048'],
            'description' => ['required', 'min:10'],
        ];

        if ($this->related_type === 'project') {
            $rules['related_id'] = ['required', 'exists:projects,id'];
        } elseif ($this->related_type === 'research') {
            $rules['related_id'] = ['required', 'exists:research,id'];
        }

        return $rules;
    }
}
