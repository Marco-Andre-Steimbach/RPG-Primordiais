<?php

namespace App\Core\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Core\Config\Env;

class TokenService
{
    public static function generate(array $payload): string
    {
        $secret = Env::get('JWT_SECRET');
        $expiresIn = Env::get('JWT_EXPIRES_IN', '1h');

        $payload['exp'] = time() + self::parseExpiration($expiresIn);

        return JWT::encode($payload, $secret, 'HS256');
    }

    public static function validate(string $token): ?array
    {
        try {
            $secret = Env::get('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array) $decoded;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function needsRefresh(array $decoded, int $thresholdSeconds = 1800): bool
    {
        if (empty($decoded['exp'])) {
            return false;
        }

        return ($decoded['exp'] - time()) <= $thresholdSeconds;
    }

    private static function parseExpiration(string $input): int
    {
        return match (true) {
            str_ends_with($input, 'h') => (int) $input * 3600,
            str_ends_with($input, 'm') => (int) $input * 60,
            default => (int) $input,
        };
    }
}
