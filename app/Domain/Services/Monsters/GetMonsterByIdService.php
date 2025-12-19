<?php

namespace App\Domain\Services\Monsters;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterElementTypeRepository;
use App\Infrastructure\Repositories\MonsterAttackRepository;
use App\Infrastructure\Repositories\MonsterAttackLinkRepository;
use App\Infrastructure\Repositories\MonsterAbilityRepository;
use App\Infrastructure\Repositories\MonsterAbilityLinkRepository;

class GetMonsterByIdService
{
    private MonsterRepository $monsters;
    private MonsterElementTypeRepository $elements;
    private MonsterAttackRepository $attacks;
    private MonsterAttackLinkRepository $attackLinks;
    private MonsterAbilityRepository $abilities;
    private MonsterAbilityLinkRepository $abilityLinks;

    public function __construct()
    {
        $this->monsters     = new MonsterRepository();
        $this->elements     = new MonsterElementTypeRepository();
        $this->attacks      = new MonsterAttackRepository();
        $this->attackLinks  = new MonsterAttackLinkRepository();
        $this->abilities    = new MonsterAbilityRepository();
        $this->abilityLinks = new MonsterAbilityLinkRepository();
    }

    public function execute(int $monsterId): array
    {
        $monster = $this->monsters->findById($monsterId);

        if (!$monster) {
            throw new NotFoundException('Monstro não encontrado.');
        }

        $elementTypes = $this->elements->getByMonster($monsterId);

        $attackIds = $this->attackLinks->getAttackIdsByMonster($monsterId);
        $attacks   = $this->attacks->findManyByIds($attackIds);

        $abilityIds = $this->abilityLinks->getAbilityIdsByMonster($monsterId);
        $abilities  = $this->abilities->findManyByIds($abilityIds);

        return [
            'id' => $monster->id,
            'name' => $monster->name,
            'description' => $monster->description,

            'stats' => [
                'hp' => $monster->base_hp,
                'ac' => $monster->base_ac,
                'speed' => $monster->base_speed,
                'actions_per_turn' => $monster->actions_per_turn,

                'str' => $monster->base_str,
                'dex' => $monster->base_dex,
                'con' => $monster->base_con,
                'wis' => $monster->base_wis,
                'int' => $monster->base_int,
            ],

            'element_types' => $elementTypes,
            'attacks' => $attacks,
            'abilities' => $abilities,
        ];
    }
}
