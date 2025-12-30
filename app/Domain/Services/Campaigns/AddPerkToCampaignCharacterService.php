<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\AddPerkToCampaignCharacterDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterPerkRepository;
use App\Infrastructure\Repositories\CharacterRepository;
use App\Infrastructure\Repositories\OrderPerkRepository;
use App\Infrastructure\Repositories\PerkRepository;
use App\Infrastructure\Repositories\RacePerkRepository;

class AddPerkToCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignCharacterPerkRepository $campaignPerks;

    private CharacterRepository $characters;
    private PerkRepository $perks;

    private RacePerkRepository $racePerks;
    private OrderPerkRepository $orderPerks;

    public function __construct()
    {
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->campaignPerks      = new CampaignCharacterPerkRepository();

        $this->characters         = new CharacterRepository();
        $this->perks              = new PerkRepository();

        $this->racePerks          = new RacePerkRepository();
        $this->orderPerks         = new OrderPerkRepository();
    }

    public function execute(
        int $campaignCharacterId,
        AddPerkToCampaignCharacterDTO $dto
    ): void {
        $cc = $this->campaignCharacters->findById($campaignCharacterId);

        if (!$cc) {
            throw new ValidationException(
                'Personagem inválido.',
                ['campaign_character_id' => ['Não encontrado.']]
            );
        }

        $perk = $this->perks->findById($dto->perk_id);

        if (!$perk) {
            throw new ValidationException(
                'Perk inválido.',
                ['perk_id' => ['Perk não encontrado.']]
            );
        }

        if ($this->campaignPerks->exists($campaignCharacterId, $dto->perk_id)) {
            throw new ConflictException('Perk já adicionado.');
        }

        $currentPerks = $this->campaignPerks->countByCampaignCharacter($campaignCharacterId);

        if ($currentPerks >= (int) $cc['level']) {
            throw new ValidationException(
                'Limite atingido.',
                ['perks' => ['Quantidade de perks excede o nível do personagem.']]
            );
        }

        $character = $this->characters->findById((int) $cc['character_id']);

        if (!$character) {
            throw new ValidationException(
                'Personagem inválido.',
                ['character_id' => ['Personagem base não encontrado.']]
            );
        }

        $level = (int) $cc['level'];

        $allowedByRace = $this->racePerks->isAllowed(
            (int) $character->race_id,
            (int) $dto->perk_id,
            $level
        );

        $allowedByOrder = false;

        if (!empty($character->order_id)) {
            $allowedByOrder = $this->orderPerks->isAllowed(
                (int) $character->order_id,
                (int) $dto->perk_id,
                $level
            );
        }

        if (!$allowedByRace && !$allowedByOrder) {
            throw new ValidationException(
                'Perk inválido.',
                ['perk_id' => ['Este perk não está disponível para este personagem (raça/ordem/nível).']]
            );
        }

        $this->campaignPerks->create([
            'campaign_character_id' => $campaignCharacterId,
            'perk_id'               => (int) $dto->perk_id,
        ]);
    }
}
