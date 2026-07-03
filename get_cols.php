<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$cols = Illuminate\Support\Facades\Schema::getColumnListing('detfbls');
echo implode("\n", $cols);
echo "\n---\n";
$cols2 = Illuminate\Support\Facades\Schema::getColumnListing('detfbrs');
echo implode("\n", $cols2);
