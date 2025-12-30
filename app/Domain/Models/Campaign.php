<?php

namespace App\Domain\Models;

class Campaign
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,

        public int $created_by,

        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
