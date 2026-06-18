<?php

namespace App\Http\Requests;

use App\Enums\DonationType;
use App\Models\Donation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Donation::class, $this->route('campaign')]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(DonationType::class)],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
