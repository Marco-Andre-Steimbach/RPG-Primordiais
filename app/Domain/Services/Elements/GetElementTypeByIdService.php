<?php

namespace App\Domain\Services\Elements;

use App\Infrastructure\Repositories\ElementTypeRepository;
use App\Core\Exceptions\NotFoundException;

class GetElementTypeByIdService
{
    private ElementTypeRepository $elements;

    public function __construct()
    {
        $this->elements = new ElementTypeRepository();
    }

    public function execute(int $id)
    {
        $element = $this->elements->findById($id);

        if (!$element) {
            throw new NotFoundException('Elemento não encontrado.');
        }

        return $element;
    }
}
