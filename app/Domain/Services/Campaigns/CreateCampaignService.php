<?php

namespace App\Domain\Services\Campaigns;

use App\Application\DTOs\Campaigns\CreateCampaignDTO;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Campaign;
use App\Infrastructure\Repositories\CampaignRepository;

class CreateCampaignService
{
    private CampaignRepository $campaigns;

    public function __construct()
    {
        $this->campaigns = new CampaignRepository();
    }

    public function execute(CreateCampaignDTO $dto, int $userId): Campaign
    {
        $campaignId = $this->campaigns->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'created_by' => $userId,
        ]);

        if (!$campaignId) {
            throw new ValidationException('Falha ao criar campanha.');
        }

        $campaign = $this->campaigns->findById($campaignId);

        if (!$campaign) {
            throw new ValidationException('Erro ao carregar campanha criada.');
        }

        return $campaign;
    }
}
