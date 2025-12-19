<?php

namespace App\Application\Controllers\Monsters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Monsters\CreateMonsterAttackDTO;
use App\Domain\Services\Monsters\CreateMonsterAttackService;

class MonsterAttackController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string',

            'dice_formula' => 'string|required',
            'base_damage' => 'int',
            'bonus_accuracy' => 'int',

            'element_types' => 'array',
        ]);

        $schema->handle($request->body());

        $dto = new CreateMonsterAttackDTO($request->body());

        $service = new CreateMonsterAttackService();
        $attack = $service->execute($dto);

        return Response::json([
            'message' => 'Ataque de monstro criado com sucesso.',
            'attack' => $attack,
        ], 201);
    }
}
