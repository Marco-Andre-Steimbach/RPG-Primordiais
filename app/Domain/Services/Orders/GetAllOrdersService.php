<?php

namespace App\Domain\Services\Orders;

use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\OrderAttributeRepository;

class GetAllOrdersService
{
    private OrderRepository $orders;
    private OrderAttributeRepository $attributes;

    public function __construct()
    {
        $this->orders = new OrderRepository();
        $this->attributes = new OrderAttributeRepository();
    }

    public function execute(): array
    {
        $rows = $this->orders->findAll();

        $orders = [];

        foreach ($rows as $row) {
            $order = $this->orders->findById((int) $row['id']);

            if ($order) {
                $order->attributes = $this->attributes->getByOrder($order->id);
                $orders[] = $order;
            }
        }

        return $orders;
    }
}
