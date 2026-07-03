<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 94; // Based on previous request "GET /bon-entree/94"
$bon = DB::table('fbls')
    ->join('fournisseurs', 'fbls.fournisseurid', '=', 'fournisseurs.fournisseurid')
    ->where('fblid', $id)
    ->select('fbls.*', 'fournisseurs.nom as fournisseur_nom')
    ->first();

echo "Bon ID $id:\n";
print_r($bon);
