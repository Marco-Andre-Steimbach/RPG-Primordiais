<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\UpdateEncounterStatusDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;

class UpdateEncounterStatusService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns  = new CampaignRepository();
    }

    public function execute(
        UpdateEncounterStatusDTO $dto,
        int $userId
    ): void {

        $encounter = $this->encounters->findById($dto->encounter_id);

        if (!$encounter) {
            throw new ValidationException(
                'Encontro não encontrado.',
                ['encounter_id' => ['Encontro inválido.']]
            );
        }

        $campaign = $this->campaigns->findById($encounter->campaign_id);

        if (!$campaign) {
            throw new ValidationException('Campanha inválida.');
        }

        if ($campaign->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para alterar este encontro.'
            );
        }

        $this->encounters->updateStatus(
            $dto->encounter_id,
            $dto->status
        );
    }
}
