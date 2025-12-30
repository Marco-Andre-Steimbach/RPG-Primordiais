<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Campaigns\AddCharacterToCampaignDTO;
use App\Domain\Services\Campaigns\AddCharacterToCampaignService;
use App\Core\Exceptions\ValidationException;

class CampaignCharacterController
{
    public function store(Request $request)
    {
        $authUser = $request->user();

        $params = $request->params();
        $campaignId = (int) ($params['id'] ?? 0);

        if ($campaignId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                ['campaign_id' => ['campaign_id inválido ou ausente na rota.']]
            );
        }

        $schema = new ValidateSchemaMiddleware([
            'character_id' => 'int|required',
            'attributes' => 'array|required',
        ]);

        $schema->handle($request->body());

        $dto = new AddCharacterToCampaignDTO($request->body());

        $service = new AddCharacterToCampaignService();
        $campaignCharacter = $service->execute(
            campaignId: $campaignId,
            dto: $dto,
            userId: $authUser->id
        );

        return Response::json([
            'message' => 'Personagem adicionado à campanha com sucesso.',
            'campaign_character' => $campaignCharacter,
        ], 201);
    }
}
