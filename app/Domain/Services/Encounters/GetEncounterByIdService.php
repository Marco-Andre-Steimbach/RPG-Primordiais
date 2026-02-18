<?php

namespace App\Domain\Services\Encounters;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\EncounterInitiativeRepository;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CharacterRepository;

class GetEncounterByIdService
{
    private EncounterRepository $encounters;
    private EncounterMonsterRepository $encounterMonsters;
    private EncounterPlayerRepository $encounterPlayers;
    private EncounterInitiativeRepository $initiative;
    private MonsterRepository $monsters;
    private CampaignCharacterRepository $campaignCharacters;
    private CharacterRepository $characters;

    public function __construct()
    {
        $this->encounters         = new EncounterRepository();
        $this->encounterMonsters  = new EncounterMonsterRepository();
        $this->encounterPlayers   = new EncounterPlayerRepository();
        $this->initiative         = new EncounterInitiativeRepository();
        $this->monsters           = new MonsterRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->characters         = new CharacterRepository();
    }

    public function execute(int $encounterId): array
    {
        $encounter = $this->encounters->findById($encounterId);

        if (!$encounter) {
            throw new ValidationException('Encontro não encontrado.');
        }

        $initiatives = $this->initiative->findByEncounterId($encounterId);

        $participants = [];

        foreach ($initiatives as $initiative) {

            if (!empty($initiative['encounter_monster_id'])) {

                $encounterMonster = $this->encounterMonsters
                    ->findById($initiative['encounter_monster_id']);

                if (!$encounterMonster) {
                    continue;
                }

                $monster = $this->monsters
                    ->findById($encounterMonster->monster_id);

                if (!$monster) {
                    continue;
                }

                $participants[] = [
                    'type' => 'monster',
                    'initiative_value' => (int) $initiative['initiative_value'],
                    'encounter_monster_id' => $encounterMonster->id,
                    'monster_id' => $monster->id,
                    'monster_name' => $monster->name,
                    'base_hp' => $monster->base_hp,
                    'current_hp' => $encounterMonster->current_hp
                ];

                continue;
            }

            if (!empty($initiative['encounter_player_id'])) {

                $encounterPlayer = $this->encounterPlayers
                    ->findById($initiative['encounter_player_id']);

                if (!$encounterPlayer) {
                    continue;
                }

                $campaignCharacter = $this->campaignCharacters
                    ->findById($encounterPlayer['campaign_character_id']);

                if (!$campaignCharacter) {
                    continue;
                }

                $character = $this->characters
                    ->findById($campaignCharacter['character_id']);

                if (!$character) {
                    continue;
                }

                $participants[] = [
                    'type' => 'player',
                    'initiative_value' => (int) $initiative['initiative_value'],
                    'encounter_player_id' => $encounterPlayer['id'],
                    'campaign_character_id' => $campaignCharacter['id'],
                    'character_id' => $character->id,
                    'character_name' => $character->name
                ];
            }
        }

        usort($participants, function ($a, $b) {
            return $b['initiative_value'] <=> $a['initiative_value'];
        });

        return [
            'id' => $encounter->id,
            'name' => $encounter->name,
            'status' => $encounter->status,
            'participants' => $participants
        ];
    }
}
