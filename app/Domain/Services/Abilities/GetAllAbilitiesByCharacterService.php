<?php

namespace App\Domain\Services\Abilities;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityNewRepository;
use App\Infrastructure\Repositories\AbilityRepository;
use App\Infrastructure\Repositories\AbilityElementTypeRepository;

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

        $abilityRepo =
            new AbilityRepository();

        $abilityElementRepo =
            new AbilityElementTypeRepository();

        $abilities = [];

        /*
         * =========================================================
         * HABILIDADES JÁ CONVERTIDAS
         * character_abilities -> abilities_new
         * =========================================================
         */

        $abilityIds =
            $charAbilityRepo
                ->getAbilitiesByCharacter(
                    $characterId
                );

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

        /*
         * =========================================================
         * HABILIDADES AINDA NÃO CONVERTIDAS
         * abilities -> status draft
         * =========================================================
         */

        $draftAbilities =
            $abilityRepo
                ->findByCharacterId(
                    $characterId
                );

        foreach ($draftAbilities as $draft) {
            if ($draft->status !== 'draft') {
                continue;
            }

            $draft->normal_element_types =
                $abilityElementRepo
                    ->getByAbilityIdAndForm(
                        $draft->id,
                        'normal'
                    );

            $draft->arcane_element_types =
                $abilityElementRepo
                    ->getByAbilityIdAndForm(
                        $draft->id,
                        'arcane'
                    );

            $abilities[] = [
                'ability' => $draft->toArray(),
                'elements' => [],
                'schema' => 'draft',
            ];
        }

        return $abilities;
    }
}
