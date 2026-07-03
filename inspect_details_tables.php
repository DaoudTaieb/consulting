<?php
use Illuminate\Support\Facades\DB;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function showColumns($table)
{
    echo "\n--- $table ---\n";
    $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = ?", [$table]);
    foreach ($columns as $c) {
        echo "- {$c->column_name} ({$c->data_type})\n";
    }
}

showColumns('detfbls');
showColumns('detfbrs');
showColumns('produits');
