<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisualRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('section_id') && !is_numeric($this->section_id) && !empty($this->section_id)) {
            $section = \App\Models\Section::where('name', $this->section_id)->first();
            if ($section) {
                $this->merge(['section_id' => $section->id]);
            } else {
                // If section not found by name, set to null (will be handled by controller to use default)
                // or remove it to avoid validation error if it's strict
                $this->merge(['section_id' => null]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => 'nullable|exists:sections,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:upload,link',
            'file' => 'nullable',
            'url' => 'nullable|url',
            'thumbnail' => 'nullable|image',
            'keywords' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }
}
