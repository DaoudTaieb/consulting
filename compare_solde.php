<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$fournisseurs = DB::table('fournisseurs')
    ->select('fournisseurs.fournisseurid', 'fournisseurs.nom', 'fournisseurs.solde as table_solde')
    ->addSelect(DB::raw('
    (COALESCE(fournisseurs.soldeinitial, 0) + 
        COALESCE((SELECT SUM(netapayer) FROM ffactures WHERE fournisseurid = fournisseurs.fournisseurid), 0) +
        COALESCE((SELECT SUM(netapayer) FROM fbls WHERE fournisseurid = fournisseurs.fournisseurid AND transfere = false), 0) -
        COALESCE((SELECT SUM(montant) FROM freglements WHERE fournisseurid = fournisseurs.fournisseurid), 0) -
        COALESCE((SELECT SUM(netapayer) FROM favoirs WHERE fournisseurid = fournisseurs.fournisseurid), 0) -
        COALESCE((SELECT SUM(netapayer) FROM fbrs WHERE fournisseurid = fournisseurs.fournisseurid AND transfere = false), 0)
    ) as calculated_solde
'))
    ->get();

$diffs = [];
foreach ($fournisseurs as $f) {
    if (abs($f->table_solde - $f->calculated_solde) > 0.01) {
        $diffs[] = [
            'id' => $f->fournisseurid,
            'nom' => $f->nom,
            'table' => $f->table_solde,
            'calc' => $f->calculated_solde
        ];
    }
}

echo "Total suppliers: " . count($fournisseurs) . "\n";
echo "Suppliers with differences: " . count($diffs) . "\n\n";

if (count($diffs) > 0) {
    echo "First 10 differences:\n";
    foreach (array_slice($diffs, 0, 10) as $d) {
        echo "ID: {$d['id']} | {$d['nom']} | Table: {$d['table']} | Calc: {$d['calc']}\n";
    }
}
