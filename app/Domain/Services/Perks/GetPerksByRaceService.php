<?php

namespace App\Domain\Services\Perks;

use App\Infrastructure\Repositories\RaceRepository;
use App\Infrastructure\Repositories\RacePerkRepository;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\PerkAttributeRepository;
use App\Infrastructure\Repositories\PerkFlagRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
use App\Infrastructure\Repositories\PerkAbilityRepository;
use App\Core\Exceptions\NotFoundException;

class GetPerksByRaceService
{
    private RaceRepository $races;
    private RacePerkRepository $racePerks;
    private PerkRepository $perks;
    private PerkAttributeRepository $attributes;
    private PerkFlagRepository $flags;
    private PerkElementTypeRepository $elements;
    private PerkAbilityRepository $abilities;

    public function __construct()
    {
        $this->races = new RaceRepository();
        $this->racePerks = new RacePerkRepository();
        $this->perks = new PerkRepository();
        $this->attributes = new PerkAttributeRepository();
        $this->flags = new PerkFlagRepository();
        $this->elements = new PerkElementTypeRepository();
        $this->abilities = new PerkAbilityRepository();
    }

    public function execute(int $raceId): array
    {
        if (!$this->races->findById($raceId)) {
            throw new NotFoundException('Raça não encontrada.');
        }

        $links = $this->racePerks->getPerksByRace($raceId);

        $result = [];

        foreach ($links as $link) {
            $perk = $this->perks->findById((int) $link['perk_id']);

            if (!$perk) {
                continue;
            }

            $perk->race_id = $raceId;
            $perk->order_id = null;
            $perk->required_level = (int) $link['required_level'];
            $perk->attributes = $this->attributes->getByPerk($perk->id);
            $perk->flags = $this->flags->getByPerk($perk->id);
            $perk->element_types = $this->elements->getElementTypesByPerk($perk->id);
            $perk->ability = $this->abilities->findByPerk($perk->id) ?? [];

            $result[] = $perk;
        }

        return $result;
    }
}
