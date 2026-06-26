<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'family_name' => ['required', 'string', 'max:120'],
            'family_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'daily_goal_pages' => ['required', 'integer', 'min:1', 'max:604'],
            'monthly_goal_pages' => ['required', 'integer', 'min:1', 'max:18120'],
            'enable_leaderboard' => ['nullable', 'boolean'],
            'enable_badges' => ['nullable', 'boolean'],
        ];
    }
}
