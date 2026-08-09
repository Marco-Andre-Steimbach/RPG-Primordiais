<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\UpdateEncounterResourcesDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\EncounterPlayerStatsRepository;
use App\Infrastructure\Repositories\CampaignCharacterResourcesRepository;

class UpdateEncounterResourcesService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;
    private EncounterMonsterRepository $encounterMonsters;
    private EncounterPlayerRepository $encounterPlayers;
    private EncounterPlayerStatsRepository $encounterPlayerStats;
    private CampaignCharacterResourcesRepository $characterResources;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns = new CampaignRepository();
        $this->encounterMonsters = new EncounterMonsterRepository();
        $this->encounterPlayers = new EncounterPlayerRepository();
        $this->encounterPlayerStats = new EncounterPlayerStatsRepository();
        $this->characterResources = new CampaignCharacterResourcesRepository();
    }

    public function execute(
        UpdateEncounterResourcesDTO $dto,
        int $userId
    ): void {
        if ($dto->type === 'monster') {
            $this->updateMonster($dto, $userId);
            return;
        }

        $this->updatePlayer($dto, $userId);
    }

    private function updateMonster(
        UpdateEncounterResourcesDTO $dto,
        int $userId
    ): void {
        $encounterMonster = $this->encounterMonsters
            ->findById($dto->encounter_monster_id);

        if (!$encounterMonster) {
            throw new ValidationException(
                'Registro de monstro inválido.',
                [
                    'encounter_monster_id' => [
                        'Monstro não encontrado no encontro.'
                    ]
                ]
            );
        }

        $encounter = $this->encounters
            ->findById($encounterMonster->encounter_id);

        if (!$encounter) {
            throw new ValidationException(
                'Encontro inválido.'
            );
        }

        $this->validatePermission(
            $encounter->campaign_id,
            $userId
        );

        $this->encounterMonsters->updateHp(
            $dto->encounter_monster_id,
            $dto->current_hp
        );
    }

    private function updatePlayer(
        UpdateEncounterResourcesDTO $dto,
        int $userId
    ): void {
        $encounterPlayer = $this->encounterPlayers
            ->findById($dto->encounter_player_id);

        if (!$encounterPlayer) {
            throw new ValidationException(
                'Registro de player inválido.',
                [
                    'encounter_player_id' => [
                        'Player não encontrado no encontro.'
                    ]
                ]
            );
        }

        $encounterId = (int) $this->value(
            $encounterPlayer,
            'encounter_id'
        );

        $campaignCharacterId = (int) $this->value(
            $encounterPlayer,
            'campaign_character_id'
        );

        $encounter = $this->encounters
            ->findById($encounterId);

        if (!$encounter) {
            throw new ValidationException(
                'Encontro inválido.'
            );
        }

        $this->validatePermission(
            $encounter->campaign_id,
            $userId
        );

        $stats = $this->encounterPlayerStats
            ->findByEncounterPlayerId(
                $dto->encounter_player_id
            );

        if (!$stats) {
            throw new ValidationException(
                'Recursos do player não encontrados.'
            );
        }

        $updates = [];

        if ($dto->current_hp !== null) {
            $updates['current_hp'] = $dto->current_hp;
        }

        if ($dto->current_mana !== null) {
            $updates['current_mana'] = $dto->current_mana;
        }

        if ($dto->current_sanity !== null) {
            $updates['current_sanity'] = $dto->current_sanity;
        }

        if (empty($updates)) {
            throw new ValidationException(
                'Nenhum recurso informado para atualização.'
            );
        }

        $this->encounterPlayerStats
            ->updateStats(
                $dto->encounter_player_id,
                $updates
            );

        $resources = $this->characterResources
            ->findByCampaignCharacterId(
                $campaignCharacterId
            );

        if (!$resources) {
            $this->characterResources->create([
                'campaign_character_id' => $campaignCharacterId,

                'current_hp' => $dto->current_hp
                    ?? (int) $this->value($stats, 'current_hp'),

                'current_mana' => $dto->current_mana
                    ?? (int) $this->value($stats, 'current_mana'),

                'current_sanity' => $dto->current_sanity
                    ?? (int) $this->value($stats, 'current_sanity'),
            ]);

            return;
        }

        $this->characterResources
            ->updateByCampaignCharacterId(
                $campaignCharacterId,
                $updates
            );
    }

    private function validatePermission(
        int $campaignId,
        int $userId
    ): void {
        $campaign = $this->campaigns
            ->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.'
            );
        }

        if ($campaign->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para alterar este encontro.'
            );
        }
    }

    private function value(
        array|object|null $source,
        string $key,
        mixed $default = null
    ): mixed {
        if ($source === null) {
            return $default;
        }

        if (is_array($source)) {
            return $source[$key] ?? $default;
        }

        return $source->{$key} ?? $default;
    }
}
