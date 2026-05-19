<?php

class Database
{
    private const CONFIG = [
        'host' => '127.0.0.1',
        'database' => 'progetto_ecommerce',
        'username' => 'root',
        'password' => '',
    ];

    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                self::CONFIG['host'],
                self::CONFIG['database']
            );

            self::$connection = new PDO($dsn, self::CONFIG['username'], self::CONFIG['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
?>
