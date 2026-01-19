<?php

namespace App\Domain\Services\Abilities;

use App\Application\DTOs\Abilities\CreateAbilityDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Domain\Models\Ability;
use App\Infrastructure\Repositories\AbilityRepository;
use App\Infrastructure\Repositories\AbilityElementTypeRepository;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\CharacterAbilityRepository;

class CreateAbilityService
{
    private AbilityRepository $abilities;
    private AbilityElementTypeRepository $elements;
    private CharacterRepository $characters;
    private CharacterAbilityRepository $characterAbilities;

    public function __construct()
    {
        $this->abilities = new AbilityRepository();
        $this->elements = new AbilityElementTypeRepository();
        $this->characters = new CharacterRepository();
        $this->characterAbilities = new CharacterAbilityRepository();
    }

    public function execute(
        int $characterId,
        CreateAbilityDTO $dto,
        int $userId
    ): Ability {
        $character = $this->characters->findById($characterId);

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                ['character_id' => ['Personagem não encontrado.']]
            );
        }

        if ($character->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para modificar este personagem.'
            );
        }

        $abilityId = $this->abilities->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'arcane_title' => $dto->arcane_title,
            'arcane_description' => $dto->arcane_description,
            'mana_cost' => $dto->mana_cost,
            'arcane_mana_cost' => $dto->arcane_mana_cost,
            'dice_formula' => $dto->dice_formula,
            'base_damage' => $dto->base_damage,
            'bonus_speed' => $dto->bonus_speed,
            'range' => $dto->range,
            'required_race_id' => $dto->required_race_id,
            'required_order_id' => $dto->required_order_id,
        ]);

        if (!$abilityId) {
            throw new ValidationException('Falha ao criar habilidade.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach($abilityId, $elementTypeId);
        }

        $this->characterAbilities->attach($characterId, $abilityId);

        $ability = $this->abilities->findById($abilityId);

        if (!$ability) {
            throw new ValidationException('Erro ao carregar habilidade criada.');
        }

        $ability->element_types = $dto->element_types;

        return $ability;
    }
}
