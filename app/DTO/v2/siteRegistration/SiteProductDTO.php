<?php

declare(strict_types=1);

namespace App\DTO\v2\siteRegistration;

readonly class SiteProductDTO
{
    public function __construct(
        public int    $id,
        public float $quantity
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            quantity: (float)$data['quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
        ];
    }
}
