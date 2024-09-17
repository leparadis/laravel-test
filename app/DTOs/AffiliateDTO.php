<?php

namespace App\DTOs;

class AffiliateDTO
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public int $affiliateId,
        public string $name
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            affiliateId: (int) $data['affiliate_id'],
            name: $data['name']
        );
    }
}
