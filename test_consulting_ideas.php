<?php
$pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=gfm', 'postgres', 'mydb171819');

echo "=== CCOMMANDES MARGES ===\n";
$stmt = $pdo->query("SELECT COUNT(*), SUM(margeht) as total_marge FROM ccommandes WHERE margeht IS NOT NULL");
foreach ($stmt as $r) print_r($r);

echo "\n=== EMPLOYES / CHAUFFEURS ===\n";
$stmt = $pdo->query("SELECT COUNT(*) FROM employees");
foreach ($stmt as $r) print_r($r);
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='employees'");
foreach ($stmt as $r) echo $r['column_name'] . ", ";

echo "\n=== VEHICULES ===\n";
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='vehicules'");
foreach ($stmt as $r) echo $r['column_name'] . ", ";

echo "\n=== ALERTS STOCKS ===\n";
$stmt = $pdo->query("SELECT COUNT(*) FROM stocks WHERE qtestock <= qtemin");
if($stmt) foreach ($stmt as $r) print_r($r);
