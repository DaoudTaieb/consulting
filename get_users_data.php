<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users')->get();
echo "Total users: " . count($users) . "\n";
foreach ($users as $user) {
    echo "ID: " . $user->userid . " | Login: " . $user->login . " | Pass: [" . $user->password . "] | Len: " . strlen($user->password ?? '') . "\n";
}
