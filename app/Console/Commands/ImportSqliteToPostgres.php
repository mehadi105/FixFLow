<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportSqliteToPostgres extends Command
{
    protected $signature = 'db:import-sqlite
                            {--path= : Path to the SQLite database file}
                            {--fresh : Truncate Postgres tables before import}';

    protected $description = 'Copy application data from a SQLite file into the current (PostgreSQL) database';

    /**
     * Tables in foreign-key-safe insert order.
     *
     * @var list<string>
     */
    private array $tables = [
        'users',
        'repair_requests',
        'invoices',
        'warranties',
        'messages',
        'technician_applications',
    ];

    public function handle(): int
    {
        if (config('database.default') !== 'pgsql') {
            $this->error('Default connection must be pgsql (got '.config('database.default').').');

            return self::FAILURE;
        }

        $path = $this->option('path') ?: database_path('database.sqlite');

        if (! is_file($path)) {
            $this->error("SQLite file not found: {$path}");

            return self::FAILURE;
        }

        config([
            'database.connections.sqlite_import' => [
                'driver' => 'sqlite',
                'database' => $path,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        DB::purge('sqlite_import');

        if ($this->option('fresh')) {
            $this->truncatePostgresTables();
        }

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($this->tables as $table) {
                $this->importTable($table);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        foreach ($this->tables as $table) {
            $this->resetSequence($table);
        }

        $this->info('SQLite → PostgreSQL import complete.');

        return self::SUCCESS;
    }

    private function truncatePostgresTables(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (array_reverse($this->tables) as $table) {
            DB::table($table)->truncate();
            $this->line("Truncated {$table}");
        }

        Schema::enableForeignKeyConstraints();
    }

    private function importTable(string $table): void
    {
        $rows = DB::connection('sqlite_import')->table($table)->orderBy('id')->get();

        if ($rows->isEmpty()) {
            $this->line("{$table}: 0 rows (skipped)");

            return;
        }

        $payload = $rows->map(fn ($row) => (array) $row)->all();

        foreach (array_chunk($payload, 100) as $chunk) {
            DB::table($table)->insert($chunk);
        }

        $this->info("{$table}: {$rows->count()} rows imported");
    }

    private function resetSequence(string $table): void
    {
        $maxId = DB::table($table)->max('id');

        if ($maxId === null) {
            return;
        }

        DB::statement(
            "SELECT setval(pg_get_serial_sequence(?, 'id'), ?, true)",
            [$table, (int) $maxId]
        );
    }
}
