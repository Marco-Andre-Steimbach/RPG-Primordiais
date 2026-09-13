<?php

namespace App\Domain\Services\Abilities;

use App\Application\DTOs\Abilities\CreateAbilityDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Domain\Models\Ability;
use App\Infrastructure\Repositories\AbilityRepository;
use App\Infrastructure\Repositories\AbilityElementTypeRepository;
use App\Infrastructure\Repositories\CharacterRepository;

class CreateAbilityService
{
    private AbilityRepository $abilities;
    private AbilityElementTypeRepository $elements;
    private CharacterRepository $characters;

    public function __construct()
    {
        $this->abilities =
            new AbilityRepository();

        $this->elements =
            new AbilityElementTypeRepository();

        $this->characters =
            new CharacterRepository();
    }

    public function execute(
        int $characterId,
        CreateAbilityDTO $dto,
        int $userId
    ): Ability {
        $character =
            $this->characters->findById(
                $characterId
            );

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                [
                    'character_id' => [
                        'Personagem não encontrado.'
                    ]
                ]
            );
        }

        if (
            (int) $character->created_by
            !== $userId
        ) {
            throw new ForbiddenException(
                'Você não tem permissão para modificar este personagem.'
            );
        }

        $abilityId =
            $this->abilities->create([
                'character_id' =>
                    $characterId,

                'title' =>
                    $dto->title,

                'description' =>
                    $dto->description,

                'arcane_title' =>
                    $dto->arcane_title,

                'arcane_description' =>
                    $dto->arcane_description,

                'mana_cost' =>
                    $dto->mana_cost,

                'arcane_mana_cost' =>
                    $dto->arcane_mana_cost,

                'required_race_id' =>
                    $dto->required_race_id,

                'required_order_id' =>
                    $dto->required_order_id,

                'status' =>
                    'draft',

                'converted_ability_id' =>
                    null,
            ]);

        if (!$abilityId) {
            throw new ValidationException(
                'Falha ao criar habilidade.'
            );
        }

        foreach (
            $dto->normal_element_types
            as $elementTypeId
        ) {
            $this->elements->attach(
                $abilityId,
                'normal',
                $elementTypeId
            );
        }

        foreach (
            $dto->arcane_element_types
            as $elementTypeId
        ) {
            $this->elements->attach(
                $abilityId,
                'arcane',
                $elementTypeId
            );
        }

        $ability =
            $this->abilities->findById(
                $abilityId
            );

        if (!$ability) {
            throw new ValidationException(
                'Erro ao carregar habilidade criada.'
            );
        }

        $ability->normal_element_types =
            $dto->normal_element_types;

        $ability->arcane_element_types =
            $dto->arcane_element_types;

        return $ability;
    }
}
