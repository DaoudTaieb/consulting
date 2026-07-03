<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$row = DB::table('fbls')
    ->whereNotNull('numerointerne')
    ->where('numerointerne', '<>', '')
    ->first();

if ($row) {
    echo "Found record with numerointerne:\n";
    print_r($row);
} else {
    echo "No records with numerointerne found.\n";
}
