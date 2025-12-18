<?php

namespace App\Domain\Services\Orders;

use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\OrderAttributeRepository;
use App\Core\Exceptions\NotFoundException;

class GetOrderByIdService
{
    private OrderRepository $orders;
    private OrderAttributeRepository $attributes;

    public function __construct()
    {
        $this->orders = new OrderRepository();
        $this->attributes = new OrderAttributeRepository();
    }

    public function execute(int $id)
    {
        $order = $this->orders->findById($id);

        if (!$order) {
            throw new NotFoundException('Ordem não encontrada.');
        }

        $order->attributes = $this->attributes->getByOrder($id);

        return $order;
    }
}
