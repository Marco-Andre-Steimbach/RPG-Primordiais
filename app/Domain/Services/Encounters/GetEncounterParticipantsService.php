<?php

namespace App\Domain\Services\Encounters;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CharacterRepository;

class GetEncounterParticipantsService
{
    private EncounterRepository $encounters;
    private EncounterMonsterRepository $encounterMonsters;
    private EncounterPlayerRepository $encounterPlayers;
    private MonsterRepository $monsters;
    private CampaignCharacterRepository $campaignCharacters;
    private CharacterRepository $characters;

    public function __construct()
    {
        $this->encounters         = new EncounterRepository();
        $this->encounterMonsters  = new EncounterMonsterRepository();
        $this->encounterPlayers   = new EncounterPlayerRepository();
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

        $monsterRows = $this->encounterMonsters->findByEncounter($encounterId);
        $playerRows  = $this->encounterPlayers->findByEncounter($encounterId);

        $monsters = [];
        $players  = [];

        foreach ($monsterRows as $row) {

            $monster = $this->monsters->findById($row->monster_id);

            if (!$monster) {
                continue;
            }

            $monsters[] = [
                'encounter_monster_id' => $row->id,
                'monster_name' => $monster->name
            ];
        }

        foreach ($playerRows as $row) {

            $campaignCharacter = $this->campaignCharacters
                ->findById($row['campaign_character_id']);

            if (!$campaignCharacter) {
                continue;
            }

            $character = $this->characters
                ->findById($campaignCharacter['character_id']);

            if (!$character) {
                continue;
            }

            $players[] = [
                'encounter_player_id' => $row['id'],
                'character_name' => $character->name
            ];
        }

        return [
            'encounter_id' => $encounter->id,
            'monsters' => $monsters,
            'players' => $players
        ];
    }
}
