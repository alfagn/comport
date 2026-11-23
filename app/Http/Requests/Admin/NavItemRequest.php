<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation rules for a navigation link.
 */
class NavItemRequest extends FormRequest
{
    /** This endpoint is already behind auth middleware; rules decide. */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'menu' => ['required', Rule::in(['utility', 'drawer', 'mega-capabilities', 'mega-journal'])],
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:nav_items,id'],
            'sub' => ['nullable', 'string', 'max:200'],
            'meta' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
