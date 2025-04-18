<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class GenerateDbDocs extends Command
{
    protected $signature = 'generate:dbdocs';
    protected $description = 'Generate Markdown documentation for the database models';

    public function handle(): void
    {
        $outputPath = base_path('docs/database.md');
        File::ensureDirectoryExists(dirname($outputPath));

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        $markdown = "# 📘 Database Documentation\n\n";

        foreach ($tables as $table) {
            $markdown .= "## 🗂️ `$table`\n\n";
            $markdown .= "| Column | Type | Nullable | Default |\n";
            $markdown .= "|--------|------|----------|---------|\n";

            $columns = Schema::getColumnListing($table);

            foreach ($columns as $column) {
                $type = DB::getSchemaBuilder()->getColumnType($table, $column);
                $columnDetails = DB::connection()->getDoctrineColumn($table, $column);
                $nullable = $columnDetails->getNotnull() ? 'No' : 'Yes';
                $default = $columnDetails->getDefault() ?? '—';

                $markdown .= "| `$column` | `$type` | $nullable | $default |\n";
            }

            $markdown .= "\n";
        }

        File::put($outputPath, $markdown);
        $this->info("✅ Database documentation generated at: `docs/database.md`");
    }
}
