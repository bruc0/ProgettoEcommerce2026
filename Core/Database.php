<?php

class Database
{
    private const LOCAL_CONFIG = [
        'host' => '127.0.0.1',
        'database' => 'progetto_ecommerce',
        'username' => 'root',
        'password' => '',
    ];

    private const ALTERVISTA_CONFIG = [
        'host' => 'localhost',
        'database' => 'my_rtbcars',
        'username' => 'rtbcars',
        'password' => '',
    ];

    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = self::getConfig();
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['database']
            );

            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }

    private static function getConfig(): array
    {
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));

        if (strpos($host, 'altervista.org') !== false) {
            return self::ALTERVISTA_CONFIG;
        }

        return self::LOCAL_CONFIG;
    }
}
?>
