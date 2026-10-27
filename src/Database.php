<?php

declare(strict_types=1);

namespace Tyto\Database;

final class Database
{
    public static function prepareStatement(string $sql): PreparedStatement
    {
        $pdo = DatabaseManager::getConnection();
        $stmt = $pdo->prepare($sql);
        return new PreparedStatement($stmt, $sql);
    }

    
    public static function query(string $sql): \PDOStatement
    {
        return DatabaseManager::getConnection()->query($sql);
    }
}