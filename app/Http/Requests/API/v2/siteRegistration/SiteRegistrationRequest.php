<?php

declare(strict_types=1);

namespace App\Http\Requests\API\v2\siteRegistration;

use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteRegistrationRequest extends FormRequest
{
    use CustomNepaliCalendarConverter;

    public function authorize(): bool
    {
        return true; // Adjust with policies if required
    }

    public function rules(): array
    {
        return [
            // Step 1 – basic info
            'site_name' => ['required', 'string', 'max:120'],
            'site_type_id' => ['required', 'integer', Rule::exists('v2_site_types', 'id')],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],

            // Step 2 – contacts & stakeholders
            'dealer_code' => ['required', 'string', 'max:60', Rule::exists('dealers', 'code')],
            'contractor_id' => ['nullable', 'integer', Rule::exists('contractors', 'id')],
            'stakeholders' => ['nullable', 'array'],
            'stakeholders.*.name' => ['required_with:stakeholders', 'string', 'max:100'],
            'stakeholders.*.role' => ['required_with:stakeholders', 'string', 'max:60'],
            'stakeholders.*.contact_number' => [
                'required_with:stakeholders',
                'string',
                'max:20',
                Rule::unique('v2_site_contacts', 'phone')
            ],

            // Step 3 – site details
            'construction_status_id' => ['required', 'integer', Rule::exists('v2_site_construction_stages', 'id')],
            'number_of_floors' => ['nullable', 'numeric'],
            'approximate_area_sqft' => ['nullable', 'numeric'],
            'preferred_brand' => ['nullable', 'string', 'max:80'],
            'competitor_brands' => ['nullable', 'string', 'max:80'],
            'estimated_paint_litres' => ['nullable', 'integer'],

            // Step 4 – products
            'products' => ['nullable', 'array'],
            'products.*.id' => ['required_with:products', 'integer', Rule::exists('v2_site_products', 'id')],
            'products.*.quantity' => ['required_with:products', 'numeric', 'min:1'],

            // Step 5 – follow‑up
            'next_followup_at' => ['nullable'],
            'follow_up_type' => ['nullable', 'string', Rule::in(['call', 'visit', 'demo', 'sampling', 'others'])],
            'follow_up_status' => ['nullable', 'string', 'max:255'],
            'follow_up_notes' => ['nullable', 'string'],
            'auto_reminder_enabled' => ['nullable', 'boolean'],

            // Step 6 – photos & notes
            'site_photos' => ['nullable', 'array', 'min:1', 'max:5'],
            'site_photos.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'progress_photos' => ['nullable', 'array', 'max:5'],
            'progress_photos.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'additional_notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Additional after‑validation check for Nepali future dates.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $next = $this->input('next_followup_at');
            if ($next && !$this->isFutureNepaliDate($next)) {
                $validator->errors()->add('next_followup_at', 'The next follow‑up date must be in the future.');
            }
        });
    }
}
