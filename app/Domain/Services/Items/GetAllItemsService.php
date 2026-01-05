<?php

namespace App\Domain\Services\Items;

use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\ArmorRepository;

class GetAllItemsService
{
    private ItemRepository $items;
    private ItemElementTypeRepository $elements;
    private ItemAbilityRepository $abilities;
    private WeaponRepository $weapons;
    private ArmorRepository $armors;

    public function __construct()
    {
        $this->items = new ItemRepository();
        $this->elements = new ItemElementTypeRepository();
        $this->abilities = new ItemAbilityRepository();
        $this->weapons = new WeaponRepository();
        $this->armors = new ArmorRepository();
    }

    public function execute(array $filters = []): array
    {
        $items = $this->items->findAllBasic();
        $result = [];

        foreach ($items as $item) {
            $itemId = (int) $item['id'];

            if ($this->weapons->existsByItemId($itemId)) {
                continue;
            }

            if ($this->armors->existsByItemId($itemId)) {
                continue;
            }

            $item['element_types'] = $this->elements->getByItemId($itemId);
            $item['item_abilities'] = $this->abilities->getByItemId($itemId);

            $result[] = $item;
        }

        return $result;
    }
}
