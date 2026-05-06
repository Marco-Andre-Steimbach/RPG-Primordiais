<?php

namespace App\Application\Controllers\Armors;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Armors\CreateArmorAbilityDTO;
use App\Domain\Services\Armors\CreateArmorAbilityService;
use App\Domain\Services\Armors\GetAllArmorAbilitiesService;
use App\Domain\Services\Armors\GetArmorAbilityByIdService;

class ArmorAbilityController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'title' => 'string|required|min:2',
            'description' => 'string|required',

            'dice_formula' => 'string',
            'range' => 'int',

            'base_damage' => 'int',
            'armor_class_bonus' => 'int',
            'bonus_speed' => 'int',
        ]);

        $schema->handle($request->body());

        $dto = new CreateArmorAbilityDTO($request->body());

        $service = new CreateArmorAbilityService();
        $ability = $service->execute($dto);

        return Response::json([
            'message' => 'Habilidade de armadura criada com sucesso.',
            'ability' => $ability,
        ], 201);
    }

    public function index()
    {
        $service = new GetAllArmorAbilitiesService();

        return Response::json([
            'armor_abilities' => $service->execute(),
        ]);
    }

    public function show(Request $request)
    {
        $id = (int) ($request->params()['id'] ?? 0);

        $service = new GetArmorAbilityByIdService();
        $ability = $service->execute($id);

        return Response::json([
            'armor_ability' => $ability,
        ]);
    }
}
