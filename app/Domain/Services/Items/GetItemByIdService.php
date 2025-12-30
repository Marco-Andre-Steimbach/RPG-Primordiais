<?php

namespace App\Domain\Services\Items;

use App\Core\Exceptions\NotFoundException;
use App\Domain\Models\Item;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class GetItemByIdService
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

    public function execute(int $itemId): Item
    {
        $item = $this->items->findById($itemId);

        if (!$item) {
            throw new NotFoundException('Item não encontrado.');
        }

        $item->element_types = $this->elements->getByItemId($itemId);
        $item->item_abilities = $this->abilities->getByItemId($itemId);

        return $item;
    }
}
