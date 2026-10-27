<?php

declare(strict_types=1);

namespace Tyto\Database\Migration;

use Tyto\Database\Database;

final class MigrationRepository
{
    public function ensureMigrationTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        Database::query($sql);
    }

    public function hasRun(string $migration): bool
    {
        $stmt = Database::prepareStatement("SELECT COUNT(*) FROM migrations WHERE migration = ?");
        $stmt->setString(1, $migration);
        
        $result = $stmt->fetch();
        return (bool) (array_values($result)[0] ?? 0);
    }

    public function log(string $migration, int $batch): void
    {
        $stmt = Database::prepareStatement("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->setString(1, $migration);
        $stmt->setInt(2, $batch);
        $stmt->execute();
    }

    public function lastBatch(): int
    {
        $result = Database::query("SELECT MAX(batch) as max_batch FROM migrations")->fetch();
        return (int) ($result['max_batch'] ?? 0);
    }
}