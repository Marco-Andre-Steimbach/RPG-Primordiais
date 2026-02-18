<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\AddMonsterToEncounterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Domain\Models\EncounterMonster;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\MonsterRepository;

class AddMonsterToEncounterService
{
    private EncounterRepository $encounters;
    private EncounterMonsterRepository $encounterMonsters;
    private CampaignRepository $campaigns;
    private MonsterRepository $monsters;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->encounterMonsters = new EncounterMonsterRepository();
        $this->campaigns = new CampaignRepository();
        $this->monsters = new MonsterRepository();
    }

    public function execute(
        AddMonsterToEncounterDTO $dto,
        int $userId
    ): array {

        $encounter = $this->encounters->findById($dto->encounter_id);

        if (!$encounter) {
            throw new ValidationException(
                'Encontro inválido.',
                ['encounter_id' => ['Encontro não encontrado.']]
            );
        }

        $campaign = $this->campaigns->findById($encounter->campaign_id);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                ['campaign_id' => ['Campanha não encontrada.']]
            );
        }

        if ($campaign->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para modificar este encontro.'
            );
        }

        $monster = $this->monsters->findById($dto->monster_id);

        if (!$monster) {
            throw new ValidationException(
                'Monstro inválido.',
                ['monster_id' => ['Monstro não encontrado.']]
            );
        }

        $inserted = [];

        for ($i = 0; $i < $dto->quantity; $i++) {

            $currentHp = $monster->base_hp;

            $id = $this->encounterMonsters->create([
                'encounter_id' => $dto->encounter_id,
                'monster_id'   => $dto->monster_id,
                'monster_level'=> $dto->monster_level,
                'current_hp'   => $currentHp,
            ]);

            if (!$id) {
                throw new ValidationException('Erro ao inserir monstro no encontro.');
            }

            $instance = $this->encounterMonsters->findById($id);

            if ($instance) {
                $inserted[] = $instance;
            }
        }

        return $inserted;
    }
}
