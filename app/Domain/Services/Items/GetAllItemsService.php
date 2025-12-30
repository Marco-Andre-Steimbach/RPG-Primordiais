<?php

namespace App\Domain\Services\Items;

use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class GetAllItemsService
{
    private ItemRepository $items;
    private ItemElementTypeRepository $elements;
    private ItemAbilityRepository $abilities;

    public function __construct()
    {
        $this->items = new ItemRepository();
        $this->elements = new ItemElementTypeRepository();
        $this->abilities = new ItemAbilityRepository();
    }

    public function execute(array $filters = []): array
    {
        $items = $this->items->findAllBasic();

        foreach ($items as &$item) {
            $item['element_types'] = $this->elements->getByItemId((int) $item['id']);
            $item['item_abilities'] = $this->abilities->getByItemId((int) $item['id']);
        }

        return $items;
    }
}
