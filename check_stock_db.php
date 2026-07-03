<?php
/**
 * Vérifie pourquoi la page Stock affiche vide.
 * Usage: php check_stock_db.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VÉRIFICATION STOCK (base " . config('database.connections.pgsql.database') . ") ===\n\n";

// 1. Nombre total de lignes dans stocks
$totalStocks = DB::table('stocks')->count();
echo "1. Table 'stocks' : {$totalStocks} ligne(s).\n";

if ($totalStocks === 0) {
    echo "   => La table 'stocks' est VIDE. C'est pour cela que la page Stock affiche 0 produit.\n\n";
}

// 2. Tables liées : produits, sites
$nbProduits = DB::table('produits')->count();
$nbSites = DB::table('sites')->count();
echo "2. Table 'produits' : {$nbProduits} produit(s).\n";
echo "   Table 'sites' : {$nbSites} site(s).\n\n";

// 3. Détails des bons (entrée/sortie) : est-ce que du stock est censé venir d'ailleurs ?
$nbDetFbl = DB::table('detfbls')->count();
$nbDetFbr = DB::table('detfbrs')->count();
echo "3. Lignes de détails bons entrée (detfbls) : {$nbDetFbl}.\n";
echo "   Lignes de détails bons sortie (detfbrs) : {$nbDetFbr}.\n\n";

echo "--- Conclusion ---\n";
if ($totalStocks === 0) {
    echo "L'application affiche les lignes de la table 'stocks'. Cette table n'est pas alimentée.\n";
    echo "Selon votre métier, il faut soit :\n";
    echo "  - Alimenter 'stocks' depuis un autre logiciel / script / trigger ;\n";
    echo "  - Ou créer un job/migration qui remplit 'stocks' à partir des mouvements (detfbls, detfbrs, etc.).\n";
}
echo "\n";
