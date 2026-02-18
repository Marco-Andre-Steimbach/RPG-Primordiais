<?php

namespace App\Domain\Models;

class Encounter
{
    public function __construct(
        public int $id,
        public int $campaign_id,
        public string $name,
        public string $description,
        public string $status,
        public ?string $created_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'campaign_id' => $this->campaign_id,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => $this->status,
            'created_at'  => $this->created_at,
        ];
    }
}
