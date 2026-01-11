<?php

namespace App\Core\Database;

use App\Core\Config\Env;
use PDO;
use PDOException;

class Connection
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (getenv('DATABASE_URL')) {
            self::$pdo = self::connectFromDatabaseUrl();
            return self::$pdo;
        }

        $rootPath = dirname(__DIR__, 3);
        Env::load($rootPath . '/.env');

        $config = require $rootPath . '/config/database.php';

        $connectionName = $config['default'];
        $dbConfig = $config['connections'][$connectionName];

        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $dbConfig['driver'],
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['database'],
            $dbConfig['charset']
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException(
                'Erro ao conectar ao banco: ' . $e->getMessage(),
                500
            );
        }

        return self::$pdo;
    }

    private static function connectFromDatabaseUrl(): PDO
    {
        $url = parse_url(getenv('DATABASE_URL'));

        $driver = $url['scheme'];
        $host   = $url['host'];
        $port   = $url['port'] ?? 3306;
        $db     = ltrim($url['path'], '/');
        $user   = $url['user'];
        $pass   = $url['pass'];

        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $driver,
            $host,
            $port,
            $db
        );

        try {
            return new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException(
                'Erro ao conectar ao banco (DATABASE_URL): ' . $e->getMessage(),
                500
            );
        }
    }
}
