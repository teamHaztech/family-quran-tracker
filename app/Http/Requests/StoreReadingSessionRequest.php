<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReadingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'surah' => ['nullable', 'string', 'max:100'],
            'start_page' => ['nullable', 'integer', 'min:1', 'max:604'],
            'end_page' => ['nullable', 'integer', 'min:1', 'max:604', 'gte:start_page'],
            'pages_read' => ['nullable', 'integer', 'min:0', 'max:604'],
            'juz' => ['nullable', 'integer', 'min:1', 'max:30'],
            'minutes_read' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'method' => ['nullable', 'in:manual,timer'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_page.gte' => 'The ending page must be greater than or equal to the starting page.',
        ];
    }
}
