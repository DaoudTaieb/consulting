<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $client = DB::table('clients')->first();
    if (!$client) die("No client found");
    $id = $client->clientid;

    $request = Request::create("/client/{$id}/releve", 'GET');
    $response = app()->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() >= 400) {
        echo substr($response->getContent(), 0, 2000);
    } else {
        echo "Success!";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
