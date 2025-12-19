<?php

namespace App\Application\Controllers\Monsters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Monsters\CreateMonsterAbilityDTO;
use App\Domain\Services\Monsters\CreateMonsterAbilityService;

class MonsterAbilityController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'title' => 'string|required|min:2',
            'description' => 'string|required|min:5',

            'dice_formula' => 'string',
            'base_damage' => 'int',
            'bonus_damage' => 'int',
            'bonus_speed' => 'int',

            'element_types' => 'array',
        ]);

        $schema->handle($request->body());

        $dto = new CreateMonsterAbilityDTO($request->body());

        $service = new CreateMonsterAbilityService();
        $ability = $service->execute($dto);

        return Response::json([
            'message' => 'Habilidade de monstro criada com sucesso.',
            'ability' => $ability,
        ], 201);
    }
}
