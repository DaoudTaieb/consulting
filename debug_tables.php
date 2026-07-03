<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
foreach($tables as $t) {
    if (strpos($t->table_name, 'det') === 0 || strpos($t->table_name, 'facture') !== false) {
        echo $t->table_name . "\n";
    }
}
