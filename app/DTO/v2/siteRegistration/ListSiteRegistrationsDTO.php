<?php
declare(strict_types=1);

namespace App\DTO\v2\siteRegistration;

use Illuminate\Http\Request;

final class ListSiteRegistrationsDTO
{
    public readonly ?string $search;
    public readonly ?array $currentStageId;
    public readonly ?array $siteTypeId;
    public readonly int $perPage;
    public readonly ?string $cursor;

    private function __construct(
        ?string $search,
        ?array  $currentStageId,
        ?array  $siteTypeId,
        int     $perPage,
        ?string $cursor
    )
    {
        $this->search = $search;
        $this->currentStageId = $currentStageId;
        $this->siteTypeId = $siteTypeId;
        $this->perPage = $perPage;
        $this->cursor = $cursor;
    }

    /**
     * Static constructor from request.
     *
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        $payload = $request->validated();

        return self::fromArray($payload);
    }

    /**
     * Static constructor from associative array (used by FormRequest).
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['search'] ?? null,
            isset($data['current_stage_id']) ? (array)$data['current_stage_id'] : null,
            isset($data['site_type_id']) ? (array)$data['site_type_id'] : null,
            isset($data['per_page']) ? (int)$data['per_page'] : 15,
            $data['cursor'] ?? null,
        );
    }

    /**
     * Convert DTO to plain array for logging or caching.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'current_stage_id' => $this->currentStageId,
            'site_type_id' => $this->siteTypeId,
            'per_page' => $this->perPage,
            'cursor' => $this->cursor,
        ];
    }
}

