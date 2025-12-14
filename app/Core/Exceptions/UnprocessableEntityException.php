<?php

namespace App\Core\Exceptions;

use Exception;

class UnprocessableEntityException extends Exception
{
    public function __construct(string $message = "Dados não puderam ser processados.", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
