<?php

namespace App\Core\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public function __construct(string $message = "Não autorizado.", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
