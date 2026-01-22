<?php

namespace App\Domain\Services\Monsters;

use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterElementTypeRepository;
use App\Infrastructure\Repositories\MonsterAttackRepository;
use App\Infrastructure\Repositories\MonsterAttackLinkRepository;
use App\Infrastructure\Repositories\MonsterAbilityRepository;
use App\Infrastructure\Repositories\MonsterAbilityLinkRepository;
use App\Infrastructure\Repositories\MonsterAttackElementTypeRepository;
use App\Infrastructure\Repositories\MonsterAbilityElementTypeRepository;

class GetMonsterByIdService
{
    private MonsterRepository $monsters;
    private MonsterElementTypeRepository $elements;
    private MonsterAttackRepository $attacks;
    private MonsterAttackLinkRepository $attackLinks;
    private MonsterAbilityRepository $abilities;
    private MonsterAbilityLinkRepository $abilityLinks;
    private MonsterAttackElementTypeRepository $attackElements;
    private MonsterAbilityElementTypeRepository $abilityElements;

    public function __construct()
    {
        $this->monsters        = new MonsterRepository();
        $this->elements        = new MonsterElementTypeRepository();
        $this->attacks         = new MonsterAttackRepository();
        $this->attackLinks     = new MonsterAttackLinkRepository();
        $this->abilities       = new MonsterAbilityRepository();
        $this->abilityLinks    = new MonsterAbilityLinkRepository();
        $this->attackElements  = new MonsterAttackElementTypeRepository();
        $this->abilityElements = new MonsterAbilityElementTypeRepository();
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

        $attacks = array_map(function (array $attack) {
            $attack['element_types'] = $this->attackElements
                ->getByAttack((int) $attack['id']);

            return $attack;
        }, $attacks);

        $abilityIds = $this->abilityLinks->getAbilityIdsByMonster($monsterId);
        $abilities  = $this->abilities->findManyByIds($abilityIds);

        $abilities = array_map(function (array $ability) {
            $ability['element_types'] = $this->abilityElements
                ->getByAbility((int) $ability['id']);

            return $ability;
        }, $abilities);

        return [
            'id' => $monster->id,
            'name' => $monster->name,
            'description' => $monster->description,

            'xp_reward' => $monster->xp_reward,

            'weakness_damage_type_id' => $monster->weakness_damage_type_id,

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
