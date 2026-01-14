<?php

namespace App\Domain\Services\Abilities;

use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityRepository;

class GetAbilityByCharacterService
{
    public function execute(int $characterId, int $abilityId): array
    {
        if ($characterId <= 0 || $abilityId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                [
                    'character_id' => ['character_id inválido.'],
                    'ability_id' => ['ability_id inválido.'],
                ]
            );
        }

        $charAbilityRepo = new CharacterAbilityRepository();
        $abilityRepo = new AbilityRepository();

        if (!$charAbilityRepo->exists($characterId, $abilityId)) {
            throw new NotFoundException(
                'Habilidade não encontrada para este personagem.'
            );
        }

        $ability = $abilityRepo->findByIdWithElements($abilityId);

        if (!$ability) {
            throw new NotFoundException('Habilidade não encontrada.');
        }

        return $ability;
    }
}
