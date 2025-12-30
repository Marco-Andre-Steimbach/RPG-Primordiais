<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Campaigns\AddPerkToCampaignCharacterDTO;
use App\Domain\Services\Campaigns\AddPerkToCampaignCharacterService;
use App\Core\Exceptions\ValidationException;

class CampaignCharacterPerkController
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
            'perk_id' => 'int|required',
        ]);

        $schema->handle($request->body());

        $dto = new AddPerkToCampaignCharacterDTO($request->body());

        $service = new AddPerkToCampaignCharacterService();
        $service->execute($campaignCharacterId, $dto);

        return Response::json([
            'message' => 'Perk adicionado ao personagem com sucesso.',
        ], 201);
    }
}
