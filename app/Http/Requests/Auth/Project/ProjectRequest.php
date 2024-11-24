<?php

namespace App\Http\Requests\Auth\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'title' => ['required', 'min:2', 'max:255'],
            'sdg' => ['required'],
            'project_status' => ['required', 'in:Proposed,On-Going,On-Hold,Completed,Rejected'],
            'is_publish' => ['nullable'],
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,svg,webp|max:2048', // 2 MB max file size
            'description' => ['required', 'min:10'],
            'location_address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
