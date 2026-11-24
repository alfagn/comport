<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServicePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merged = [];

        if ($this->filled('slug')) {
            $merged['slug'] = Str::slug((string) $this->input('slug'));
        } elseif ($this->has('slug')) {
            $merged['slug'] = null;
        }

        // Compile structured section cards into the plain-text body format when submitted from the card editor
        if ($this->has('sections') && is_array($this->input('sections'))) {
            $blocks = collect($this->input('sections'))
                ->map(function ($sec) {
                    if (! is_array($sec)) {
                        return null;
                    }
                    $heading = trim((string) ($sec['heading'] ?? ''));
                    $content = trim((string) ($sec['content'] ?? ''));
                    if ($heading === '' && $content === '') {
                        return null;
                    }

                    return $content !== '' ? "{$heading}\n{$content}" : $heading;
                })
                ->filter()
                ->values()
                ->all();

            $merged['body'] = implode("\n\n", $blocks);
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        $currentId = $this->route('id') ? (int) $this->route('id') : null;

        return [
            'slug' => [
                'nullable',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('service_pages', 'slug')
                    ->where(fn ($q) => $q->where('locale', 'en'))
                    ->ignore($currentId),
            ],
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:20000'],
            'sections' => ['nullable', 'array'],
            'sections.*.heading' => ['nullable', 'string', 'max:255'],
            'sections.*.content' => ['nullable', 'string', 'max:5000'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Please add at least one content section with a heading or description.',
            'slug.unique' => 'Another English service page already uses this URL slug.',
        ];
    }
}
