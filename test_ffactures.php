<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "ffactures:\n";
print_r(Schema::getColumnListing('ffactures'));

echo "\nfbls:\n";
print_r(Schema::getColumnListing('fbls'));
