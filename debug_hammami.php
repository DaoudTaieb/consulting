<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 244;

$soldeinitial = DB::table('fournisseurs')->where('fournisseurid', $id)->value('soldeinitial');
$solde = DB::table('fournisseurs')->where('fournisseurid', $id)->value('solde');

$ffactures = DB::table('ffactures')->where('fournisseurid', $id)->sum('netapayer');
$fbls = DB::table('fbls')->where('fournisseurid', $id)->where('transfere', false)->sum('netapayer');
$freglements = DB::table('freglements')->where('fournisseurid', $id)->sum('montant');
$favoirs = DB::table('favoirs')->where('fournisseurid', $id)->sum('netapayer');
$fbrs = DB::table('fbrs')->where('fournisseurid', $id)->where('transfere', false)->sum('netapayer');

echo "Solde initial: " . $soldeinitial . "\n";
echo "Solde table: " . $solde . "\n";
echo "Factures: " . $ffactures . "\n";
echo "BLs (non transferés): " . $fbls . "\n";
echo "Règlements: " . $freglements . "\n";
echo "Avoirs: " . $favoirs . "\n";
echo "BRs (non transferés): " . $fbrs . "\n";

$calculated = $soldeinitial + $ffactures + $fbls - $freglements - $favoirs - $fbrs;
echo "Calculated: " . $calculated . "\n";
