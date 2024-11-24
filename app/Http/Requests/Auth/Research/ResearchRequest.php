<?php

namespace App\Http\Requests\Auth\Research;

use Illuminate\Foundation\Http\FormRequest;

class ResearchRequest extends FormRequest
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
            'researchcategory_id' => ['required', 'exists:researchcategories,id'],
            'research_status' => ['required', 'in:Proposed,On-Going,On-Hold,Completed,Rejected'], 
            'sdg' => ['required', 'array'],
            'is_publish' => ['required', 'boolean'],
            
            'file' => ['file', 'mimes:pdf,docx', 'max:2048'], // Adjust mime types and size as needed
            'description' => ['required', 'min:10'],
        ];
    }
}
