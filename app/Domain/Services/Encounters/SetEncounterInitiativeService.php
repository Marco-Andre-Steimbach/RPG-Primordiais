<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\SetEncounterInitiativeDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\EncounterMonsterRepository;
use App\Infrastructure\Repositories\EncounterPlayerRepository;
use App\Infrastructure\Repositories\EncounterInitiativeRepository;

class SetEncounterInitiativeService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;
    private EncounterMonsterRepository $monsters;
    private EncounterPlayerRepository $players;
    private EncounterInitiativeRepository $initiative;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns = new CampaignRepository();
        $this->monsters = new EncounterMonsterRepository();
        $this->players = new EncounterPlayerRepository();
        $this->initiative = new EncounterInitiativeRepository();
    }

    public function execute(
        SetEncounterInitiativeDTO $dto,
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
                'Campanha inválida.'
            );
        }

        if ($campaign->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para modificar este encontro.'
            );
        }

        $monsterId = $dto->encounter_monster_id;
        $playerId  = $dto->encounter_player_id;

        if ($monsterId !== null) {

            $monster = $this->monsters->findById($monsterId);

            if (!$monster || $monster->encounter_id !== $dto->encounter_id) {
                throw new ValidationException(
                    'Monstro inválido para este encontro.'
                );
            }
        }

        if ($playerId !== null) {

            $player = $this->players->findById($playerId);

            if (!$player || $player['encounter_id'] !== $dto->encounter_id) {
                throw new ValidationException(
                    'Player inválido para este encontro.'
                );
            }
        }

        if ($this->initiative->existsForTarget(
            $dto->encounter_id,
            $monsterId,
            $playerId
        )) {
            throw new ValidationException(
                'Iniciativa já definida para este alvo.'
            );
        }

        $this->initiative->create([
            'encounter_id' => $dto->encounter_id,
            'encounter_monster_id' => $monsterId,
            'encounter_player_id' => $playerId,
            'initiative_value' => $dto->initiative_value,
        ]);
    }
}
