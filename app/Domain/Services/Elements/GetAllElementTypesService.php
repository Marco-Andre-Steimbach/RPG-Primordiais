<?php

namespace App\Domain\Services\Elements;

use App\Infrastructure\Repositories\ElementTypeRepository;

class GetAllElementTypesService
{
    private ElementTypeRepository $elements;

    public function __construct()
    {
        $this->elements = new ElementTypeRepository();
    }

    public function execute(): array
    {
        $rows = $this->elements->findAll();

        $elements = [];

        foreach ($rows as $row) {
            $element = $this->elements->findById((int) $row['id']);

            if ($element) {
                $elements[] = $element;
            }
        }

        return $elements;
    }
}