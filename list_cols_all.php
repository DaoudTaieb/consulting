<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- detfbls ---\n";
print_r(Schema::getColumnListing('detfbls'));
echo "--- detfbrs ---\n";
print_r(Schema::getColumnListing('detfbrs'));
echo "--- produits ---\n";
print_r(Schema::getColumnListing('produits'));
