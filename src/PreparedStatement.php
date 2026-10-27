<?php

declare(strict_types=1);

namespace Tyto\Database;

use PDO;
use PDOStatement;

final class PreparedStatement
{
    private array $bindings = [];

    public function __construct(
        private readonly PDOStatement $stmt,
        private readonly string $sql
    ) {}

    public function setString(int $position, string $value): self
    {
        $this->bindings[$position] = [
            'value' => $value,
            'type'  => PDO::PARAM_STR
        ];
        return $this;
    }

    public function setInt(int $position, int $value): self
    {
        $this->bindings[$position] = [
            'value' => $value,
            'type'  => PDO::PARAM_INT
        ];
        return $this;
    }

    public function setFloat(int $position, float $value): self
    {
        $this->bindings[$position] = [
            'value' => (string) $value,
            'type' => PDO::PARAM_STR
        ];

        return $this;
    }

    public function setDate(int $position, string $value): self
    {
        $this->bindings[$position] = [
            'value' => $value,
            'type'  => PDO::PARAM_STR
        ];
        return $this;
    }

    public function execute(): bool
    {
        foreach ($this->bindings as $pos => $data) {
            $this->stmt->bindValue($pos, $data['value'], $data['type']);
        }

        return $this->stmt->execute();
    }

    public function fetchAll(): array
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetch(): ?array
    {
        $this->execute();
        $result = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }
}