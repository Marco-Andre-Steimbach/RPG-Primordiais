<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\AddPlayerToEncounterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;

class AddPlayerToEncounterService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;
    private CampaignCharacterRepository $campaignCharacters;
    private EncounterPlayerRepository $encounterPlayers;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns = new CampaignRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->encounterPlayers = new EncounterPlayerRepository();
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

        $character = $this->campaignCharacters->findById($dto->campaign_character_id);

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Personagem não encontrado.']]
            );
        }

        if ((int)$character['campaign_id'] !== $encounter->campaign_id) {
            throw new ValidationException(
                'Personagem não pertence a esta campanha.'
            );
        }

        $this->encounterPlayers->create([
            'encounter_id' => $dto->encounter_id,
            'campaign_character_id' => $dto->campaign_character_id,
        ]);
    }
}
