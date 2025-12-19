<?php

namespace App\Application\Controllers\Monsters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Monsters\CreateMonsterAbilityDTO;
use App\Application\DTOs\Monsters\LinkMonsterAbilitiesDTO;
use App\Domain\Services\Monsters\CreateMonsterAbilityService;
use App\Domain\Services\Monsters\LinkMonsterAbilitiesService;

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

    public function linkToMonster(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'ability_ids' => 'array|required',
        ]);

        $schema->handle($request->body());

        $params = $request->params();
        $monsterId = (int) ($params['id'] ?? 0);

        $dto = new LinkMonsterAbilitiesDTO(
            $monsterId,
            $request->body()
        );

        $service = new LinkMonsterAbilitiesService();
        $service->execute($dto);

        return Response::json([
            'message' => 'Habilidades vinculadas ao monstro com sucesso.'
        ]);
    }
}
