<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'fbrs';
$columns = Schema::getColumnListing($table);

echo "Columns in $table:\n";
print_r($columns);

$row = DB::table($table)->first();
echo "\nFirst row sample:\n";
print_r($row);
