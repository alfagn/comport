<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for PageSectionRequest.
 *
 * Supports text/textarea sections, public image uploads, private-storage
 * video uploads (or external HTTPS / local asset links), and inline editable
 * content-list card groups.
 */
class PageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'sections' => ['required', 'array'],
            'sections.*' => ['nullable', 'string', 'max:20000'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'videos.*' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/ogg', 'max:102400'],
            'lists' => ['nullable', 'array'],
            'lists.*' => ['nullable', 'array'],
            'lists.*.*.title' => ['nullable', 'string', 'max:255'],
            'lists.*.*.body' => ['nullable', 'string', 'max:5000'],
            'lists.*.*.meta' => ['nullable', 'string', 'max:120'],
        ];
    }
}
