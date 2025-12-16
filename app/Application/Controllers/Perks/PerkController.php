<?php

namespace App\Application\Controllers\Perks;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Perks\CreatePerkDTO;
use App\Domain\Services\Perks\CreatePerkService;

class PerkController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string|required|min:5',
            'type' => 'string|required',
            'mana_cost' => 'int',
            'race_id' => 'int|required',
            'required_level' => 'int',

            'element_types' => 'array',
            'flags' => 'array',
            'attributes' => 'array',
            'ability' => 'array',
        ]);

        $schema->handle($request->body());

        $dto = new CreatePerkDTO($request->body());

        $service = new CreatePerkService();
        $perk = $service->execute($dto);

        return Response::json([
            'message' => 'Perk criado com sucesso.',
            'perk' => $perk,
        ], 201);
    }
}
