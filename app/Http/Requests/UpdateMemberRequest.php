<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', \App\Models\User::class) ?? false;
    }

    public function rules(): array
    {
        $memberId = $this->route('member')->id;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($memberId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'date_joined' => ['nullable', 'date'],
            'daily_goal_pages' => ['nullable', 'integer', 'min:1', 'max:604'],
            'current_surah' => ['nullable', 'string', 'max:100'],
            'current_ayah' => ['nullable', 'integer', 'min:1', 'max:286'],
        ];
    }
}
