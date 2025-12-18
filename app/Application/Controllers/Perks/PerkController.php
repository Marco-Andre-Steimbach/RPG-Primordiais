<?php

namespace App\Application\Controllers\Perks;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Perks\CreatePerkDTO;
use App\Domain\Services\Perks\CreatePerkService;
use App\Domain\Services\Perks\GetPerksByRaceService;
use App\Domain\Services\Perks\GetPerkByIdService;
use App\Domain\Services\Perks\GetPerksByOrderService;
use App\Core\Exceptions\ValidationException;

class PerkController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string|required|min:5',
            'type' => 'string|required',
            'mana_cost' => 'int',

            'race_id' => 'int',
            'order_id' => 'int',
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
            'perk' => $perk->toArray(),
        ], 201);
    }

    public function byRace(Request $request)
    {
        $raceId = (int) ($request->params()['id'] ?? 0);

        if ($raceId <= 0) {
            throw new ValidationException('ID da raça inválido.');
        }

        $service = new GetPerksByRaceService();
        $perks = $service->execute($raceId);

        return Response::json([
            'perks' => array_map(
                fn($perk) => $perk->toArray(),
                $perks
            ),
        ]);
    }

    public function byOrder(Request $request)
    {
        $orderId = (int) ($request->params()['id'] ?? 0);

        if ($orderId <= 0) {
            throw new ValidationException('ID da ordem inválido.');
        }

        $service = new GetPerksByOrderService();
        $perks = $service->execute($orderId);

        return Response::json([
            'perks' => array_map(
                fn($perk) => $perk->toArray(),
                $perks
            ),
        ]);
    }
    public function show(Request $request)
    {
        $perkId = (int) ($request->params()['id'] ?? 0);

        if ($perkId <= 0) {
            throw new ValidationException('ID do perk inválido.');
        }

        $service = new GetPerkByIdService();
        $perk = $service->execute($perkId);

        return Response::json([
            'perk' => $perk->toArray(),
        ]);
    }
}
