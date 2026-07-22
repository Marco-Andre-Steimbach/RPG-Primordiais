<?php

namespace App\Domain\Services\Encounters;

use App\Core\Exceptions\ValidationException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\EncounterPlayerStatsRepository;
use App\Infrastructure\Repositories\EncounterInitiativeRepository;
use App\Infrastructure\Repositories\MonsterRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;
use App\Infrastructure\Repositories\CharacterRepository;

class GetEncounterCombatService
{
    private EncounterRepository $encounters;
    private EncounterMonsterRepository $encounterMonsters;
    private EncounterPlayerRepository $encounterPlayers;
    private EncounterPlayerStatsRepository $encounterPlayerStats;
    private EncounterInitiativeRepository $initiatives;
    private MonsterRepository $monsters;
    private CampaignCharacterRepository $campaignCharacters;
    private CharacterRepository $characters;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->encounterMonsters = new EncounterMonsterRepository();
        $this->encounterPlayers = new EncounterPlayerRepository();
        $this->encounterPlayerStats = new EncounterPlayerStatsRepository();
        $this->initiatives = new EncounterInitiativeRepository();
        $this->monsters = new MonsterRepository();
        $this->campaignCharacters = new CampaignCharacterRepository();
        $this->characters = new CharacterRepository();
    }

    public function execute(int $encounterId): array
    {
        $encounter = $this->encounters->findById($encounterId);

        if (!$encounter) {
            throw new ValidationException(
                'Encontro não encontrado.',
                ['encounter_id' => ['Encontro não encontrado.']]
            );
        }

        $monsterRows = $this->encounterMonsters
            ->findByEncounter($encounterId);

        $playerRows = $this->encounterPlayers
            ->findByEncounter($encounterId);

        $initiativeRows = $this->initiatives
            ->findByEncounter($encounterId);

        $initiativePlayers = [];
        $initiativeMonsters = [];

        foreach ($initiativeRows as $initiativeRow) {
            $encounterPlayerId = $this->value(
                $initiativeRow,
                'encounter_player_id'
            );

            $encounterMonsterId = $this->value(
                $initiativeRow,
                'encounter_monster_id'
            );

            if ($encounterPlayerId) {
                $initiativePlayers[(int) $encounterPlayerId] = $initiativeRow;
            }

            if ($encounterMonsterId) {
                $initiativeMonsters[(int) $encounterMonsterId] = $initiativeRow;
            }
        }

        $turnOrder = [];

        foreach ($playerRows as $row) {
            $encounterPlayerId = (int) $this->value($row, 'id');
            $campaignCharacterId = (int) $this->value(
                $row,
                'campaign_character_id'
            );

            $campaignCharacter = $this->campaignCharacters
                ->findById($campaignCharacterId);

            if (!$campaignCharacter) {
                continue;
            }

            $characterId = (int) $this->value(
                $campaignCharacter,
                'character_id'
            );

            $character = $this->characters->findById($characterId);

            if (!$character) {
                continue;
            }

            $stats = $this->encounterPlayerStats
                ->findByEncounterPlayerId($encounterPlayerId);

            if (!$stats) {
                continue;
            }

            $initiative = $initiativePlayers[$encounterPlayerId] ?? null;

            $turnOrder[] = [
                'type' => 'player',
                'initiative_id' => $initiative
                    ? (int) $this->value($initiative, 'id')
                    : null,
                'initiative_value' => $initiative
                    ? (int) $this->value($initiative, 'initiative_value')
                    : null,
                'encounter_player_id' => $encounterPlayerId,
                'campaign_character_id' => $campaignCharacterId,
                'character_id' => $characterId,
                'name' => $this->value($character, 'name'),
                'current_hp' => (int) $this->value($stats, 'current_hp'),
                'max_hp' => (int) $this->value($stats, 'max_hp'),
                'current_mana' => (int) $this->value($stats, 'current_mana'),
                'max_mana' => (int) $this->value($stats, 'max_mana'),
                'current_sanity' => (int) $this->value(
                    $stats,
                    'current_sanity'
                ),
                'max_sanity' => (int) $this->value($stats, 'max_sanity'),
                'armor_class' => (int) $this->value($stats, 'armor_class'),
            ];
        }

        foreach ($monsterRows as $row) {
            $encounterMonsterId = (int) $this->value($row, 'id');
            $monsterId = (int) $this->value($row, 'monster_id');

            $monster = $this->monsters->findById($monsterId);

            if (!$monster) {
                continue;
            }

            $initiative = $initiativeMonsters[$encounterMonsterId] ?? null;

            $currentHp = (int) $this->value($row, 'current_hp');
$maxHp = (int) $this->value($row, 'max_hp');

            $turnOrder[] = [
                'type' => 'monster',
                'initiative_id' => $initiative
                    ? (int) $this->value($initiative, 'id')
                    : null,
                'initiative_value' => $initiative
                    ? (int) $this->value($initiative, 'initiative_value')
                    : null,
                'encounter_monster_id' => $encounterMonsterId,
                'monster_id' => $monsterId,
                'monster_level' => (int) $this->value(
                    $row,
                    'monster_level'
                ),
                'name' => $this->value($monster, 'name'),
                'current_hp' => $currentHp,
                'max_hp' => $maxHp,
            ];
        }

        usort(
            $turnOrder,
            function (array $first, array $second): int {
                $firstInitiative = $first['initiative_value'] ?? -1;
                $secondInitiative = $second['initiative_value'] ?? -1;

                if ($firstInitiative === $secondInitiative) {
                    return $first['name'] <=> $second['name'];
                }

                return $secondInitiative <=> $firstInitiative;
            }
        );

        return [
            'encounter_id' => (int) $this->value($encounter, 'id'),
            'campaign_id' => (int) $this->value($encounter, 'campaign_id'),
            'name' => $this->value($encounter, 'name'),
            'description' => $this->value($encounter, 'description'),
            'status' => $this->value($encounter, 'status'),
            'turn_order' => $turnOrder,
        ];
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
