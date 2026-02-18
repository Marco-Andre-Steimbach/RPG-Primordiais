<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\UpdateEncounterMonsterHpDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;

class UpdateEncounterMonsterHpService
{
    private EncounterMonsterRepository $encounterMonsters;
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;

    public function __construct()
    {
        $this->encounterMonsters = new EncounterMonsterRepository();
        $this->encounters        = new EncounterRepository();
        $this->campaigns         = new CampaignRepository();
    }

    public function execute(
        UpdateEncounterMonsterHpDTO $dto,
        int $userId
    ): void {

        $encounterMonster = $this->encounterMonsters->findById($dto->encounter_monster_id);

        if (!$encounterMonster) {
            throw new ValidationException(
                'Registro de monstro inválido.',
                ['encounter_monster_id' => ['Monstro não encontrado no encontro.']]
            );
        }

        $encounter = $this->encounters->findById($encounterMonster->encounter_id);

        if (!$encounter) {
            throw new ValidationException('Encontro inválido.');
        }

        $campaign = $this->campaigns->findById($encounter->campaign_id);

        if (!$campaign) {
            throw new ValidationException('Campanha inválida.');
        }

        if ($campaign->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para alterar este encontro.'
            );
        }

        $this->encounterMonsters->updateHp(
            $dto->encounter_monster_id,
            $dto->current_hp
        );
    }
}
