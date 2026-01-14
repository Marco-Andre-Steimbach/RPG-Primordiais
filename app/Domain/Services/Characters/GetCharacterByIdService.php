<?php

namespace App\Domain\Services\Characters;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityRepository;
use App\Infrastructure\Repositories\UserRepository;

class GetCharacterByIdService
{
    public function execute(int $characterId): array
    {
        $characterRepo = new CharacterRepository();
        $abilityRepo = new AbilityRepository();
        $charAbilityRepo = new CharacterAbilityRepository();
        $userRepo = new UserRepository();

        $character = $characterRepo->findById($characterId);

        if (!$character) {
            throw new NotFoundException('Personagem não encontrado.');
        }

        $ownerNickname = null;

        if ($character->created_by) {
            $ownerNickname = $userRepo->findNicknameById($character->created_by);
        }


        $abilityIds = $charAbilityRepo->getAbilitiesByCharacter($characterId);
        $abilities = [];

        foreach ($abilityIds as $abilityId) {
            $abilities[] = $abilityRepo->findByIdWithElements($abilityId);
        }

        return [
            'character' => $character->toArray(),
            'owner' => $ownerNickname,
            'abilities' => $abilities,
        ];
    }
}
