<?php

namespace App\Http\Requests\Admin;

use App\Services\ContentListItemService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation rules for a content list card.
 */
class ContentListItemRequest extends FormRequest
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
            'group' => ['required', Rule::in(array_keys(ContentListItemService::GROUPS))],
            'title' => ['required', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:2000'],
            'meta' => ['nullable', 'string', 'max:80'],
            'badge' => ['nullable', 'string', 'max:80'],
            'cta' => ['nullable', 'string', 'max:160'],
            'url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
