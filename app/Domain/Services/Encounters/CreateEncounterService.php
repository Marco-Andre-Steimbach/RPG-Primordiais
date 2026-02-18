<?php

namespace App\Domain\Services\Encounters;

use App\Application\DTOs\Encounters\CreateEncounterDTO;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Domain\Models\Encounter;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;

class CreateEncounterService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;

    public function __construct()
    {
        $this->encounters = new EncounterRepository();
        $this->campaigns = new CampaignRepository();
    }

    public function execute(
        CreateEncounterDTO $dto,
        int $userId
    ): Encounter {

        $campaign = $this->campaigns->findById($dto->campaign_id);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                ['campaign_id' => ['Campanha não encontrada.']]
            );
        }

        if ($campaign->created_by !== $userId) {
            throw new ForbiddenException(
                'Você não tem permissão para criar encontros nesta campanha.'
            );
        }

        $encounterId = $this->encounters->create([
            'campaign_id' => $dto->campaign_id,
            'name'        => $dto->name,
            'description' => $dto->description,
            'status'      => 'pending',
        ]);

        if (!$encounterId) {
            throw new ValidationException('Falha ao criar encontro.');
        }

        $encounter = $this->encounters->findById($encounterId);

        if (!$encounter) {
            throw new ValidationException('Erro ao carregar encontro criado.');
        }

        return $encounter;
    }
}
