<?php

namespace App\Application\Controllers\Abilities;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Abilities\CreateAbilityDTO;
use App\Domain\Services\Abilities\CreateAbilityService;
use App\Core\Exceptions\ValidationException;

class AbilityController
{
    public function store(Request $request)
    {
        $authUser = $request->user();

        $params = $request->params();
        $characterId = (int) ($params['id'] ?? 0);

        if ($characterId <= 0) {
            throw new ValidationException(
                'Dados inválidos.',
                ['character_id' => ['character_id inválido ou ausente na rota.']]
            );
        }

        $schema = new ValidateSchemaMiddleware([
            'title' => 'string|required|min:2',
            'description' => 'string|required',

            'arcane_title' => 'string',
            'arcane_description' => 'string',

            'mana_cost' => 'int|required',
            'arcane_mana_cost' => 'int',

            'dice_formula' => 'string',

            'base_damage' => 'int',
            'bonus_speed' => 'int',

            'element_types' => 'array|required',

            'required_race_id' => 'int',
            'required_order_id' => 'int',
        ]);

        $schema->handle($request->body());

        $dto = new CreateAbilityDTO($request->body());

        $service = new CreateAbilityService();
        $ability = $service->execute(
            characterId: $characterId,
            dto: $dto,
            userId: $authUser->id
        );

        return Response::json([
            'message' => 'Habilidade criada com sucesso.',
            'ability' => $ability,
        ], 201);
    }

}
