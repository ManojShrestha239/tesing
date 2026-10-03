<?php
declare(strict_types=1);

namespace App\DTO\v2\siteRegistration;

readonly class StakeholderDTO
{
    public function __construct(
        public string $name,
        public string $role,
        public string $contactNumber
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            role: $data['role'],
            contactNumber: $data['contact_number'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'role' => $this->role,
            'contact_number' => $this->contactNumber,
        ];
    }
}

