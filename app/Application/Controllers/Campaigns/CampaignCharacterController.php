<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Campaigns\AddCharacterToCampaignDTO;
use App\Application\DTOs\Campaigns\LevelUpCampaignCharacterDTO;
use App\Domain\Services\Campaigns\AddCharacterToCampaignService;
use App\Domain\Services\Campaigns\LevelUpCampaignCharacterService;
use App\Application\DTOs\Campaigns\ChangeCharacterXPAmountDTO;
use App\Domain\Services\Campaigns\ChangeCampaignCharacterXPService;
use App\Application\DTOs\Campaigns\ChangeCharacterGoldAmountDTO;
use App\Domain\Services\Campaigns\ChangeCampaignCharacterGoldService;
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
    public function levelUp(Request $request)
    {
        $authUser = $request->user();

        $schema = new ValidateSchemaMiddleware([
            'campaign_character_id' => 'int|required',
            'attribute' => 'string|required',
        ]);

        $schema->handle($request->body());

        $dto = new LevelUpCampaignCharacterDTO(
            $request->body()
        );

        $service = new LevelUpCampaignCharacterService();

        $service->execute(
            dto: $dto,
            userId: $authUser->id
        );

        return Response::json([
            'success' => true,
            'message' => 'Level-up realizado com sucesso.'
        ]);
    }
    public function changeXP(Request $request)
    {
        $authUser = $request->user();

        $schema = new ValidateSchemaMiddleware([
            'campaign_character_id' => 'int|required',
            'amount' => 'int|required',
            'operation' => 'string|required',
        ]);

        $schema->handle($request->body());

        $dto = new ChangeCharacterXPAmountDTO(
            $request->body()
        );

        $service = new ChangeCampaignCharacterXPService();

        $xp = $service->execute(
            campaignCharacterId: (int) $request->body()['campaign_character_id'],
            dto: $dto,
            userId: $authUser->id
        );

        return Response::json([
            'success' => true,
            'xp' => $xp,
        ]);
    }
    public function changeGold(Request $request)
    {
        $authUser = $request->user();

        $schema = new ValidateSchemaMiddleware([
            'campaign_character_id' => 'int|required',
            'amount' => 'int|required',
            'operation' => 'string|required',
        ]);

        $schema->handle($request->body());

        $dto = new ChangeCharacterGoldAmountDTO(
            $request->body()
        );

        $service = new ChangeCampaignCharacterGoldService();

        $gold = $service->execute(
            campaignCharacterId: $dto->campaign_character_id,
            dto: $dto,
            userId: $authUser->id
        );

        return Response::json([
            'success' => true,
            'gold' => $gold,
        ]);
    }
}
