<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "SITES:\n";
print_r(Schema::getColumnListing('sites'));

echo "\nFAMILLES:\n";
print_r(Schema::getColumnListing('familles'));
