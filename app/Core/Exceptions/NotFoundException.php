<?php

namespace App\Core\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $message = "Recurso não encontrado.", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
