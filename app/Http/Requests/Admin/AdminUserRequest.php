<?php

namespace App\Http\Requests\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

/**
 * Validation for creating/updating a dashboard admin account.
 *
 * Team (`members`) is the single source of personal identity (name, email, photo);
 * Admin accounts bind a `member_id` to login credentials (`password` + `roles`).
 */
class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('admin');

        if ($userId) {
            return [
                'member_id' => ['nullable', 'integer', Rule::exists('members', 'id')],
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'password' => ['nullable', 'confirmed', Password::min(8)],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['string', Rule::exists('roles', 'name')],
            ];
        }

        // Creating an admin by selecting a Team member (`member_id`)
        if ($this->filled('member_id') || ! $this->filled('email')) {
            return [
                'member_id' => ['required', 'integer', Rule::exists('members', 'id')],
                'password' => ['required', 'confirmed', Password::min(8)],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['string', Rule::exists('roles', 'name')],
            ];
        }

        // Programmatic fallback if name + email were posted directly
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $userId = $this->route('admin');
                if (! $userId && $this->filled('member_id')) {
                    $member = Member::find((int) $this->input('member_id'));
                    if (! $member) {
                        return;
                    }
                    if (empty($member->email)) {
                        $validator->errors()->add('member_id', 'This team member does not have an email address yet. Please add their email in Team & Founders first.');
                        return;
                    }
                    $alreadyAdmin = User::where('member_id', $member->id)
                        ->orWhereRaw('LOWER(email) = ?', [strtolower($member->email)])
                        ->exists();
                    if ($alreadyAdmin) {
                        $validator->errors()->add('member_id', 'This team member already has an active Admin account.');
                    }
                }
            },
        ];
    }
}
