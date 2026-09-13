<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\AddAbilityToCampaignCharacterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ConflictException;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CampaignCharacterAbilityRepository;
use App\Infrastructure\Repositories\AbilityNewRepository;
use App\Infrastructure\Repositories\CharacterAbilityRepository;

class AddAbilityToCampaignCharacterService
{
    private CampaignCharacterRepository $campaignCharacters;
    private CampaignCharacterAbilityRepository $campaignAbilities;
    private AbilityNewRepository $abilities;
    private CharacterAbilityRepository $characterAbilities;

    public function __construct()
    {
        $this->campaignCharacters =
            new CampaignCharacterRepository();

        $this->campaignAbilities =
            new CampaignCharacterAbilityRepository();

        $this->abilities =
            new AbilityNewRepository();

        $this->characterAbilities =
            new CharacterAbilityRepository();
    }

    public function execute(
        int $campaignCharacterId,
        AddAbilityToCampaignCharacterDTO $dto
    ): void {
        $campaignCharacter =
            $this->campaignCharacters
                ->findById(
                    $campaignCharacterId
                );

        if (!$campaignCharacter) {
            throw new ValidationException(
                'Personagem inválido.',
                [
                    'campaign_character_id' => [
                        'Não encontrado.'
                    ]
                ]
            );
        }

        $ability =
            $this->abilities
                ->findCompleteById(
                    $dto->ability_id
                );

        if (!$ability) {
            throw new ValidationException(
                'Habilidade inválida.',
                [
                    'ability_id' => [
                        'Habilidade não encontrada.'
                    ]
                ]
            );
        }

        $characterId =
            (int) $campaignCharacter[
                'character_id'
            ];

        if (
            !$this->characterAbilities
                ->exists(
                    $characterId,
                    $dto->ability_id
                )
        ) {
            throw new ValidationException(
                'Habilidade inválida.',
                [
                    'ability_id' => [
                        'Esta habilidade não pertence a este personagem.'
                    ]
                ]
            );
        }

        if (
            $this->campaignAbilities
                ->exists(
                    $campaignCharacterId,
                    $dto->ability_id
                )
        ) {
            throw new ConflictException(
                'Habilidade já adicionada.'
            );
        }

        $currentAbilities =
            $this->campaignAbilities
                ->countByCampaignCharacter(
                    $campaignCharacterId
                );

        if (
            $currentAbilities >=
            (int) $campaignCharacter['level']
        ) {
            throw new ValidationException(
                'Limite atingido.',
                [
                    'abilities' => [
                        'Quantidade de habilidades excede o nível do personagem.'
                    ]
                ]
            );
        }

        $this->campaignAbilities->create([
            'campaign_character_id' =>
                $campaignCharacterId,

            'ability_id' =>
                $dto->ability_id,
        ]);
    }
}
