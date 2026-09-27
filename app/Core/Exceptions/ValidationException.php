<?php

namespace App\Core\Exceptions;

class ValidationException extends \Exception
{
    protected array $errors = [];

    public function __construct(
        string $message,
        array $errors = [],
        int $code = 400
    ) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
