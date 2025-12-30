<?php

namespace App\Domain\Services\Characters;

use App\Infrastructure\Repositories\CharacterRepository;

class GetCharactersByUserService
{
    public function execute(int $userId): array
    {
        $repo = new CharacterRepository();

        return $repo->findByUser($userId);
    }
}
