<?php

namespace App\Http\Requests\Admin;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation rules for MemberRequest.
 *
 * Enforces unique `member_code` (auto-generated when left empty) and unique `email`
 * across all Team members and linked Admin accounts.
 */
class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email') && is_string($this->input('email'))) {
            $cleanEmail = strtolower(trim($this->input('email')));
            $this->merge(['email' => $cleanEmail !== '' ? $cleanEmail : null]);
        }

        if ($this->has('member_code') && is_string($this->input('member_code'))) {
            $cleanCode = strtoupper(trim($this->input('member_code')));
            $this->merge(['member_code' => $cleanCode !== '' ? $cleanCode : null]);
        }

        if ($this->hasFile('photo') && ! $this->file('photo')?->isValid()) {
            $this->request->remove('photo');
            $this->files->remove('photo');
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $rawRouteMember = $this->route('member');
        $memberId = is_object($rawRouteMember) ? $rawRouteMember->id : ($rawRouteMember ? (int) $rawRouteMember : null);
        $existingMember = $memberId ? Member::find($memberId) : null;
        $linkedUserId = $existingMember?->user_id;

        return [
            'member_code' => [
                'nullable',
                'string',
                'max:32',
                'alpha_dash',
                Rule::unique('members', 'member_code')->ignore($memberId),
            ],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('members', 'slug')->ignore($memberId)],
            'role' => ['nullable', 'string', 'max:160'],
            'kind' => ['required', 'in:founder,member'],
            'department' => ['nullable', 'in:engineering,design,cloud,marketing'],
            'focus' => ['nullable', 'string', 'max:255'],
            'lead' => ['nullable', 'string', 'max:500'],
            'expertise' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'email' => [
                $memberId ? 'nullable' : 'required',
                'email',
                'max:160',
                Rule::unique('members', 'email')->ignore($memberId),
                Rule::unique('users', 'email')->ignore($linkedUserId),
            ],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
