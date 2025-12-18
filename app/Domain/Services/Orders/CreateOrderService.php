<?php

namespace App\Domain\Services\Orders;

use App\Application\DTOs\Orders\CreateOrderDTO;
use App\Core\Exceptions\ConflictException;
use App\Core\Exceptions\ValidationException;
use App\Domain\Models\Order;
use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\OrderAttributeRepository;

class CreateOrderService
{
    private OrderRepository $orders;
    private OrderAttributeRepository $attributes;

    public function __construct()
    {
        $this->orders = new OrderRepository();
        $this->attributes = new OrderAttributeRepository();
    }

    public function execute(CreateOrderDTO $dto): Order
    {
        if ($this->orders->existsByName($dto->name)) {
            throw new ConflictException('Já existe uma ordem com esse nome.');
        }

        $orderId = $this->orders->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'required_race_id' => $dto->required_race_id,
        ]);

        if (!$orderId) {
            throw new ValidationException('Falha ao criar ordem.');
        }

        foreach ($dto->attributes as $attribute) {
            $this->attributes->attach(
                $orderId,
                $attribute['name'],
                $attribute['value']
            );
        }

        $order = $this->orders->findById($orderId);

        if (!$order) {
            throw new ValidationException('Erro ao carregar a ordem criada.');
        }

        $rawAttributes = $this->attributes->getByOrder($orderId);

        $order->attributes = array_map(
            fn($name, $value) => [
                'name' => $name,
                'value' => $value,
            ],
            array_keys($rawAttributes),
            $rawAttributes
        );

        return $order;
    }
}
