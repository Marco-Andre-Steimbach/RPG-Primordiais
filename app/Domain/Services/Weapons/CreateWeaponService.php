<?php

namespace App\Domain\Services\Weapons;

use App\Application\DTOs\Weapons\CreateWeaponDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Weapon;
use App\Infrastructure\Repositories\ItemRepository;
use App\Infrastructure\Repositories\WeaponRepository;
use App\Infrastructure\Repositories\WeaponElementTypeRepository;

class CreateWeaponService
{
    private ItemRepository $items;
    private WeaponRepository $weapons;
    private WeaponElementTypeRepository $elements;

    public function __construct()
    {
        $this->items = new ItemRepository();
        $this->weapons = new WeaponRepository();
        $this->elements = new WeaponElementTypeRepository();
    }

    public function execute(CreateWeaponDTO $dto): Weapon
    {
        if (!$this->items->existsById($dto->item_id)) {
            throw new ValidationException('Item informado não existe.');
        }

        if ($this->weapons->existsByItemId($dto->item_id)) {
            throw new ConflictException('Este item já está registrado como arma.');
        }

        $weaponId = $this->weapons->create([
            'item_id' => $dto->item_id,
            'weapon_damage_type_id' => $dto->weapon_damage_type_id,
            'dice_formula' => $dto->dice_formula,
            'base_damage' => $dto->base_damage,
            'bonus_accuracy' => $dto->bonus_accuracy,
            'bonus_speed' => $dto->bonus_speed,
            'ammo_item_id' => $dto->ammo_item_id,
            'ammo_per_use' => $dto->ammo_per_use,
        ]);

        if (!$weaponId) {
            throw new ValidationException('Falha ao criar arma.');
        }

        foreach ($dto->element_types as $elementTypeId) {
            $this->elements->attach($weaponId, $elementTypeId);
        }

        $weapon = $this->weapons->findById($weaponId);

        if (!$weapon) {
            throw new ValidationException('Erro ao carregar arma criada.');
        }

        $weapon->element_types = $dto->element_types;

        return $weapon;
    }
}
