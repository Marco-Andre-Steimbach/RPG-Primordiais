<?php

namespace App\Domain\Services\Encounters;

use App\Infrastructure\Repositories\EncounterRepository;

class GetAllEncountersService
{
    private EncounterRepository $encounters;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
    }

    public function execute(?string $status = null): array
    {
        return $this->encounters->findAllBasic($status);
    }
}
