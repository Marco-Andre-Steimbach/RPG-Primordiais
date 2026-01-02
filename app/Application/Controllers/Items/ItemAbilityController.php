<?php

namespace App\Application\Controllers\Items;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Items\CreateItemAbilityDTO;
use App\Domain\Services\Items\CreateItemAbilityService;
use App\Domain\Services\Items\GetItemAbilitiesByItemIdService;

class ItemAbilityController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'title' => 'string|required|min:2',
            'description' => 'string|required',

            'dice_formula' => 'string',

            'base_damage' => 'int',
            'bonus_damage' => 'int',
            'bonus_accuracy' => 'int',
            'bonus_speed' => 'int',

            'is_consumable' => 'bool',
            'max_uses' => 'int',

            'override_element_type_id' => 'int',
        ]);

        $schema->handle($request->body());

        $dto = new CreateItemAbilityDTO($request->body());

        $service = new CreateItemAbilityService();
        $ability = $service->execute($dto);

        return Response::json([
            'message' => 'Habilidade de item criada com sucesso.',
            'ability' => $ability,
        ], 201);
    }
    public function abilities(Request $request)
    {
        $params = $request->params();
        $itemId = (int) ($params['id'] ?? 0);

        $service = new GetItemAbilitiesByItemIdService();
        $abilities = $service->execute($itemId);

        return Response::json([
            'abilities' => $abilities,
        ]);
    }

}
