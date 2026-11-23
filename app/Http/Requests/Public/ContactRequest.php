<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for ContactRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility).
 */
class ContactRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'service' => ['nullable', 'array'],
            'service.*' => ['string', 'max:120'],
            'budget_tier' => ['nullable', 'string', 'max:60'],
            'timeline' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:5000'],
        ];
    }
}
