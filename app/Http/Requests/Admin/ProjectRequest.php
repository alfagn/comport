<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for ProjectRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility).
 */
class ProjectRequest extends FormRequest
{
    /** This endpoint is already behind auth/middleware; rules decide. */
    public function authorize(): bool
    {
        return true;
    }

    /** Prepare and sanitize empty file inputs before validation. */
    protected function prepareForValidation(): void
    {
        if ($this->hasFile('gallery_files')) {
            $files = array_filter((array) $this->file('gallery_files'), fn ($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid());
            if (empty($files)) {
                $this->request->remove('gallery_files');
                $this->files->remove('gallery_files');
            } else {
                $this->files->set('gallery_files', array_values($files));
            }
        }

        if ($this->hasFile('image') && ! $this->file('image')?->isValid()) {
            $this->request->remove('image');
            $this->files->remove('image');
        }

        if ($this->hasFile('logo') && ! $this->file('logo')?->isValid()) {
            $this->request->remove('logo');
            $this->files->remove('logo');
        }
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'alpha_dash'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'live_url' => ['nullable', 'url', 'max:500'],
            'location' => ['nullable', 'string', 'max:200'],
            'client' => ['nullable', 'string', 'max:160'],
            'kicker' => ['nullable', 'string', 'max:200'],
            'year' => ['nullable', 'string', 'max:4'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'facts' => ['nullable', 'string'],
            'stats' => ['nullable', 'string'],
            'sections' => ['nullable', 'string'],
            'quote' => ['nullable', 'string'],
            'quote_cite' => ['nullable', 'string', 'max:255'],
            'video_poster' => ['nullable', 'string', 'max:255'],
            'video_src' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'max:4096'],
            'gallery_files.*' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
