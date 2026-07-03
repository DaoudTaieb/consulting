<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = 4133278;
$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

try {
    $request = Illuminate\Http\Request::create("/client/{$id}/releve", 'GET');
    $request->headers->set('X-Inertia', 'true');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    $response = app()->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 200) {
        $data = json_decode($response->getContent(), true);
        echo "Inertia Page: " . $data['component'] . "\n";
    } else {
        echo substr($response->getContent(), 0, 2000);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
