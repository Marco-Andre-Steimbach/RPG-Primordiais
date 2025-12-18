<?php

namespace App\Domain\Services\Perks;

use App\Application\DTOs\Perks\CreatePerkDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Perk;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\RacePerkRepository;
use App\Infrastructure\Repositories\OrderPerkRepository;
use App\Infrastructure\Repositories\PerkAttributeRepository;
use App\Infrastructure\Repositories\PerkFlagRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
use App\Infrastructure\Repositories\PerkAbilityRepository;

class CreatePerkService
{
    private PerkRepository $perks;
    private RacePerkRepository $racePerks;
    private OrderPerkRepository $orderPerks;
    private PerkAttributeRepository $attributes;
    private PerkFlagRepository $flags;
    private PerkElementTypeRepository $elements;
    private PerkAbilityRepository $abilities;

    public function __construct()
    {
        $this->perks = new PerkRepository();
        $this->racePerks = new RacePerkRepository();
        $this->orderPerks = new OrderPerkRepository();
        $this->attributes = new PerkAttributeRepository();
        $this->flags = new PerkFlagRepository();
        $this->elements = new PerkElementTypeRepository();
        $this->abilities = new PerkAbilityRepository();
    }

    public function execute(CreatePerkDTO $dto): Perk
    {
        if ($this->perks->existsByName($dto->name)) {
            throw new ConflictException('Já existe um perk com esse nome.');
        }

        $perkId = $this->perks->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'type' => $dto->type,
            'mana_cost' => $dto->mana_cost,
        ]);

        if (!$perkId) {
            throw new ValidationException('Falha ao criar perk.');
        }

        if ($dto->race_id !== null) {
            $this->racePerks->attachPerk(
                $dto->race_id,
                $perkId,
                $dto->required_level
            );
        }

        if ($dto->order_id !== null) {
            $this->orderPerks->attachPerk(
                $dto->order_id,
                $perkId,
                $dto->required_level
            );
        }

        foreach ($dto->attributes as $attribute) {
            $this->attributes->attach(
                $perkId,
                $attribute['name'],
                $attribute['value']
            );
        }

        foreach ($dto->flags as $flag) {
            $this->flags->attach($perkId, $flag);
        }

        foreach ($dto->element_types as $elementId) {
            $this->elements->attach($perkId, $elementId);
        }

        if ($dto->type === 'active' && $dto->hasAbility()) {
            $this->abilities->create($perkId, $dto->ability);
        }

        $perk = $this->perks->findById($perkId);

        if (!$perk) {
            throw new ValidationException('Erro ao carregar perk criado.');
        }

        $perk->race_id = $dto->race_id;
        $perk->order_id = $dto->order_id;
        $perk->required_level = $dto->required_level;
        $perk->attributes = $dto->attributes;
        $perk->flags = $dto->flags;
        $perk->element_types = $dto->element_types;
        $perk->ability = $dto->ability;

        return $perk;
    }
}
