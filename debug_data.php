<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fournisseurs = Illuminate\Support\Facades\DB::table('fournisseurs')->limit(3)->get();
print_r($fournisseurs);

$purchases = Illuminate\Support\Facades\DB::table('ffactures')
    ->join('fournisseurs', 'ffactures.fournisseurid', '=', 'fournisseurs.fournisseurid')
    ->select('ffactureid as id', 'ffactures.fournisseurid', 'fournisseurs.nom as fournisseur_nom')
    ->limit(3)->get();
print_r($purchases);
