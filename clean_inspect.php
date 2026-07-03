<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function inspect($table)
{
    echo "--- Table: $table ---\n";
    $cols = Schema::getColumnListing($table);
    foreach ($cols as $c)
        echo "$c, ";
    echo "\nSample Row:\n";
    $row = DB::table($table)->first();
    if ($row) {
        foreach ((array) $row as $k => $v) {
            echo "  $k => $v\n";
        }
    } else {
        echo "  No data\n";
    }
}

inspect('detfbls');
inspect('produits');
