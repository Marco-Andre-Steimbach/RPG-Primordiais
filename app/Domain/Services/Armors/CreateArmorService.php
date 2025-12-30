<?php

namespace App\Domain\Services\Armors;

use App\Application\DTOs\Armors\CreateArmorDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Armor;
use App\Infrastructure\Repositories\ArmorRepository;
use App\Infrastructure\Repositories\ArmorElementTypeRepository;
use App\Infrastructure\Repositories\ArmorArmorAbilityRepository;
use App\Infrastructure\Repositories\ArmorSlotRepository;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\ArmorAbilityRepository;

class CreateArmorService
{
    private ArmorRepository $armors;
    private ArmorElementTypeRepository $elements;
    private ArmorArmorAbilityRepository $abilities;
    private ArmorSlotRepository $slots;
    private ItemRepository $items;
    private ArmorAbilityRepository $armorAbilities;

    public function __construct()
    {
        $this->armors = new ArmorRepository();
        $this->elements = new ArmorElementTypeRepository();
        $this->abilities = new ArmorArmorAbilityRepository();
        $this->slots = new ArmorSlotRepository();
        $this->items = new ItemRepository();
        $this->armorAbilities = new ArmorAbilityRepository();
    }

    public function execute(CreateArmorDTO $dto): Armor
    {
        if (!$this->items->existsById($dto->item_id)) {
            throw new ValidationException(
                'Item inválido.',
                ['item_id' => ['Item não encontrado.']]
            );
        }

        if ($this->armors->existsByItemId($dto->item_id)) {
            throw new ConflictException('Este item já está vinculado a uma armadura.');
        }

        $slot = $this->slots->findById($dto->armor_slot_id);

        if (!$slot) {
            throw new ValidationException(
                'Slot inválido.',
                ['armor_slot_id' => ['Slot não encontrado.']]
            );
        }

        $armorId = $this->armors->create([
            'item_id' => $dto->item_id,
            'armor_slot_id' => $dto->armor_slot_id,
            'armor_class_bonus' => $dto->armor_class_bonus,
            'min_strength_required' => $dto->min_strength_required,
            'speed_penalty' => $dto->speed_penalty,
        ]);

        if (!$armorId) {
            throw new ValidationException('Falha ao criar armadura.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach($armorId, $elementTypeId);
        }

        foreach ($dto->armor_abilities as $abilityId) {
            if (!$this->armorAbilities->existsById($abilityId)) {
                throw new ValidationException(
                    'Habilidade inválida.',
                    ['armor_abilities' => ["Habilidade {$abilityId} não encontrada."]]
                );
            }

            $this->abilities->attach($armorId, $abilityId);
        }

        $armor = $this->armors->findById($armorId);

        if (!$armor) {
            throw new ValidationException('Erro ao carregar armadura criada.');
        }

        $armor->element_types = $dto->element_types;
        $armor->armor_abilities = $dto->armor_abilities;

        return $armor;
    }
}
