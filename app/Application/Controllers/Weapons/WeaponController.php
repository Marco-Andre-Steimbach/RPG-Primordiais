<?php

namespace App\Application\Controllers\Weapons;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Weapons\CreateWeaponDTO;
use App\Domain\Services\Weapons\CreateWeaponService;
use App\Domain\Services\Weapons\GetAllWeaponsService;
use App\Domain\Services\Weapons\GetWeaponByIdService;

class WeaponController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'item_id' => 'int|required',
            'weapon_damage_type_id' => 'int|required',

            'dice_formula' => 'string|required',

            'base_damage' => 'int',
            'bonus_accuracy' => 'int',
            'bonus_speed' => 'int',

            'ammo_item_id' => 'int',
            'ammo_per_use' => 'int',

            'element_types' => 'array|required',
        ]);

        $schema->handle($request->body());

        $dto = new CreateWeaponDTO($request->body());

        $service = new CreateWeaponService();
        $weapon = $service->execute($dto);

        return Response::json([
            'message' => 'Arma criada com sucesso.',
            'weapon' => $weapon,
        ], 201);
    }
    public function index(Request $request)
    {
        $service = new GetAllWeaponsService();
        $weapons = $service->execute();

        return Response::json([
            'weapons' => $weapons,
        ]);
    }
    public function show(Request $request)
    {
        $params = $request->params();
        $weaponId = (int) ($params['id'] ?? 0);

        $service = new GetWeaponByIdService();
        $weapon = $service->execute($weaponId);

        return Response::json([
            'weapon' => $weapon,
        ]);
    }
}
