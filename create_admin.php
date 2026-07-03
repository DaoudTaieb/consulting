<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// If they have no users, we might need to specify a userid if it's not auto-increment
$maxId = DB::table('users')->max('userid') ?: 0;

$user = User::updateOrCreate(
    ['login' => 'admin'],
    [
        'userid' => $maxId + 1,
        'password' => Hash::make('password'),
    ]
);

echo "User created: " . $user->login . " with password: password\n";
