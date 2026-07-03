<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$client = DB::table('clients')->where('clientcode', '4133278')->first();
if ($client) {
    echo "ID is " . $client->clientid . "\n";
} else {
    echo "Client not found\n";
}
