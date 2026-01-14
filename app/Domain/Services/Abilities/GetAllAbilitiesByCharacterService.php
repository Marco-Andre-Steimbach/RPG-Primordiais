<?php

namespace App\Domain\Services\Abilities;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityRepository;

class GetAllAbilitiesByCharacterService
{
    public function execute(int $characterId): array
    {
        if ($characterId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                ['character_id' => ['character_id inválido.']]
            );
        }

        $charAbilityRepo = new CharacterAbilityRepository();
        $abilityRepo = new AbilityRepository();

        $abilityIds = $charAbilityRepo->getAbilitiesByCharacter($characterId);
        $abilities = [];

        foreach ($abilityIds as $abilityId) {
            $ability = $abilityRepo->findByIdWithElements((int) $abilityId);

            if ($ability) {
                $abilities[] = $ability;
            }
        }

        return $abilities;
    }
}
