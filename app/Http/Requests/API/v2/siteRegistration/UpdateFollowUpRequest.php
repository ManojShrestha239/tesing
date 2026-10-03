<?php
declare(strict_types=1);

namespace App\Http\Requests\API\v2\siteRegistration;

use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFollowUpRequest extends FormRequest
{
    use CustomNepaliCalendarConverter;

    public function authorize(): bool
    {
        // Authorisation logic can go here (policy, gate, etc.)
        return true;
    }

    /**
     * Cast booleans before validation runs.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('auto_reminder_enabled')) {
            $this->merge([
                'auto_reminder_enabled' => filter_var(
                    $this->input('auto_reminder_enabled'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Stage change
            'construction_status_id' => ['required', 'integer', 'exists:v2_site_construction_stages,id'],

            // Current follow‑up update (optional)
            'current_follow_up_type' => ['required', 'string', 'max:255', 'in:call,visit,demo,sampling,others'],
            'current_follow_up_status' => ['required', 'string', 'max:255', 'in:completed,cancelled'],
            'current_follow_up_notes' => ['nullable', 'string'],

            // Coordinates (required only when a status change occurs)
            'lat' => ['required_with:current_follow_up_status', 'numeric'],
            'lng' => ['required_with:current_follow_up_status', 'numeric'],

            // Next follow‑up (optional)
            'next_followup_at' => ['nullable'],
            'follow_up_type' => ['nullable', 'string', 'max:255', 'in:call,visit,demo,sampling,others'],
            'follow_up_status' => ['nullable', 'string', 'max:255'],
            'follow_up_notes' => ['nullable', 'string'],
            'auto_reminder_enabled' => ['nullable', 'boolean'],

            // Photos (optional, up to 5)
            'site_photos' => ['nullable', 'array', 'min:1', 'max:5'],
            'site_photos.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }

    /**
     * Helper to get the next follow‑up date as a Carbon instance or null.
     */
//    public function getNextFollowUpAt(): ?Carbon
//    {
//        $raw = $this->input('next_followup_at');
//        return $raw ? Carbon::parse($raw) : null;
//    }

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
