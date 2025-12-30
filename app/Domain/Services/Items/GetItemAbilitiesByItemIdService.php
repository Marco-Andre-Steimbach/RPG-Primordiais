<?php

namespace App\Domain\Services\Items;

use App\Core\Exceptions\NotFoundException;
use App\Domain\Models\ItemAbility;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class GetItemAbilitiesByItemIdService
{
    private ItemRepository $items;
    private ItemAbilityRepository $abilities;

    public function __construct()
    {
        $this->items = new ItemRepository();
        $this->abilities = new ItemAbilityRepository();
    }

    public function execute(int $itemId): array
    {
        if (!$this->items->existsById($itemId)) {
            throw new NotFoundException('Item não encontrado.');
        }

        $abilityIds = $this->abilities->getByItemId($itemId);

        if (empty($abilityIds)) {
            return [];
        }

        $abilities = [];

        foreach ($abilityIds as $abilityId) {
            $ability = $this->abilities->findById($abilityId);

            if ($ability instanceof ItemAbility) {
                $abilities[] = $ability->toArray();
            }
        }

        return $abilities;
    }
}
