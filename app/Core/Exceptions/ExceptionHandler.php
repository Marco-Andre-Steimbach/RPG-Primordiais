<?php

namespace App\Core\Exceptions;

use Throwable;

class ExceptionHandler
{
    public static function handle(Throwable $e): void
    {
        http_response_code(self::resolveStatus($e));

        header('Content-Type: application/json');

        echo json_encode([
            'error' => true,
            'message' => $e->getMessage(),
            'type' => (new \ReflectionClass($e))->getShortName(),
        ]);

        exit;
    }

    private static function resolveStatus(Throwable $e): int
    {
        return match(true) {
            $e instanceof ValidationException => 400,
            $e instanceof BadRequestException => 400,
            $e instanceof UnauthorizedException => 401,
            $e instanceof ForbiddenException => 403,
            $e instanceof NotFoundException => 404,
            default => 500,
        };
    }
}
