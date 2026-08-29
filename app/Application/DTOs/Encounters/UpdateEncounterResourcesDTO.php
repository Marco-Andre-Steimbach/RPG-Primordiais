<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\UpdateEncounterResourcesDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Domain\Services\Campaigns\GetCampaignCharacterSheetService;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\EncounterPlayerStatsRepository;
use App\Infrastructure\Repositories\CampaignCharacterResourcesRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;

class UpdateEncounterResourcesService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;
    private EncounterMonsterRepository $encounterMonsters;
    private EncounterPlayerRepository $encounterPlayers;
    private EncounterPlayerStatsRepository $encounterPlayerStats;
    private CampaignCharacterResourcesRepository $characterResources;
    private CampaignCharacterRepository $campaignCharacters;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns = new CampaignRepository();
        $this->encounterMonsters = new EncounterMonsterRepository();
        $this->encounterPlayers = new EncounterPlayerRepository();
        $this->encounterPlayerStats = new EncounterPlayerStatsRepository();
        $this->characterResources = new CampaignCharacterResourcesRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
    }

    public function execute(
        UpdateEncounterResourcesDTO $dto,
        int $userId
    ): array {
        if ($dto->type === 'monster') {
            return $this->updateMonster($dto, $userId);
        }

        return $this->updatePlayer($dto, $userId);
    }

    private function updateMonster(
        UpdateEncounterResourcesDTO $dto,
        int $userId
    ): array {
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
            ->findById(
                (int) $this->value(
                    $encounterMonster,
                    'encounter_id'
                )
            );

        if (!$encounter) {
            throw new ValidationException(
                'Encontro inválido.'
            );
        }

        $this->validatePermission(
            (int) $encounter->campaign_id,
            $userId
        );

        $currentHp = (int) $this->value(
            $encounterMonster,
            'current_hp',
            0
        );

        $maxHpValue = $this->value(
            $encounterMonster,
            'max_hp'
        );

        $maxHp = $maxHpValue !== null
            ? (int) $maxHpValue
            : null;

        if ($dto->mode === 'delta') {
            $newCurrentHp = $currentHp + $dto->value;
        } else {
            $newCurrentHp = $dto->value;
        }

        $newCurrentHp = max(
            0,
            $newCurrentHp
        );

        if ($maxHp !== null) {
            $newCurrentHp = min(
                $newCurrentHp,
                $maxHp
            );
        }

        $this->encounterMonsters->updateHp(
            $dto->encounter_monster_id,
            $newCurrentHp
        );

        return [
            'type' => 'monster',
            'encounter_monster_id'
                => $dto->encounter_monster_id,

            'current_hp'
                => $newCurrentHp,

            'max_hp'
                => $maxHp,
        ];
    }

    private function updatePlayer(
        UpdateEncounterResourcesDTO $dto,
        int $userId
    ): array {
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
            (int) $encounter->campaign_id,
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

        $campaignCharacter = $this->campaignCharacters
            ->findById($campaignCharacterId);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem da campanha não encontrado.'
            );
        }

        $characterId = (int) $this->value(
            $campaignCharacter,
            'character_id'
        );

        if ($characterId <= 0) {
            throw new ValidationException(
                'Personagem inválido.'
            );
        }

        $sheetService =
            new GetCampaignCharacterSheetService();

        $sheet = $sheetService->execute(
            campaignId: (int) $encounter->campaign_id,
            characterId: $characterId
        );

        $newMaxHp = max(
            1,
            (int) ($sheet['base']['hp_max'] ?? 1)
        );

        $newMaxMana = max(
            0,
            (int) ($sheet['base']['mana_max'] ?? 0)
        );

        $newMaxSanity = max(
            0,
            (int) ($sheet['base']['sanity']['max'] ?? 0)
        );

        $oldMaxHp = (int) $this->value(
            $stats,
            'max_hp',
            $newMaxHp
        );

        $oldMaxMana = (int) $this->value(
            $stats,
            'max_mana',
            $newMaxMana
        );

        $oldMaxSanity = (int) $this->value(
            $stats,
            'max_sanity',
            $newMaxSanity
        );

        $currentHp = (int) $this->value(
            $stats,
            'current_hp',
            0
        );

        $currentMana = (int) $this->value(
            $stats,
            'current_mana',
            0
        );

        $currentSanity = (int) $this->value(
            $stats,
            'current_sanity',
            0
        );

        $currentHp = $this->synchronizeCurrent(
            $currentHp,
            $oldMaxHp,
            $newMaxHp
        );

        $currentMana = $this->synchronizeCurrent(
            $currentMana,
            $oldMaxMana,
            $newMaxMana
        );

        $currentSanity = $this->synchronizeCurrent(
            $currentSanity,
            $oldMaxSanity,
            $newMaxSanity
        );

        if ($dto->resource === 'hp') {
            $currentHp = $this->applyOperation(
                current: $currentHp,
                max: $newMaxHp,
                mode: $dto->mode,
                value: $dto->value
            );
        }

        if ($dto->resource === 'mana') {
            $currentMana = $this->applyOperation(
                current: $currentMana,
                max: $newMaxMana,
                mode: $dto->mode,
                value: $dto->value
            );
        }

        if ($dto->resource === 'sanity') {
            $currentSanity = $this->applyOperation(
                current: $currentSanity,
                max: $newMaxSanity,
                mode: $dto->mode,
                value: $dto->value
            );
        }

        $statsUpdates = [
            'current_hp' => $currentHp,
            'max_hp' => $newMaxHp,

            'current_mana' => $currentMana,
            'max_mana' => $newMaxMana,

            'current_sanity' => $currentSanity,
            'max_sanity' => $newMaxSanity,
        ];

        $this->encounterPlayerStats
            ->updateStats(
                $dto->encounter_player_id,
                $statsUpdates
            );

        $resourceUpdates = [
            'current_hp' => $currentHp,
            'current_mana' => $currentMana,
            'current_sanity' => $currentSanity,
        ];

        $resources = $this->characterResources
            ->findByCampaignCharacterId(
                $campaignCharacterId
            );

        if (!$resources) {
            $this->characterResources->create([
                'campaign_character_id'
                    => $campaignCharacterId,

                'current_hp'
                    => $currentHp,

                'current_mana'
                    => $currentMana,

                'current_sanity'
                    => $currentSanity,
            ]);
        } else {
            $this->characterResources
                ->updateByCampaignCharacterId(
                    $campaignCharacterId,
                    $resourceUpdates
                );
        }

        return [
            'type' => 'player',

            'encounter_player_id'
                => $dto->encounter_player_id,

            'current_hp'
                => $currentHp,

            'max_hp'
                => $newMaxHp,

            'current_mana'
                => $currentMana,

            'max_mana'
                => $newMaxMana,

            'current_sanity'
                => $currentSanity,

            'max_sanity'
                => $newMaxSanity,
        ];
    }

    private function synchronizeCurrent(
        int $current,
        int $oldMax,
        int $newMax
    ): int {
        $difference = $newMax - $oldMax;

        return max(
            0,
            min(
                $newMax,
                $current + $difference
            )
        );
    }

    private function applyOperation(
        int $current,
        int $max,
        string $mode,
        int $value
    ): int {
        if ($mode === 'delta') {
            $value = $current + $value;
        }

        return max(
            0,
            min(
                $max,
                $value
            )
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
