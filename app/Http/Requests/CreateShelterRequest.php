<?php

namespace App\Http\Requests;

use App\Models\Shelter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateShelterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Shelter::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:shelters'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
