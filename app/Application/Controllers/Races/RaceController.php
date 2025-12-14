<?php

namespace App\Application\Controllers\Races;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Races\RaceCreateDTO;
use App\Domain\Services\Races\CreateRaceService;

class RaceController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string',
            'attributes' => 'required',
        ]);

        $schema->handle($request->body());

        $dto = new RaceCreateDTO($request->body());

        $service = new CreateRaceService();
        $race = $service->execute($dto);

        return Response::json([
            'message' => 'Raça criada com sucesso.',
            'race' => $race,
        ], 201);
    }
}
