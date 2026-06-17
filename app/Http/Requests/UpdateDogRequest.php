<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateDogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'breed' => ['sometimes', 'nullable', 'string', 'max:255'],
            'age_months' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'gender' => ['sometimes', 'nullable', new Enum(Gender::class)],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_urgent' => ['sometimes', 'boolean'],
            'rescued_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
