<?php

namespace App\Core\Exceptions;

use Exception;

class InternalErrorException extends Exception
{
    public function __construct(string $message = "Erro interno no servidor.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
