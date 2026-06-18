<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Dog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateDogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Dog::class, $this->route('shelter')]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'age_months' => ['nullable', 'integer', 'min:0'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'description' => ['nullable', 'string'],
            'is_urgent' => ['boolean'],
            'rescued_at' => ['nullable', 'date'],
        ];
    }
}
