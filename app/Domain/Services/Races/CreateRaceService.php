<?php

namespace App\Domain\Services\Races;

use App\Application\DTOs\Races\RaceCreateDTO;
use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\RaceAttributeRepository;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Race;

class CreateRaceService
{
    private RaceRepository $races;
    private RaceAttributeRepository $attributes;

    public function __construct()
    {
        $this->races = new RaceRepository();
        $this->attributes = new RaceAttributeRepository();
    }

    public function execute(RaceCreateDTO $dto): Race
    {
        if ($this->races->existsByName($dto->name)) {
            throw new ConflictException('Já existe uma raça com esse nome.');
        }

        $raceId = $this->races->create([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);

        if (!$raceId) {
            throw new ValidationException('Falha ao criar raça.');
        }

        $this->attributes->attachAttributes($raceId, $dto->attributes);

        $race = $this->races->findById($raceId);

        if (!$race) {
            throw new ValidationException('Erro ao carregar a raça criada.');
        }

        $race->attributes = $dto->attributes;

        return $race;
    }
}
