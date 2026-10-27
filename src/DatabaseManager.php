<?php

declare(strict_types=1);

namespace Tyto\Database;

use PDO;
use PDOException;
use RuntimeException;

final class DatabaseManager
{
    private static ?PDO $connection = null;

    public static function configure(array $config): void
    {
        $driver = $config['driver'] ?? 'mysql';
        
        if ($driver === 'sqlite') { 
            $dsn = "sqlite:" . ($config['database'] ?? ':memory:');
            $username = null;
            $password = null;
        } else {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 3306;
            $database = $config['database'] ?? '';
            $charset = $config['charset'] ?? 'utf8mb4';
            
            $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset={$charset}";
            $username = $config['username'] ?? null;
            $password = $config['password'] ?? null;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new RuntimeException("Erro na conexão com a base de dados: " . $e->getMessage());
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            throw new RuntimeException("A conexão com a base de dados não foi inicializada. Chame DatabaseManager::connect() primeiro.");
        }
        return self::$connection;
    }

    public static function columnExists(string $table, string $column): bool
    {}

    public static function disconnect(): void
    {
        self::$connection = null;
    }
    
}