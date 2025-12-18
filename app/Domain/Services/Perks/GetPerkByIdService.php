<?php

namespace App\Domain\Services\Perks;

use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\RacePerkRepository;
use App\Infrastructure\Repositories\OrderPerkRepository;
use App\Infrastructure\Repositories\PerkAttributeRepository;
use App\Infrastructure\Repositories\PerkFlagRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
use App\Infrastructure\Repositories\PerkAbilityRepository;
use App\Core\Exceptions\NotFoundException;

class GetPerkByIdService
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

    public function execute(int $perkId)
    {
        $perk = $this->perks->findById($perkId);

        if (!$perk) {
            throw new NotFoundException('Perk não encontrado.');
        }

        $raceLink = $this->racePerks->findByPerkId($perkId);
        $orderLink = $this->orderPerks->findByPerkId($perkId);

        if ($raceLink) {
            $perk->race_id = (int) $raceLink['race_id'];
            $perk->order_id = null;
            $perk->required_level = (int) $raceLink['required_level'];
        }

        if ($orderLink) {
            $perk->race_id = null;
            $perk->order_id = (int) $orderLink['order_id'];
            $perk->required_level = (int) $orderLink['required_level'];
        }

        $perk->attributes = $this->attributes->getByPerk($perkId);
        $perk->flags = $this->flags->getByPerk($perkId);
        $perk->element_types = $this->elements->getElementTypesByPerk($perk->id);
        $perk->ability = $this->abilities->findByPerk($perk->id) ?? [];

        return $perk;
    }
}
