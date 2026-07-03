<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['sites', 'familles', 'sousfamilles', 'fournisseurs'];
$results = [];

foreach ($tables as $table) {
    $results[$table] = Schema::getColumnListing($table);
}

echo json_encode($results, JSON_PRETTY_PRINT);
