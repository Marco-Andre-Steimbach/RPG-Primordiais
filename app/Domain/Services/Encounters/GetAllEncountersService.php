<?php

namespace App\Domain\Services\Encounters;

use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\ForbiddenException;
use App\Infrastructure\Repositories\EncounterRepository;
use App\Infrastructure\Repositories\CampaignRepository;

class GetAllEncountersService
{
    private EncounterRepository $encounters;
    private CampaignRepository $campaigns;

    public function __construct()
    {
        $this->encounters =
            new EncounterRepository();

        $this->campaigns =
            new CampaignRepository();
    }

    public function execute(
        int $campaignId,
        ?string $status,
        int $userId
    ): array {
        $campaign = $this->campaigns
            ->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException(
                'Campanha inválida.',
                [
                    'campaign_id' => [
                        'Campanha não encontrada.'
                    ]
                ]
            );
        }

        if (
            (int) $campaign->created_by !==
            $userId
        ) {
            throw new ForbiddenException(
                'Você não tem permissão para visualizar os encontros desta campanha.'
            );
        }

        return $this->encounters
            ->findAllBasic(
                campaignId: $campaignId,
                status: $status
            );
    }
}
