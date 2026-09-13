<?php

namespace App\Domain\Services\Abilities;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityNewRepository;

class GetAllAbilitiesByCharacterService
{
    public function execute(int $characterId): array
    {
        if ($characterId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                [
                    'character_id' => [
                        'character_id inválido.'
                    ]
                ]
            );
        }

        $charAbilityRepo =
            new CharacterAbilityRepository();

        $abilityNewRepo =
            new AbilityNewRepository();

        $abilityIds =
            $charAbilityRepo
                ->getAbilitiesByCharacter(
                    $characterId
                );

        $abilities = [];

        foreach ($abilityIds as $abilityId) {
            $ability =
                $abilityNewRepo
                    ->findCompleteById(
                        (int) $abilityId
                    );

            if (!$ability) {
                continue;
            }

            $abilities[] = [
                'ability' => $ability,
                'elements' => [],
                'schema' => 'new',
            ];
        }

        return $abilities;
    }
}
