<?php

namespace App\Core\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    public function __construct(string $message = "Requisição inválida.", int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
