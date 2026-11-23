<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validation for creating/updating a dashboard admin account.
 */
class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route middleware already enforced "manage admins" server-side.
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            // Required on create, optional (keep current) on update.
            'password' => [$userId ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ];
    }
}
