<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$user = User::updateOrCreate(
    ['login' => 'Auto'],
    [
        'password' => 'auto',
        'userdroitid' => 1,
        'societeid' => 1,
        'employeeid' => 1,
        'siteid' => 100,
        'plafonremise' => 0.0,
    ]
);

echo "Utilisateur Auto ajouté (login: Auto, mot de passe: auto)\n";
