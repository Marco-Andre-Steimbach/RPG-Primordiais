<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\AddCharacterToCampaignDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ConflictException;
use App\Domain\Models\CampaignCharacterSheet;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterAttributesRepository;

class AddCharacterToCampaignService
{
    private CampaignRepository $campaigns;
    private CharacterRepository $characters;
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignCharacterAttributesRepository $attributes;

    public function __construct()
    {
        $this->campaigns = new CampaignRepository();
        $this->characters = new CharacterRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->attributes = new CampaignCharacterAttributesRepository();
    }

    public function execute(
        int $campaignId,
        AddCharacterToCampaignDTO $dto,
        int $userId
    ): CampaignCharacterSheet {
        $campaign = $this->campaigns->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                ['campaign_id' => ['Campanha não encontrada.']]
            );
        }

        $character = $this->characters->findById($dto->character_id);

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                ['character_id' => ['Personagem não encontrado.']]
            );
        }

        if ($character->created_by !== $userId) {
            throw new ValidationException(
                'Ação não permitida.',
                ['character_id' => ['Você não é dono deste personagem.']]
            );
        }

        if ($this->campaignCharacters->exists($campaignId, $dto->character_id)) {
            throw new ConflictException('Este personagem já está na campanha.');
        }

        $campaignCharacterId = $this->campaignCharacters->create([
            'campaign_id' => $campaignId,
            'user_id' => $userId,
            'character_id' => $dto->character_id,
            'level' => 1,
        ]);

        if (!$campaignCharacterId) {
            throw new ValidationException('Falha ao adicionar personagem à campanha.');
        }

        $sanityMax = max(15, (int) ($dto->attributes['sanity'] ?? 15));

        $this->attributes->create([
            'campaign_character_id' => $campaignCharacterId,
            'str' => (int) $dto->attributes['str'],
            'dex' => (int) $dto->attributes['dex'],
            'con' => (int) $dto->attributes['con'],
            'intt' => (int) $dto->attributes['intt'],
            'wis' => (int) $dto->attributes['wis'],
            'cha' => (int) $dto->attributes['cha'],
            'sanity' => $sanityMax,
            'sanity_max' => $sanityMax,
        ]);

        return new CampaignCharacterSheet(
            campaign_character_id: $campaignCharacterId,
            level: 1,
            mana_modifier: $character->mana_modifier,
            baseAttributes: [
                'str'  => (int) $dto->attributes['str'],
                'dex'  => (int) $dto->attributes['dex'],
                'con'  => (int) $dto->attributes['con'],
                'intt' => (int) $dto->attributes['intt'],
                'wis'  => (int) $dto->attributes['wis'],
                'cha'  => (int) $dto->attributes['cha'],
            ],
            raceAttributes: [],
            orderAttributes: [],
            sanity_max: $sanityMax,
            sanity_current: $sanityMax
        );
    }
}
