<?php

namespace App\Application\Controllers\Orders;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Application\Middlewares\ValidateSchemaMiddleware;
use App\Application\DTOs\Orders\CreateOrderDTO;
use App\Domain\Services\Orders\CreateOrderService;
use App\Domain\Services\Orders\GetOrderByIdService;
use App\Domain\Services\Orders\GetAllOrdersService;
use App\Core\Exceptions\ValidationException;

class OrderController
{
    public function store(Request $request)
    {
        $schema = new ValidateSchemaMiddleware([
            'name' => 'string|required|min:2',
            'description' => 'string',
            'required_race_id' => 'int',
            'attributes' => 'array|required',
        ]);

        $schema->handle($request->body());

        $dto = new CreateOrderDTO($request->body());

        $service = new CreateOrderService();
        $order = $service->execute($dto);

        return Response::json([
            'message' => 'Ordem criada com sucesso.',
            'order' => $order->toArray(),
        ], 201);
    }

    public function index()
    {
        $service = new GetAllOrdersService();
        $orders = $service->execute();

        return Response::json([
            'orders' => array_map(
                fn($order) => $order->toArray(),
                $orders
            ),
        ]);
    }

    public function show(Request $request)
    {
        $id = (int) ($request->params()['id'] ?? 0);

        if ($id <= 0) {
            throw new ValidationException('ID da ordem inválido.');
        }

        $service = new GetOrderByIdService();
        $order = $service->execute($id);

        return Response::json([
            'order' => $order->toArray(),
        ]);
    }
}
