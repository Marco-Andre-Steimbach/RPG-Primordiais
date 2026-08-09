<?php

namespace App\Application\Controllers\Encounters;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Encounters\CreateEncounterDTO;
use App\Application\DTOs\Encounters\AddMonsterToEncounterDTO;
use App\Domain\Services\Encounters\CreateEncounterService;
use App\Domain\Services\Encounters\AddMonsterToEncounterService;
use App\Application\DTOs\Encounters\AddPlayerToEncounterDTO;
use App\Domain\Services\Encounters\AddPlayerToEncounterService;
use App\Application\DTOs\Encounters\SetEncounterInitiativeDTO;
use App\Domain\Services\Encounters\SetEncounterInitiativeService;
use App\Application\DTOs\Encounters\UpdateEncounterInitiativeDTO;
use App\Domain\Services\Encounters\UpdateEncounterInitiativeService;
use App\Application\DTOs\Encounters\UpdateEncounterStatusDTO;
use App\Domain\Services\Encounters\UpdateEncounterStatusService;
use App\Application\DTOs\Encounters\UpdateEncounterResourcesDTO;
use App\Domain\Services\Encounters\UpdateEncounterResourcesService;
use App\Domain\Services\Encounters\GetAllEncountersService;
use App\Domain\Services\Encounters\GetEncounterByIdService;
use App\Domain\Services\Encounters\GetEncounterParticipantsService;
use App\Domain\Services\Encounters\GetEncounterCombatService;
use App\Core\Exceptions\ValidationException;

class EncounterController
{
    public function store(Request $request)
    {
        try {
            $authUser = $request->user();

            $schema = new ValidateSchemaMiddleware([
                'campaign_id' => 'int|required',
                'name'        => 'string|required|min:2',
                'description' => 'string|required',
            ]);

            $schema->handle($request->body());

            $dto = new CreateEncounterDTO($request->body());

            $service = new CreateEncounterService();

            $encounter = $service->execute(
                dto: $dto,
                userId: $authUser->id
            );

            return Response::json([
                'message' => 'Encontro criado com sucesso.',
                'encounter' => $encounter,
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error'   => true,
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors()
            ], 400);
        }
    }

    public function addMonster(Request $request)
    {
        try {
            $authUser = $request->user();

            $schema = new ValidateSchemaMiddleware([
                'encounter_id' => 'int|required',
                'monster_id'   => 'int|required',
                'quantity'     => 'int|required|min:1',
                'monster_level' => 'int|required|min:1',
            ]);

            $schema->handle($request->body());

            $dto = new AddMonsterToEncounterDTO($request->body());

            $service = new AddMonsterToEncounterService();

            $result = $service->execute(
                dto: $dto,
                userId: $authUser->id
            );

            return Response::json([
                'message' => 'Monstro(s) adicionados ao encontro com sucesso.',
                'data'    => $result,
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error'   => true,
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors()
            ], 400);
        }
    }
    public function addPlayer(Request $request)
    {
        try {
            $authUser = $request->user();

            $schema = new ValidateSchemaMiddleware([
                'encounter_id' => 'int|required',
                'campaign_character_id' => 'int|required',
            ]);

            $schema->handle($request->body());

            $dto = new AddPlayerToEncounterDTO($request->body());

            $service = new AddPlayerToEncounterService();

            $service->execute(
                dto: $dto,
                userId: $authUser->id
            );

            return Response::json([
                'message' => 'Player adicionado ao encontro com sucesso.',
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error'   => true,
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors()
            ], 400);
        }
    }
    public function setInitiative(Request $request)
    {
        try {
            $authUser = $request->user();

            $schema = new ValidateSchemaMiddleware([
                'encounter_id' => 'int|required',
                'initiative_value' => 'int|required|min:1',
                'encounter_monster_id' => 'int',
                'encounter_player_id' => 'int',
            ]);

            $schema->handle($request->body());

            $dto = new SetEncounterInitiativeDTO($request->body());

            $service = new SetEncounterInitiativeService();

            $service->execute(
                dto: $dto,
                userId: $authUser->id
            );

            return Response::json([
                'message' => 'Iniciativa definida com sucesso.',
            ], 201);
        } catch (ValidationException $e) {
            return Response::json([
                'error'   => true,
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors()
            ], 400);
        }
    }
    public function updateInitiative(Request $request)
    {
        try {
            $authUser = $request->user();

            $schema = new ValidateSchemaMiddleware([
                'initiative_id' => 'int|required',
                'initiative_value' => 'int|required|min:1',
            ]);

            $schema->handle($request->body());

            $dto = new UpdateEncounterInitiativeDTO($request->body());

            $service = new UpdateEncounterInitiativeService();

            $service->execute(
                dto: $dto,
                userId: $authUser->id
            );

            return Response::json([
                'message' => 'Iniciativa atualizada com sucesso.',
            ], 200);
        } catch (ValidationException $e) {
            return Response::json([
                'error'   => true,
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors()
            ], 400);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $authUser = $request->user();

            $schema = new ValidateSchemaMiddleware([
                'encounter_id' => 'int|required',
                'status' => 'string|required',
            ]);

            $schema->handle($request->body());

            $dto = new UpdateEncounterStatusDTO($request->body());

            $service = new UpdateEncounterStatusService();

            $service->execute(
                dto: $dto,
                userId: $authUser->id
            );

            return Response::json([
                'message' => 'Status do encontro atualizado com sucesso.',
            ], 200);
        } catch (ValidationException $e) {
            return Response::json([
                'error'   => true,
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors()
            ], 400);
        }
    }

public function updateResources(Request $request)
{
    try {
        $authUser = $request->user();

        $dto = new UpdateEncounterResourcesDTO(
            $request->body()
        );

        $service = new UpdateEncounterResourcesService();

        $service->execute(
            dto: $dto,
            userId: $authUser->id
        );

        return Response::json([
            'message' => 'Recursos atualizados com sucesso.',
        ], 200);
    } catch (ValidationException $e) {
        return Response::json([
            'error' => true,
            'message' => $e->getMessage(),
            'errors' => $e->getErrors()
        ], 400);
    }
}
    public function index(Request $request)
    {
        $status = $request->query()['status'] ?? null;

        $service = new GetAllEncountersService();

        $encounters = $service->execute($status);

        return Response::json([
            'encounters' => $encounters,
        ]);
    }
    public function show(Request $request)
    {
        $encounterId = (int) ($request->params()['id'] ?? 0);

        if ($encounterId <= 0) {
            throw new ValidationException(
                'ID inválido.',
                ['id' => ['ID do encontro inválido.']]
            );
        }

        $service = new GetEncounterByIdService();
        $encounter = $service->execute($encounterId);

        return Response::json([
            'encounter' => $encounter
        ]);
    }
    public function participants(Request $request)
    {
        $encounterId = (int) ($request->params()['id'] ?? 0);

        if ($encounterId <= 0) {
            throw new ValidationException(
                'ID inválido.',
                ['id' => ['ID do encontro inválido.']]
            );
        }

        $service = new GetEncounterParticipantsService();
        $participants = $service->execute($encounterId);

        return Response::json([
            'participants' => $participants
        ]);
    }
    public function combat(Request $request)
{
    $encounterId = (int) ($request->params()['id'] ?? 0);

    if ($encounterId <= 0) {
        throw new ValidationException(
            'ID inválido.',
            ['id' => ['ID do encontro inválido.']]
        );
    }

    $service = new GetEncounterCombatService();
    $combat = $service->execute($encounterId);

    return Response::json([
        'combat' => $combat
    ]);
}
}
