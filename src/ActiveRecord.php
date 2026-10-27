<?php

declare(strict_types=1);

namespace Tyto\Database;

use PDO;
use ReflectionClass;

abstract class ActiveRecord
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public static function getTable(): string
    {
        if (static::$table !== '') {
            return static::$table;
        }
        $className = (new ReflectionClass(static::class))->getShortName();
        return strtolower($className) . 's';
    }

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::getTable());
    }

    public static function find(int|string $id): ?static
    {
        $table = static::getTable();
        $pk = static::$primaryKey;

        $pdo = DatabaseManager::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE {$pk} = ? LIMIT 1");
        $stmt->execute([$id]);
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return new static($data);
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}