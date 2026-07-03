<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

foreach (['detfbls', 'detfbrs', 'produits'] as $table) {
    echo "--- Table: $table ---\n";
    print_r(Schema::getColumnListing($table));
    $row = DB::table($table)->first();
    echo "Sample row:\n";
    print_r($row);
    echo "\n";
}
