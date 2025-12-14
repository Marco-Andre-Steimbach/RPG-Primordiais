<?php

namespace App\Core\Exceptions;

use Throwable;
use App\Core\Config\Env;

class ExceptionHandler
{
    public static function handle(Throwable $e): void
    {
        http_response_code(self::resolveStatus($e));
        header('Content-Type: application/json');

        $isDev = Env::get('APP_ENV', 'prod') === 'dev';

        $response = [
            'error' => true,
            'message' => $e->getMessage(),
            'type' => (new \ReflectionClass($e))->getShortName(),
        ];

        if ($isDev) {
            $response['file'] = $e->getFile();
            $response['line'] = $e->getLine();
            $response['trace'] = $e->getTraceAsString();
        }

        echo json_encode($response);
        exit;
    }

    private static function resolveStatus(Throwable $e): int
    {
        return match (true) {
            $e instanceof ValidationException => 400,
            $e instanceof BadRequestException => 400,
            $e instanceof UnauthorizedException => 401,
            $e instanceof ForbiddenException => 403,
            $e instanceof NotFoundException => 404,
            $e instanceof ConflictException => 409,
            $e instanceof UnprocessableEntityException => 422,
            $e instanceof InternalErrorException => 500,
            default => 500,
        };
    }
}
