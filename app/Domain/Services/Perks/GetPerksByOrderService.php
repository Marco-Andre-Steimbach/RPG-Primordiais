<?php

namespace App\Domain\Services\Perks;

use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\OrderPerkRepository;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\PerkAttributeRepository;
use App\Infrastructure\Repositories\PerkFlagRepository;
use App\Infrastructure\Repositories\PerkElementTypeRepository;
use App\Infrastructure\Repositories\PerkAbilityRepository;
use App\Core\Exceptions\NotFoundException;

class GetPerksByOrderService
{
    private OrderRepository $orders;
    private OrderPerkRepository $orderPerks;
    private PerkRepository $perks;
    private PerkAttributeRepository $attributes;
    private PerkFlagRepository $flags;
    private PerkElementTypeRepository $elements;
    private PerkAbilityRepository $abilities;

    public function __construct()
    {
        $this->orders = new OrderRepository();
        $this->orderPerks = new OrderPerkRepository();
        $this->perks = new PerkRepository();
        $this->attributes = new PerkAttributeRepository();
        $this->flags = new PerkFlagRepository();
        $this->elements = new PerkElementTypeRepository();
        $this->abilities = new PerkAbilityRepository();
    }

    public function execute(int $orderId): array
    {
        if (!$this->orders->findById($orderId)) {
            throw new NotFoundException('Ordem não encontrada.');
        }

        $links = $this->orderPerks->getPerksByOrder($orderId);

        $result = [];

        foreach ($links as $link) {
            $perk = $this->perks->findById((int) $link['perk_id']);

            if (!$perk) {
                continue;
            }

            $perk->race_id = null;
            $perk->order_id = $orderId;
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
