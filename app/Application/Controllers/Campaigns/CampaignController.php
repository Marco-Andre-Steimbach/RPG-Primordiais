<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Core\Exceptions\ValidationException;
use App\Application\DTOs\Campaigns\CreateCampaignDTO;
use App\Domain\Services\Campaigns\CreateCampaignService;
use App\Domain\Services\Campaigns\GetAllCampaignsService;
use App\Domain\Services\Campaigns\GetCampaignByIdService;
use App\Domain\Services\Campaigns\GetCampaignCharacterSheetService;

class CampaignController
{
    public function store(Request $request)
    {
        $authUser = $request->user();

        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:3',
            'description' => 'string',
        ]);

        $schema->handle($request->body());

        $dto = new CreateCampaignDTO($request->body());

        $service = new CreateCampaignService();
        $campaign = $service->execute($dto, $authUser->id);

        return Response::json([
            'message' => 'Campanha criada com sucesso.',
            'campaign' => $campaign,
        ], 201);
    }

    public function index()
    {
        $service = new GetAllCampaignsService();

        return Response::json([
            'campaigns' => $service->execute()
        ]);
    }

    public function show(Request $request)
    {
        $campaignId = (int) ($request->params()['id'] ?? 0);

        $service = new GetCampaignByIdService();
        $campaign = $service->execute($campaignId);

        return Response::json([
            'campaign' => $campaign
        ]);
    }

    public function getCharacterSheet(Request $request)
{
    $params = $request->params();

    $campaignId = (int) ($params['campaign_id'] ?? 0);
    $campaignCharacterId = (int) ($params['character_id'] ?? 0);

    if ($campaignId <= 0 || $campaignCharacterId <= 0) {
        throw new ValidationException(
            'Dados inválidos.',
            [
                'campaign_id' => ['ID da campanha inválido.'],
                'character_id' => ['ID do personagem inválido.'],
            ]
        );
    }

    $service = new GetCampaignCharacterSheetService();
    $sheet = $service->execute($campaignId, $campaignCharacterId);

    return Response::json([
        'sheet' => $sheet
    ]);
}

}
