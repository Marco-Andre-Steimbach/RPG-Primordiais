<?php

namespace App\Domain\Services\Perks;

use App\Application\DTOs\Perks\CreatePerkDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Perk;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\RacePerkRepository;
use App\Infrastructure\Repositories\PerkAttributeRepository;
use App\Infrastructure\Repositories\PerkFlagRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
use App\Infrastructure\Repositories\PerkAbilityRepository;

class CreatePerkService
{
    private PerkRepository $perks;
    private RacePerkRepository $racePerks;
    private PerkAttributeRepository $attributes;
    private PerkFlagRepository $flags;
    private PerkElementTypeRepository $elements;
    private PerkAbilityRepository $abilities;

    public function __construct()
    {
        $this->perks = new PerkRepository();
        $this->racePerks = new RacePerkRepository();
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

        $this->racePerks->attachPerk(
            $dto->race_id,
            $perkId,
            $dto->required_level
        );

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

        if (
            $dto->type === 'active'
            && $dto->ability !== null
            && isset($dto->ability['name'], $dto->ability['description'])
        ) {
            $this->abilities->create($perkId, $dto->ability);
        }

        $perk = $this->perks->findById($perkId);

        if (!$perk) {
            throw new ValidationException('Erro ao carregar perk criado.');
        }

        return $perk;
    }
}
