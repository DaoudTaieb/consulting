<?php
use Illuminate\Support\Facades\DB;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$patterns = ['v%', 'view%', '%releve%', '%mouv%', '%journal%'];
echo "Searching for relevant tables/views...\n";
foreach ($patterns as $pattern) {
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_name LIKE ? AND table_schema = 'public'", [$pattern]);
    foreach ($tables as $t) {
        echo $t->table_name . "\n";
    }
}
