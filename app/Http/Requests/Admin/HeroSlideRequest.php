<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for HeroSlideRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility).
 */
class HeroSlideRequest extends FormRequest
{
    /** This endpoint is already behind auth/middleware; rules decide. */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'tag' => ['nullable', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'tab_label' => ['nullable', 'string', 'max:60'],
            'tab_title' => ['nullable', 'string', 'max:120'],
            'tab_sub' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'image', 'max:4096'],
            'meta' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
