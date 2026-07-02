<?php

namespace App\Domain\Services\Elements;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterElementTypeRepository;
use App\Infrastructure\Repositories\ElementTypeRepository;

class GetMonsterElementsService
{
    private MonsterRepository $monsters;
    private MonsterElementTypeRepository $elements;
    private ElementTypeRepository $elementTypes;

    public function __construct()
    {
        $this->monsters = new MonsterRepository();
        $this->elements = new MonsterElementTypeRepository();
        $this->elementTypes = new ElementTypeRepository();
    }

    public function execute(int $monsterId): array
    {
        $monster = $this->monsters->findById($monsterId);

        if (!$monster) {
            throw new NotFoundException('Monstro não encontrado.');
        }

        $elementIds = $this->elements->getByMonster($monsterId);
        $result = [];

        foreach ($elementIds as $elementId) {
            $element = $this->elementTypes->findById((int) $elementId);

            if (!$element) {
                continue;
            }

            $result[] = [
                'id' => $element->id,
                'name' => $element->name,
            ];
        }

        return $result;
    }
}
