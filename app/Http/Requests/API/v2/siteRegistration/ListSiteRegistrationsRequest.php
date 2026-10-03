<?php

namespace App\Http\Requests\API\v2\siteRegistration;

use App\DTO\v2\siteRegistration\ListSiteRegistrationsDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListSiteRegistrationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'current_stage_id' => ['nullable', 'array'],
            'current_stage_id.*' => ['nullable', 'integer', 'exists:v2_site_construction_stages,id'],
            'site_type_id' => ['nullable', 'array'],
            'site_type_id.*' => ['nullable', 'integer', 'exists:v2_site_types,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'cursor' => ['nullable', 'string'],
        ];
    }

    /**
     * Transform the validated request data into a DTO.
     *
     * @return ListSiteRegistrationsDTO
     */
    public function toDTO(): ListSiteRegistrationsDTO
    {
        /** @var array $validated */
        $validated = $this->validated();

        return ListSiteRegistrationsDTO::fromArray([
            'search' => $validated['search'] ?? null,
            'current_stage_id' => $validated['current_stage_id'] ?? null,
            'site_type_id' => $validated['site_type_id'] ?? null,
            'per_page' => $validated['per_page'] ?? 15,
            'cursor' => $validated['cursor'] ?? null,
        ]);
    }


}
