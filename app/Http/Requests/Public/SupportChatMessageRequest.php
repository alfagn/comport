<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class SupportChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $max = (int) config('assistant.support.max_message_length', 1500);

        return [
            'message' => ['required', 'string', 'min:1', 'max:'.$max],
            'token' => ['nullable', 'uuid'],
            'landing_page' => ['nullable', 'string', 'max:250'],
        ];
    }
}
