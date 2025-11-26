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

        // Carrega o .env uma única vez
        $rootPath = dirname(__DIR__, 3); // volta de /app/Core/Database até a raiz
        Env::load($rootPath . '/.env');

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $db   = Env::get('DB_NAME', 'rpg_system');
        $user = Env::get('DB_USER', 'root');
        $pass = Env::get('DB_PASS', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Aqui depois podemos jogar uma HttpException bonitinha
            throw new \RuntimeException('Erro ao conectar ao banco: ' . $e->getMessage(), 500);
        }

        return self::$pdo;
    }
}
