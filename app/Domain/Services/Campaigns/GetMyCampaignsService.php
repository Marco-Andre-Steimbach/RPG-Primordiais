<?php

namespace App\Domain\Services\Campaigns;

use App\Infrastructure\Repositories\CampaignRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\CampaignCharacterRepository;

class GetMyCampaignsService
{
    public function execute(int $userId): array
    {

        $campaignRepo = new CampaignRepository();
        $userRepo = new UserRepository();
        $campaignCharacterRepo = new CampaignCharacterRepository();

        $campaigns = $campaignRepo->findByUserParticipation($userId);
        return array_map(function ($campaign) use ($userRepo, $campaignCharacterRepo) {
            $master = $userRepo->findById($campaign['created_by']);

            return [
                'id' => $campaign['id'],
                'name' => $campaign['name'],
                'description' => $campaign['description'],
                'created_at' => $campaign['created_at'],
                'master' => $master ? $master->nickname : null,
                'characters_count' => $campaignCharacterRepo->countByCampaign($campaign['id']),
            ];
        }, $campaigns);
    }
}
