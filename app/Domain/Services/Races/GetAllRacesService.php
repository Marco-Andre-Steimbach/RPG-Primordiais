<?php

namespace App\Domain\Services\Races;

use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\RaceAttributeRepository;

class GetAllRacesService
{
    private RaceRepository $races;
    private RaceAttributeRepository $attributes;

    public function __construct()
    {
        $this->races = new RaceRepository();
        $this->attributes = new RaceAttributeRepository();
    }

    public function execute(): array
    {
        $races = $this->races->findAll();

        foreach ($races as &$race) {
            $race['attributes'] = $this->attributes->getByRace((int) $race['id']);
        }

        return $races;
    }
}
