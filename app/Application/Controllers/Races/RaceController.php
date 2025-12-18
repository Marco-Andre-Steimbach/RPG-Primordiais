<?php

namespace App\Application\Controllers\Races;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Races\RaceCreateDTO;
use App\Domain\Services\Races\CreateRaceService;
use App\Domain\Services\Races\UpdateRaceService;
use App\Domain\Services\Races\GetAllRacesService;
use App\Domain\Services\Races\GetRaceByIdService;
use App\Core\Exceptions\ValidationException;

class RaceController
{
    public function index()
    {
        $service = new GetAllRacesService();
        $races = $service->execute();

        return Response::json([
            'races' => $races,
        ]);
    }

    public function show(Request $request)
    {
        $id = (int) $request->param('id');

        if ($id <= 0) {
            throw new ValidationException('ID da raça inválido.');
        }

        $service = new GetRaceByIdService();
        $race = $service->execute($id);

        return Response::json([
            'race' => $race->toArray(),
        ]);
    }


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

    public function update(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'id'          => 'int|required',
            'name'        => 'string',
            'description' => 'string',
            'attributes'  => 'array',
        ]);

        $schema->handle($request->body());

        $service = new UpdateRaceService();
        $race = $service->execute($request->body());

        return Response::json([
            'message' => 'Raça atualizada com sucesso.',
            'race'    => $race->toArray(),
        ]);
    }
}
