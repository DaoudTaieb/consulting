<?php
$pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=gfm', 'postgres', 'mydb171819');
$res1 = $pdo->query('SELECT MAX(date) FROM cfactures')->fetchColumn();
$res2 = $pdo->query('SELECT MAX(date) FROM ffactures')->fetchColumn();
$res3 = $pdo->query('SELECT MAX(date) FROM depenses')->fetchColumn();
echo "Max date cfactures: " . $res1 . "\n";
echo "Max date ffactures: " . $res2 . "\n";
echo "Max date depenses: " . $res3 . "\n";
