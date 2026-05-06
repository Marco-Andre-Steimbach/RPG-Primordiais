<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Campaigns\AddArmorToCampaignCharacterDTO;
use App\Domain\Services\Campaigns\AddArmorToCampaignCharacterService;
use App\Application\DTOs\Campaigns\UnequipArmorFromCampaignCharacterDTO;
use App\Domain\Services\Campaigns\UnequipArmorFromCampaignCharacterService;
use App\Core\Exceptions\ValidationException;

class CampaignCharacterArmorController
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
            'armor_item_id' => 'int|required',
            'equip'         => 'bool|nullable',
        ]);

        $schema->handle($request->body());

        $dto = new AddArmorToCampaignCharacterDTO(
            $request->body()
        );

        $service = new AddArmorToCampaignCharacterService();
        $service->execute($campaignCharacterId, $dto);

        return Response::json([
            'message' => 'Armadura adicionada ao personagem com sucesso.',
        ], 201);
    }

     public function remove(Request $request)
    {
        $params = $request->params();

        $campaignCharacterId = (int) ($params['id'] ?? 0);

        if ($campaignCharacterId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                [
                    'campaign_character_id' => [
                        'ID do personagem da campanha inválido ou ausente.'
                    ]
                ]
            );
        }

        $schema = new ValidateSchemaMiddleware([
            'armor_slot_id' => 'int|required',
        ]);

        $schema->handle($request->body());

        $dto = new UnequipArmorFromCampaignCharacterDTO(
            $request->body()
        );

        $service = new UnequipArmorFromCampaignCharacterService();

        $service->execute(
            $campaignCharacterId,
            $dto
        );

        return Response::json([
            'message' => 'Armadura desequipada com sucesso.',
        ]);
    }
}
