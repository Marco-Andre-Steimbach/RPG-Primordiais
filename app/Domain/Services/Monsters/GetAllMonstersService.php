<?php

namespace App\Domain\Services\Monsters;

use App\Infrastructure\Repositories\MonsterRepository;

class GetAllMonstersService
{
    private MonsterRepository $monsters;

    public function __construct()
    {
        $this->monsters = new MonsterRepository();
    }

    public function execute(array $filters = []): array
    {
        $elementTypes = [];

        if (!empty($filters['element_types'])) {
            $elementTypes = array_filter(
                array_map('intval', explode(',', $filters['element_types']))
            );
        }

        if (!empty($elementTypes)) {
            return $this->monsters->findAllByElementTypes($elementTypes);
        }

        return $this->monsters->findAllBasic();
    }
}
