<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('section_id') && ! is_numeric($this->section_id) && ! empty($this->section_id)) {
            $section = \App\Models\Section::where('slug', $this->section_id)
                ->orWhere('name', $this->section_id)
                ->first();

            if ($section) {
                $this->merge(['section_id' => $section->id]);
            } else {
                $this->merge(['section_id' => null]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'section_id' => 'nullable|exists:sections,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'images' => 'nullable|array',
            'images.*' => 'image',
            'keywords' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'delete_image_ids' => 'nullable|array',
            'delete_image_ids.*' => 'integer',
        ];
    }
}
