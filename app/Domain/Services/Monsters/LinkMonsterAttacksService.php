<?php

namespace App\Domain\Services\Monsters;

use App\Application\DTOs\Monsters\LinkMonsterAttacksDTO;
use App\Core\Exceptions\NotFoundException;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\MonsterAttackRepository;
use App\Infrastructure\Repositories\MonsterAttackLinkRepository;

class LinkMonsterAttacksService
{
    private MonsterRepository $monsters;
    private MonsterAttackRepository $attacks;
    private MonsterAttackLinkRepository $links;

    public function __construct()
    {
        $this->monsters = new MonsterRepository();
        $this->attacks  = new MonsterAttackRepository();
        $this->links    = new MonsterAttackLinkRepository();
    }

    public function execute(LinkMonsterAttacksDTO $dto): void
    {
        if (!$this->monsters->existsById($dto->monster_id)) {
            throw new NotFoundException('Monstro não encontrado.');
        }

        foreach ($dto->attack_ids as $attackId) {

            if (!$this->attacks->existsById($attackId)) {
                throw new NotFoundException("Ataque {$attackId} não encontrado.");
            }

            if ($this->links->exists($dto->monster_id, $attackId)) {
                continue;
            }

            $this->links->attach(
                $dto->monster_id,
                $attackId
            );
        }
    }
}
