<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\UpdateEncounterInitiativeDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterInitiativeRepository;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;

class UpdateEncounterInitiativeService
{
    private EncounterInitiativeRepository $initiatives;
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;

    public function __construct()
    {
        $this->initiatives = new EncounterInitiativeRepository();
        $this->encounters  = new EncounterRepository();
        $this->campaigns   = new CampaignRepository();
    }

    public function execute(
        UpdateEncounterInitiativeDTO $dto,
        int $userId
    ): void {

        $initiative = $this->initiatives->findById($dto->initiative_id);

        if (!$initiative) {
            throw new ValidationException(
                'Iniciativa inválida.',
                ['initiative_id' => ['Registro de iniciativa não encontrado.']]
            );
        }

        $encounter = $this->encounters->findById($initiative['encounter_id']);

        if (!$encounter) {
            throw new ValidationException(
                'Encontro inválido.'
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
                'Você não tem permissão para alterar este encontro.'
            );
        }

        $this->initiatives->updateValue(
            $dto->initiative_id,
            $dto->initiative_value
        );
    }
}
