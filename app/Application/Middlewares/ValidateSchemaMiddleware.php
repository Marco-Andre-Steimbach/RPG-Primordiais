<?php

namespace App\Application\Middlewares;

use App\Core\Exceptions\ValidationException;

class ValidateSchemaMiddleware
{
    private array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    public function handle(array $body)
    {
        foreach ($this->schema as $field => $rules) {
            $rules = explode('|', $rules);

            if (in_array('required', $rules) && !array_key_exists($field, $body)) {
                throw new ValidationException("Campo obrigatório ausente: $field");
            }

            if (!array_key_exists($field, $body)) {
                continue;
            }

            if (is_string($body[$field])) {
                $body[$field] = trim($body[$field]);
            }

            if (in_array('string', $rules) && !is_string($body[$field])) {
                throw new ValidationException("Campo '$field' deve ser string.");
            }

            foreach ($rules as $rule) {
                if (str_starts_with($rule, 'min:')) {
                    $min = intval(explode(':', $rule)[1]);
                    if (strlen($body[$field]) < $min) {
                        throw new ValidationException("Campo '$field' deve ter ao menos $min caracteres.");
                    }
                }
            }
        }
    }
}
