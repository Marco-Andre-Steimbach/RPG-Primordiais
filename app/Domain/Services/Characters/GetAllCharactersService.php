<?php

namespace App\Domain\Services\Characters;

use App\Infrastructure\Repositories\CharacterRepository;

class GetAllCharactersService
{
    public function execute(): array
    {
        $repo = new CharacterRepository();

        return $repo->findAllBasic();
    }
}
