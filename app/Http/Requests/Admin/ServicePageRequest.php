<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServicePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:20000'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['required', 'boolean'],
        ];
    }
}
