<?php

declare(strict_types=1);

namespace Tyto\Database\Migration;

use Tyto\Database\Database;
use Tyto\Database\PreparedStatement;

abstract class Migration
{
    abstract public function up(): void;
    abstract public function down(): void;

    protected function exec(string $sql): bool
    {
        return Database::query($sql)->execute();
    }

    protected function prepareStatement(string $sql): PreparedStatement
    {
        return Database::prepareStatement($sql);
    }
}