<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for JobApplicationRequest.
 *
 * Validation lives here so controllers stay thin and every rule has one
 * obvious home (Single Responsibility). The CV is limited to common
 * document types and 10 MB, matching the promise shown on the form.
 */
class JobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:160'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'portfolio' => ['nullable', 'url', 'max:255'],
            'experience_years' => ['required', 'in:3-5,5-8,8+'],
            'notice_period' => ['required', 'in:immediate,2-weeks,30-days,60-days'],
            'achievement' => ['required', 'string', 'max:5000'],
            'cv_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }
}
