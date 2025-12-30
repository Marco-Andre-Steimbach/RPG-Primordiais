<?php

namespace App\Application\Controllers\Campaigns;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\DTOs\Campaigns\AddWeaponToCampaignCharacterDTO;
use App\Domain\Services\Campaigns\AddWeaponToCampaignCharacterService;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Core\Exceptions\ValidationException;

class CampaignCharacterWeaponController
{
    public function store(Request $request)
{
    $params = $request->params();
    $campaignCharacterId = (int) ($params['id'] ?? 0);

    if ($campaignCharacterId <= 0) {
        throw new ValidationException(
            'Dados inválidos.',
            ['campaign_character_id' => ['ID inválido.']]
        );
    }

    $schema = new ValidateSchemaMiddleware([
        'weapon_id' => 'int|required',
        'deactivate_weapon_id' => 'int|nullable',
        'equip' => 'bool|nullable',
    ]);

    $schema->handle($request->body());

    $dto = new AddWeaponToCampaignCharacterDTO($request->body());

    $service = new AddWeaponToCampaignCharacterService();
    $service->execute($campaignCharacterId, $dto);

    return Response::json([
        'message' => 'Arma adicionada com sucesso.',
    ], 201);
}

}
