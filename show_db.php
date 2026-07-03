<?php
/**
 * Affiche les infos de la base de données (tables et colonnes principales).
 * Usage: php show_db.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== BASE DE DONNÉES ===\n\n";

try {
    $driver = config('database.default');
    $dbName = config("database.connections.{$driver}.database");
    $host = config("database.connections.{$driver}.host");
    $port = config("database.connections.{$driver}.port");
    echo "Connexion : {$driver}\n";
    echo "Base      : {$dbName}\n";
    echo "Hôte      : {$host}:{$port}\n\n";

    if ($driver === 'pgsql') {
        $tables = DB::select("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'public'
            ORDER BY tablename
        ");
        $list = array_column($tables, 'tablename');
        echo "Nombre de tables : " . count($list) . "\n\n";

        // Tables utilisées par l'app (prioritaires)
        $appTables = [
            'users', 'fournisseurs', 'fournisseurfamilles', 'ffactures', 'freglements', 'favoirs',
            'fbls', 'fbrs', 'detfbls', 'detfbrs', 'produits', 'stocks', 'sites', 'familles', 'sousfamilles',
            'journalcaisses', 'journalcaissedets', 'caisses', 'sectionclotures',
        ];

        echo "--- TABLES PRINCIPALES (utilisées par l'app) ---\n\n";
        foreach ($appTables as $t) {
            if (!in_array($t, $list, true)) {
                echo "[?] {$t} (non trouvée)\n";
                continue;
            }
            $cols = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns
                WHERE table_schema = 'public' AND table_name = ?
                ORDER BY ordinal_position
            ", [$t]);
            echo "Table: {$t}\n";
            foreach ($cols as $c) {
                echo "  - {$c->column_name} ({$c->data_type}) " . ($c->is_nullable === 'YES' ? 'nullable' : '') . "\n";
            }
            echo "\n";
        }

        echo "--- TOUTES LES TABLES (public) ---\n\n";
        echo implode(", ", $list) . "\n";
    }
} catch (\Throwable $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
