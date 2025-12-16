<?php

namespace App\Domain\Services\Races;

use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\RaceAttributeRepository;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ConflictException;
use App\Domain\Models\Race;

class UpdateRaceService
{
    private RaceRepository $races;
    private RaceAttributeRepository $attributes;

    public function __construct()
    {
        $this->races = new RaceRepository();
        $this->attributes = new RaceAttributeRepository();
    }

    public function execute(array $data): Race
    {
        $race = $this->races->findById($data['id']);

        if (!$race) {
            throw new NotFoundException('Raça não encontrada.');
        }

        if (!empty($data['name']) && $data['name'] !== $race->name) {
            if ($this->races->existsByName($data['name'])) {
                throw new ConflictException('Já existe uma raça com esse nome.');
            }

            $race->name = $data['name'];
        }

        if (isset($data['description'])) {
            $race->description = $data['description'];
        }

        $this->races->update($race->id, [
            'name'        => $race->name,
            'description' => $race->description,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        if (!empty($data['attributes'])) {
            $this->attributes->replaceAttributes(
                $race->id,
                $data['attributes']
            );

            $race->attributes = $data['attributes'];
        }

        return $race;
    }
}
