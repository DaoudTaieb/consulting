<?php
$pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=gfm', 'postgres', 'mydb171819');

echo "=== STOCKS columns ===\n";
$stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name='stocks' ORDER BY ordinal_position");
foreach ($stmt as $r) echo "  {$r['column_name']} ({$r['data_type']})\n";

echo "\n=== CA par client (top 10) ===\n";
$stmt = $pdo->query("SELECT c.nom, SUM(f.totalttc) as total_ca, COUNT(*) as nb_factures FROM cfactures f JOIN clients c ON f.clientid = c.clientid GROUP BY c.nom ORDER BY total_ca DESC LIMIT 10");
foreach ($stmt as $r) echo "  {$r['nom']}: CA={$r['total_ca']}, {$r['nb_factures']} factures\n";

echo "\n=== CA par fournisseur (top 10) ===\n";
$stmt = $pdo->query("SELECT fo.nom, SUM(f.totalttc) as total_achats, COUNT(*) as nb_factures FROM ffactures f JOIN fournisseurs fo ON f.fournisseurid = fo.fournisseurid GROUP BY fo.nom ORDER BY total_achats DESC LIMIT 10");
foreach ($stmt as $r) echo "  {$r['nom']}: Achats={$r['total_achats']}, {$r['nb_factures']} factures\n";

echo "\n=== CLIENTS avec solde négatif (trop payé) ===\n";
$stmt = $pdo->query("SELECT nom, solde FROM clients WHERE solde < 0 ORDER BY solde LIMIT 10");
foreach ($stmt as $r) echo "  {$r['nom']}: {$r['solde']}\n";

echo "\n=== FCOMMANDES columns ===\n";
$stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name='fcommandes' ORDER BY ordinal_position");
foreach ($stmt as $r) echo "  {$r['column_name']} ({$r['data_type']})\n";

echo "\n=== CCOMMANDES columns ===\n";
$stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name='ccommandes' ORDER BY ordinal_position");
foreach ($stmt as $r) echo "  {$r['column_name']} ({$r['data_type']})\n";

echo "\n=== AGENCEBS (banques) ===\n";
$stmt = $pdo->query("SELECT * FROM agencebs ORDER BY agencebid");
foreach ($stmt as $r) { foreach($r as $k=>$v) { if(!is_numeric($k)) echo "  $k=$v"; } echo "\n"; }

echo "\n=== CHEQUES par status ===\n";
$stmt = $pdo->query("SELECT s.libelle, COUNT(*) as nb FROM cheques c JOIN statuscheques s ON c.statuschequeid = s.statuschequeid GROUP BY s.libelle");
foreach ($stmt as $r) echo "  {$r['libelle']}: {$r['nb']}\n";

echo "\n=== FREGLEMENTS avec echéance future ===\n";
$stmt = $pdo->query("SELECT f.date, f.montant, f.echeance, fo.nom, m.libelle as mode FROM freglements f JOIN fournisseurs fo ON f.fournisseurid = fo.fournisseurid LEFT JOIN modereglements m ON f.modereglementid = m.modereglementid WHERE f.echeance > NOW() ORDER BY f.echeance LIMIT 10");
foreach ($stmt as $r) echo "  {$r['nom']}: {$r['montant']} ({$r['mode']}) échéance={$r['echeance']}\n";

echo "\n=== CREGLEMENTS avec echéance future ===\n";
$stmt = $pdo->query("SELECT c.date, c.montant, c.echeance, cl.nom, m.libelle as mode FROM creglements c JOIN clients cl ON c.clientid = cl.clientid LEFT JOIN modereglements m ON c.modereglementid = m.modereglementid WHERE c.echeance > NOW() ORDER BY c.echeance LIMIT 10");
foreach ($stmt as $r) echo "  {$r['nom']}: {$r['montant']} ({$r['mode']}) échéance={$r['echeance']}\n";

echo "\n=== FREGLEMENTS impayés (statusreglementid) ===\n";
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='freglements' AND column_name LIKE '%status%'");
foreach ($stmt as $r) echo "  col: {$r['column_name']}\n";
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='statusreglements'");
foreach ($stmt as $r) echo "  statusreglements col: {$r['column_name']}\n";
$stmt = $pdo->query("SELECT * FROM statusreglements");
foreach ($stmt as $r) { foreach($r as $k=>$v) { if(!is_numeric($k)) echo "  $k=$v"; } echo "\n"; }

echo "\n=== FREGLEMENTS par status ===\n";
$stmt = $pdo->query("SELECT sr.libelle, COUNT(*) as nb, SUM(f.montant) as total FROM freglements f LEFT JOIN statusreglements sr ON f.statusreglementid = sr.statusreglementid GROUP BY sr.libelle");
foreach ($stmt as $r) echo "  {$r['libelle']}: {$r['nb']} reglements, total={$r['total']}\n";

echo "\n=== CREGLEMENTS par status ===\n";
$stmt = $pdo->query("SELECT sr.libelle, COUNT(*) as nb, SUM(c.montant) as total FROM creglements c LEFT JOIN statusreglements sr ON c.statusreglementid = sr.statusreglementid GROUP BY sr.libelle");
foreach ($stmt as $r) echo "  {$r['libelle']}: {$r['nb']} reglements, total={$r['total']}\n";
