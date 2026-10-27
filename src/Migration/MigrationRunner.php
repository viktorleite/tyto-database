<?php

declare(strict_types=1);

namespace Tyto\Database\Migration;

final class MigrationRunner
{
    public function __construct(
        private readonly MigrationRepository $repository
    ) {}

    public function run(string $migrationsPath): void
    {
        $this->repository->ensureMigrationTableExists();

        $batch = $this->repository->lastBatch() + 1;
        $files = glob($migrationsPath . '/*.php');

        if ($files === false) {
            return;
        }

        sort($files);

        foreach ($files as $file) {
            $name = basename($file, '.php');

            if ($this->repository->hasRun($name)) {
                continue;
            }

            echo "Executando migration: {$name}\n";

            $migration = require $file;
            if ($migration instanceof Migration) {
                $migration->up();
                $this->repository->log($name, $batch);
                echo "Concluído: {$name}\n";
            }
        }
    }
}