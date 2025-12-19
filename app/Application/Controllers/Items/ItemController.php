<?php

namespace App\Application\Controllers\Items;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Items\CreateItemDTO;
use App\Domain\Services\Items\CreateItemService;
use App\Domain\Services\Items\GetAllItemsService;
use App\Domain\Services\Items\GetItemByIdService;

class ItemController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string',
            'value' => 'int',

            'element_types' => 'array',
            'item_abilities' => 'array',
        ]);

        $schema->handle($request->body());

        $dto = new CreateItemDTO($request->body());

        $service = new CreateItemService();
        $item = $service->execute($dto);

        return Response::json([
            'message' => 'Item criado com sucesso.',
            'item' => $item,
        ], 201);
    }

    public function index(Request $request)
    {
        $filters = $request->query();

        $service = new GetAllItemsService();
        $items = $service->execute($filters);

        return Response::json([
            'items' => $items
        ]);
    }

    public function show(Request $request)
    {
        $params = $request->params();
        $itemId = (int) ($params['id'] ?? 0);

        $service = new GetItemByIdService();
        $item = $service->execute($itemId);

        return Response::json([
            'item' => $item
        ]);
    }
}
