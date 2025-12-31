<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id' => 'nullable|exists:sections,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'url' => 'sometimes|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'keywords' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }
}
