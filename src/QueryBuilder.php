<?php

declare(strict_types=1);

namespace TytoPhp\Database;

use PDO;

final class QueryBuilder
{
    private string $table;
    private array $wheres = [];
    private array $bindings = [];
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        $this->bindings[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = compact('column', 'direction');
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function toSQL(): string
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $clauses = [];
            foreach ($this->wheres as $where) {
                $clauses[] = "{$where['column']} {$where['operator']} ?";
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        if (!empty($this->orders)) {
            $orderClauses = array_map(fn($o) => "{$o['column']} {$o['direction']}", $this->orders);
            $sql .= ' ORDER BY ' . implode(', ', $orderClauses);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function get(): array
    {
        $pdo = DatabaseManager::getConnection();
        $stmt = $pdo->prepare($this->toSQL());
        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
}