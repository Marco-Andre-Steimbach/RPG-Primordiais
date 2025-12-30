<?php

namespace App\Domain\Services\Campaigns;

use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;

class GetAllCampaignsService
{
    public function execute(): array
    {
        $campaignRepo = new CampaignRepository();
        $userRepo = new UserRepository();
        $characterRepo = new CampaignCharacterRepository();

        $campaigns = $campaignRepo->findAll();

        return array_map(function ($campaign) use ($userRepo, $characterRepo) {
            $master = $userRepo->findById($campaign['created_by']);

            return [
                'id' => $campaign['id'],
                'name' => $campaign['name'],
                'description' => $campaign['description'],
                'created_at' => $campaign['created_at'],
                'master' => $master ? $master->nickname : null,
                'characters_count' => $characterRepo->countByCampaign($campaign['id']),
            ];
        }, $campaigns);
    }
}
