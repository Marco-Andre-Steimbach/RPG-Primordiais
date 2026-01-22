<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Campaigns\AddItemToCampaignCharacterDTO;
use App\Application\DTOs\Campaigns\UseCampaignCharacterItemDTO;
use App\Domain\Services\Campaigns\AddItemToCampaignCharacterService;
use App\Domain\Services\Campaigns\UseCampaignCharacterItemService;
use App\Core\Exceptions\ValidationException;

class CampaignCharacterItemController
{
    public function store(Request $request)
    {
        $params = $request->params();
        $campaignCharacterId = (int) ($params['id'] ?? 0);

        if ($campaignCharacterId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                ['campaign_character_id' => ['ID do personagem da campanha inválido ou ausente.']]
            );
        }

        $schema = new ValidateSchemaMiddleware([
            'item_id'  => 'int|required',
            'quantity' => 'int|required',
        ]);

        $schema->handle($request->body());

        $dto = new AddItemToCampaignCharacterDTO($request->body());

        $service = new AddItemToCampaignCharacterService();
        $service->execute($campaignCharacterId, $dto);

        return Response::json([
            'message' => 'Item adicionado ao personagem com sucesso.',
        ], 201);
    }

    public function use(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'campaign_character_id' => 'int|required',
            'item_id' => 'int|required',
        ]);

        $schema->handle($request->body());

        $dto = new UseCampaignCharacterItemDTO($request->body());

        $service = new UseCampaignCharacterItemService();
        $service->execute(
            $dto->campaign_character_id,
            $dto->item_id
        );

        return Response::json([
            'message' => 'Item utilizado com sucesso.',
        ]);
    }
}
