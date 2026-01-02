<?php

namespace App\Domain\Services\Armors;

use App\Infrastructure\Repositories\ArmorRepository;

class GetAllArmorsService
{
    private ArmorRepository $armors;

    public function __construct()
    {
        $this->armors = new ArmorRepository();
    }

    public function execute(): array
    {
        return $this->armors->findAllWithItemAndSlot();
    }
}
