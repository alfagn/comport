<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for ServiceRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility).
 */
class ServiceRequest extends FormRequest
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
            'num' => ['nullable', 'string', 'max:10'],
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'alpha_dash'],
            'tag' => ['nullable', 'string', 'max:80'],
            'kicker' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'metric_num' => ['nullable', 'string', 'max:40'],
            'metric_label' => ['nullable', 'string', 'max:120'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
