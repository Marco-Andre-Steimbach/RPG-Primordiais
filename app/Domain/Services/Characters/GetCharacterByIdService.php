<?php

namespace App\Domain\Services\Characters;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityNewRepository;
use App\Infrastructure\Repositories\UserRepository;

class GetCharacterByIdService
{
    public function execute(int $characterId): array
    {
        $characterRepo =
            new CharacterRepository();

        $abilityRepo =
            new AbilityNewRepository();

        $charAbilityRepo =
            new CharacterAbilityRepository();

        $userRepo =
            new UserRepository();

        $character =
            $characterRepo->findById(
                $characterId
            );

        if (!$character) {
            throw new NotFoundException(
                'Personagem não encontrado.'
            );
        }

        $ownerNickname = null;

        if ($character->created_by) {
            $ownerNickname =
                $userRepo->findNicknameById(
                    $character->created_by
                );
        }

        $abilityIds =
            $charAbilityRepo
                ->getAbilitiesByCharacter(
                    $characterId
                );

        $abilities = [];

        foreach ($abilityIds as $abilityId) {
            $ability =
                $abilityRepo
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

        return [
            'character' =>
                $character->toArray(),

            'owner' =>
                $ownerNickname,

            'abilities' =>
                $abilities,
        ];
    }
}
