<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for JobPostRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility).
 */
class JobPostRequest extends FormRequest
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
            'ref_code' => ['nullable', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'alpha_dash'],
            'department' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'max:60'],
            'summary' => ['nullable', 'string'],
            'intro' => ['nullable', 'string'],
            'pills' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'sections' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
