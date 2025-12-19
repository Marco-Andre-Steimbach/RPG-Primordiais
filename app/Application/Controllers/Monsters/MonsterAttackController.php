<?php

namespace App\Application\Controllers\Monsters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Monsters\CreateMonsterAttackDTO;
use App\Application\DTOs\Monsters\LinkMonsterAttacksDTO;
use App\Domain\Services\Monsters\CreateMonsterAttackService;
use App\Domain\Services\Monsters\LinkMonsterAttacksService;

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

    public function linkToMonster(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'attack_ids' => 'array|required',
        ]);
        
        $schema->handle($request->body());

        $params = $request->params();
        $monsterId = (int) ($params['id'] ?? 0);

        $dto = new LinkMonsterAttacksDTO(
            $monsterId,
            $request->body()
        );

        $service = new LinkMonsterAttacksService();
        $service->execute($dto);

        return Response::json([
            'message' => 'Ataques vinculados ao monstro com sucesso.'
        ]);
    }
}
