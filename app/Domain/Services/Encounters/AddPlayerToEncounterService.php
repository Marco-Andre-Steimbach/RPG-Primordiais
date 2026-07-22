<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\AddPlayerToEncounterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Domain\Services\Campaigns\GetCampaignCharacterSheetService;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\CampaignCharacterResourcesRepository;
use App\Infrastructure\Repositories\EncounterPlayerStatsRepository;

class AddPlayerToEncounterService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;
    private CampaignCharacterRepository $campaignCharacters;
    private EncounterPlayerRepository $encounterPlayers;
    private CampaignCharacterResourcesRepository $characterResources;
    private EncounterPlayerStatsRepository $encounterPlayerStats;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns = new CampaignRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->encounterPlayers = new EncounterPlayerRepository();
        $this->characterResources = new CampaignCharacterResourcesRepository();
        $this->encounterPlayerStats = new EncounterPlayerStatsRepository();
    }

    public function execute(
        AddPlayerToEncounterDTO $dto,
        int $userId
    ): void {
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

        $campaignCharacter = $this->campaignCharacters
            ->findById($dto->campaign_character_id);

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                [
                    'campaign_character_id' => [
                        'Personagem não encontrado.'
                    ]
                ]
            );
        }

        if (
            (int) $campaignCharacter['campaign_id']
            !== $encounter->campaign_id
        ) {
            throw new ValidationException(
                'Personagem não pertence a esta campanha.'
            );
        }

        $sheetService = new GetCampaignCharacterSheetService();

        $sheet = $sheetService->execute(
            campaignId: (int) $campaignCharacter['campaign_id'],
            characterId: (int) $campaignCharacter['character_id']
        );

        $maxHp = max(
            1,
            (int) ($sheet['base']['hp_max'] ?? 1)
        );

        $maxMana = max(
            0,
            (int) ($sheet['base']['mana_max'] ?? 0)
        );

        $maxSanity = max(
            0,
            (int) ($sheet['base']['sanity_max'] ?? 0)
        );

        $sheetCurrentSanity = max(
            0,
            (int) ($sheet['base']['sanity_current'] ?? $maxSanity)
        );

        $armorClass = max(
            0,
            (int) ($sheet['derived']['armor_class'] ?? 0)
        );

        $resources = $this->characterResources
            ->findByCampaignCharacterId($dto->campaign_character_id);

        if (!$resources) {
            $currentHp = $maxHp;
            $currentMana = $maxMana;
            $currentSanity = min(
                $sheetCurrentSanity,
                $maxSanity
            );

            $this->characterResources->create([
                'campaign_character_id' => $dto->campaign_character_id,
                'current_hp' => $currentHp,
                'current_mana' => $currentMana,
                'current_sanity' => $currentSanity,
            ]);
        } else {
            $currentHp = min(
                max(0, (int) $resources['current_hp']),
                $maxHp
            );

            $currentMana = min(
                max(0, (int) $resources['current_mana']),
                $maxMana
            );

            $currentSanity = min(
                max(0, (int) $resources['current_sanity']),
                $maxSanity
            );
        }

        $encounterPlayerId = $this->encounterPlayers->create([
            'encounter_id' => $dto->encounter_id,
            'campaign_character_id' => $dto->campaign_character_id,
        ]);

        if ($encounterPlayerId <= 0) {
            throw new ValidationException(
                'Erro ao adicionar personagem ao encontro.'
            );
        }

        $statsId = $this->encounterPlayerStats->create([
            'encounter_player_id' => $encounterPlayerId,
            'current_hp' => $currentHp,
            'max_hp' => $maxHp,
            'current_mana' => $currentMana,
            'max_mana' => $maxMana,
            'current_sanity' => $currentSanity,
            'max_sanity' => $maxSanity,
            'armor_class' => $armorClass,
        ]);

        if ($statsId <= 0) {
            throw new ValidationException(
                'Erro ao criar os recursos do personagem no encontro.'
            );
        }
    }
}
