<?php

namespace App\Application\Controllers\Elements;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Domain\Services\Elements\GetAllElementTypesService;
use App\Domain\Services\Elements\GetElementTypeByIdService;
use App\Application\DTOs\Elements\CalculateDamageDTO;
use App\Domain\Services\Elements\CalculateElementDamageService;
use App\Domain\Services\Elements\GetElementRelationsService;
use App\Application\DTOs\Elements\GetElementRelationsDTO;
use App\Application\DTOs\Elements\GetElementAttackRelationsDTO;
use App\Domain\Services\Elements\GetElementAttackRelationsService;


class ElementTypeController
{
    public function index()
    {
        $service = new GetAllElementTypesService();

        return Response::json([
            'elements' => $service->execute(),
        ]);
    }

    public function show(Request $request)
    {
        $elementId = (int) ($request->params()['id'] ?? 0);

        $service = new GetElementTypeByIdService();
        $element = $service->execute($elementId);

        return Response::json([
            'element' => $element,
        ]);
    }

    public function calculateDamage(Request $request)
    {
        $dto = new CalculateDamageDTO($request->body());

        $service = new CalculateElementDamageService();
        $result = $service->execute($dto);

        return Response::json([
            'damage' => $result,
        ]);
    }
    public function getRelations(Request $request)
    {
        $dto = new GetElementRelationsDTO(
            $request->body()
        );

        $service = new GetElementRelationsService();
        $result = $service->execute($dto->defense_elements);

        return Response::json([
            'relations' => $result,
        ]);
    }
    public function getAttackRelations(Request $request)
    {
        $dto = new GetElementAttackRelationsDTO(
            $request->body()
        );

        $service = new GetElementAttackRelationsService();
        $result = $service->execute($dto->attack_elements);

        return Response::json([
            'relations' => $result,
        ]);
    }
}
