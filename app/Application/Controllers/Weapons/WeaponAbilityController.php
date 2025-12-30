<?php

namespace App\Application\Controllers\Weapons;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Weapons\CreateWeaponAbilityDTO;
use App\Domain\Services\Weapons\CreateWeaponAbilityService;
use App\Core\Exceptions\ValidationException;

class WeaponAbilityController
{
    public function store(Request $request)
    {
        $params = $request->params();
        $weaponId = (int) ($params['id'] ?? 0);

        if ($weaponId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                ['weapon_id' => ['weapon_id inválido ou ausente na rota.']]
            );
        }

        $schema = new ValidateSchemaMiddleware([
            'title' => 'string|required|min:2',
            'description' => 'string|required',

            'dice_formula' => 'string',

            'base_damage' => 'int',
            'bonus_damage' => 'int',
            'bonus_accuracy' => 'int',
            'bonus_speed' => 'int',

            'element_types' => 'array|required',
        ]);

        $schema->handle($request->body());

        $dto = new CreateWeaponAbilityDTO($request->body());

        $service = new CreateWeaponAbilityService();
        $ability = $service->execute($weaponId, $dto);

        return Response::json([
            'message' => 'Habilidade de arma criada com sucesso.',
            'ability' => $ability,
        ], 201);
    }
}
