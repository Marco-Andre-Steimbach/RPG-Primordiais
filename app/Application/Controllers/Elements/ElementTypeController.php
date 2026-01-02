<?php

namespace App\Application\Controllers\Elements;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Domain\Services\Elements\GetAllElementTypesService;
use App\Domain\Services\Elements\GetElementTypeByIdService;
use App\Application\DTOs\Elements\CalculateDamageDTO;
use App\Domain\Services\Elements\CalculateElementDamageService;

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
}
