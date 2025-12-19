<?php

namespace App\Domain\Services\Items;

use App\Application\DTOs\Items\CreateItemDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Item;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ItemElementTypeRepository;
use App\Infrastructure\Repositories\ItemAbilityRepository;

class CreateItemService
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

    public function execute(CreateItemDTO $dto): Item
    {
        if ($this->items->existsByName($dto->name)) {
            throw new ConflictException('Já existe um item com esse nome.');
        }

        $itemId = $this->items->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'value' => $dto->value,
        ]);

        if (!$itemId) {
            throw new ValidationException('Falha ao criar item.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach($itemId, $elementTypeId);
        }

        foreach ($dto->item_abilities as $abilityId) {
            $this->abilities->attach($itemId, $abilityId);
        }

        $item = $this->items->findById($itemId);

        if (!$item) {
            throw new ValidationException('Erro ao carregar item criado.');
        }

        $item->element_types = $dto->element_types;
        $item->item_abilities = $dto->item_abilities;

        return $item;
    }
}
