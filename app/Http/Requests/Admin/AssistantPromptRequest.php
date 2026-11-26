<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssistantPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('use ai assistant');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['nullable', 'required_without:attachments', 'string', 'max:6000'],
            'conversation_id' => ['nullable', 'string', 'max:36'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }
}
