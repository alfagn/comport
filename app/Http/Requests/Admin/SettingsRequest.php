<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for SettingsRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility).
 */
class SettingsRequest extends FormRequest
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
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:2000'],
            // Brand assets: optional uploads; current files stay when absent.
            'brand_favicon' => ['nullable', 'file', 'mimes:png,svg,ico,jpg', 'max:1024'],
            'brand_logo' => ['nullable', 'image', 'max:2048'],
            'brand_logo_dark' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
