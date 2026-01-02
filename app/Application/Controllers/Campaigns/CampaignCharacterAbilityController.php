<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\DTOs\Campaigns\AddAbilityToCampaignCharacterDTO;
use App\Domain\Services\Campaigns\AddAbilityToCampaignCharacterService;

class CampaignCharacterAbilityController
{
    public function store(Request $request)
    {
        $params = $request->params();
        $campaignCharacterId = (int) ($params['id'] ?? 0);

        $dto = new AddAbilityToCampaignCharacterDTO($request->body());

        $service = new AddAbilityToCampaignCharacterService();
        $service->execute($campaignCharacterId, $dto);

        return Response::json([
            'message' => 'Habilidade adicionada à campanha com sucesso.',
        ], 201);
    }
}
