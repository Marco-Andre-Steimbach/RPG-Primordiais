<?php

namespace App\Application\Controllers\Monsters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Monsters\CreateMonsterDTO;
use App\Domain\Services\Monsters\CreateMonsterService;

class MonsterController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string',

            'base_hp' => 'int',
            'base_ac' => 'int',
            'base_speed' => 'int',

            'actions_per_turn' => 'int',

            'base_str' => 'int',
            'base_dex' => 'int',
            'base_con' => 'int',
            'base_wis' => 'int',
            'base_int' => 'int',

            'element_types' => 'array',
        ]);

        $schema->handle($request->body());

        $dto = new CreateMonsterDTO($request->body());

        $service = new CreateMonsterService();
        $monster = $service->execute($dto);

        return Response::json([
            'message' => 'Monstro criado com sucesso.',
            'monster' => $monster,
        ], 201);
    }
}
