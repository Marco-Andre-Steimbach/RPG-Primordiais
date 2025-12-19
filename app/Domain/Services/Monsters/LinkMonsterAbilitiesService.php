<?php

namespace App\Domain\Services\Monsters;

use App\Application\DTOs\Monsters\LinkMonsterAbilitiesDTO;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ConflictException;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterAbilityRepository;
use App\Infrastructure\Repositories\MonsterAbilityLinkRepository;

class LinkMonsterAbilitiesService
{
    private MonsterRepository $monsters;
    private MonsterAbilityRepository $abilities;
    private MonsterAbilityLinkRepository $links;

    public function __construct()
    {
        $this->monsters  = new MonsterRepository();
        $this->abilities = new MonsterAbilityRepository();
        $this->links     = new MonsterAbilityLinkRepository();
    }

    public function execute(LinkMonsterAbilitiesDTO $dto): void
    {
        if (!$this->monsters->findById($dto->monster_id)) {
            throw new NotFoundException('Monstro não encontrado.');
        }

        foreach ($dto->ability_ids as $abilityId) {

            if (!$this->abilities->findById($abilityId)) {
                throw new NotFoundException(
                    "Habilidade {$abilityId} não encontrada."
                );
            }

            if ($this->links->exists($dto->monster_id, $abilityId)) {
                throw new ConflictException(
                    "Habilidade {$abilityId} já está vinculada a este monstro."
                );
            }

            $this->links->attach(
                $dto->monster_id,
                $abilityId
            );
        }
    }
}
