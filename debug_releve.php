<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 269;
$factures = DB::table('ffactures')->where('fournisseurid', $id)->select('ffactureid', 'ffacturenumero', 'totalttc', 'netapayer')->get();
print_r($factures->toArray());
