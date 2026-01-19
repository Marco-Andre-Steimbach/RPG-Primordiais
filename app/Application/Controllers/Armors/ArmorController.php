<?php

namespace App\Application\Controllers\Armors;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Armors\CreateArmorDTO;
use App\Domain\Services\Armors\CreateArmorService;
use App\Domain\Services\Armors\GetAllArmorsService;
use App\Domain\Services\Armors\GetArmorByIdService;

class ArmorController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'item_id' => 'int|required',
            'armor_slot_id' => 'int|required',

            'armor_class_bonus' => 'int',
            'min_strength_required' => 'int',
            'speed_penalty' => 'int',

            'weak_damage_type_id' => 'int',

            'element_types' => 'array',
            'armor_abilities' => 'array',
        ]);

        $schema->handle($request->body());

        $dto = new CreateArmorDTO($request->body());

        $service = new CreateArmorService();
        $armor = $service->execute($dto);

        return Response::json([
            'message' => 'Armadura criada com sucesso.',
            'armor' => $armor,
        ], 201);
    }

    public function index()
    {
        $service = new GetAllArmorsService();

        return Response::json([
            'armors' => $service->execute(),
        ]);
    }

    public function show(Request $request)
    {
        $id = (int) ($request->params()['id'] ?? 0);

        $service = new GetArmorByIdService();
        $armor = $service->execute($id);

        return Response::json([
            'armor' => $armor,
        ]);
    }
}
