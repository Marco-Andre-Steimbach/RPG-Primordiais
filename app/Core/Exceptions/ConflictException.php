<?php

namespace App\Core\Exceptions;

use Exception;

class ConflictException extends Exception
{
    public function __construct(string $message = "Conflito de dados.", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
