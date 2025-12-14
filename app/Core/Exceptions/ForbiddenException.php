<?php

namespace App\Core\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public function __construct(string $message = "Acesso negado.", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
