<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|max:255|unique:users,email,' . $userId,
            'title'              => 'nullable|string|max:120',
            'bio'                => 'nullable|string|max:2000',
            'portfolio_url'      => 'nullable|url|max:255',
            'avatar'             => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'current_password'   => ['required_with:password', 'current_password'],
            'password'           => ['nullable', 'confirmed', Password::min(8)],
            'available_for_hire' => 'nullable|boolean',
            'location'           => 'nullable|string|max:120',
            'employment_type'    => 'nullable|in:full_time,part_time,contract',
            'work_style'         => 'nullable|in:remote,hybrid,onsite',
            'target_salary'      => 'nullable|string|max:80',
            'show_email'         => 'nullable|boolean',
            'show_phone'         => 'nullable|boolean',
        ];
    }
}
