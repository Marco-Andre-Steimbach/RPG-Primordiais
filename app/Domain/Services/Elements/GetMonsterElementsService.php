<?php

namespace App\Domain\Services\Elements;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterElementTypeRepository;

class GetMonsterElementsService
{
    private MonsterRepository $monsters;
    private MonsterElementTypeRepository $elements;

    public function __construct()
    {
        $this->monsters = new MonsterRepository();
        $this->elements = new MonsterElementTypeRepository();
    }

    public function execute(int $monsterId): array
    {
        $monster = $this->monsters->findById($monsterId);

        if (!$monster) {
            throw new NotFoundException('Monstro não encontrado.');
        }

        return $this->elements->getByMonster($monsterId);
    }
}
