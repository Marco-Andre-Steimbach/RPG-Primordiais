<?php

use App\Core\Config\Env;

return [

    'default' => Env::get('DB_CONNECTION', 'mysql'),

    'connections' => [

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => Env::get('DB_HOST', '127.0.0.1'),
            'port'      => Env::get('DB_PORT', '3306'),
            'database'  => Env::get('DB_NAME', 'rpg_system'),
            'username'  => Env::get('DB_USER', 'root'),
            'password'  => Env::get('DB_PASS', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],

    ],

];
