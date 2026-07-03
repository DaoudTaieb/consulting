<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");

echo "Tables list:\n";
foreach ($tables as $table) {
    echo "- " . $table->table_name . "\n";
}

echo "\nDetailed Structure:\n";
foreach ($tables as $table) {
    echo "\n--- Table: " . $table->table_name . " ---\n";
    $columns = DB::select("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = ?", [$table->table_name]);
    foreach ($columns as $column) {
        echo "  - " . $column->column_name . " (" . $column->data_type . ")" . ($column->is_nullable === 'YES' ? " (NULLABLE)" : "") . "\n";
    }
}
