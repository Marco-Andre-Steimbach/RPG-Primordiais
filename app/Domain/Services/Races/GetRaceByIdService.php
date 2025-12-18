<?php

namespace App\Domain\Services\Races;

use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\RaceAttributeRepository;
use App\Core\Exceptions\NotFoundException;

class GetRaceByIdService
{
    private RaceRepository $races;
    private RaceAttributeRepository $attributes;

    public function __construct()
    {
        $this->races = new RaceRepository();
        $this->attributes = new RaceAttributeRepository();
    }

    public function execute(int $id)
    {
        $race = $this->races->findById($id);

        if (!$race) {
            throw new NotFoundException('Raça não encontrada.');
        }

        $race->attributes = $this->attributes->getByRace($id);

        return $race;
    }
}
