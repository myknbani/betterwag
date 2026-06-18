<?php

namespace App\Http\Requests;

use App\Enums\CampaignType;
use App\Models\Campaign;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [Campaign::class, $this->route('shelter')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'goal_amount' => ['required_if:type,one_off', 'nullable', 'integer', 'min:0'],
            'type' => ['required', new Enum(CampaignType::class)],
            'description' => ['nullable', 'string'],
            'dog_id' => ['nullable', 'exists:dogs,id'],
        ];
    }
}
