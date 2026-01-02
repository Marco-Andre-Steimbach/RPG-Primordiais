<?php

namespace App\Domain\Services\Characters;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\CharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityRepository;

class GetCharacterByIdService
{
    public function execute(int $characterId): array
    {
        $characterRepo = new CharacterRepository();
        $abilityRepo = new AbilityRepository();
        $charAbilityRepo = new CharacterAbilityRepository();

        $character = $characterRepo->findById($characterId);

        if (!$character) {
            throw new NotFoundException('Personagem não encontrado.');
        }

        $abilityIds = $charAbilityRepo->getAbilitiesByCharacter($characterId);
        $abilities = [];

        foreach ($abilityIds as $abilityId) {
            $abilities[] = $abilityRepo->findByIdWithElements($abilityId);
        }

        return [
            'character' => $character->toArray(),
            'abilities' => $abilities,
        ];
    }
}
