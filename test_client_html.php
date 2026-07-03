<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = 2; // test with valid clientid 2
$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

try {
    $request = Illuminate\Http\Request::create("/client/{$id}/releve", 'GET');
    $response = app()->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 200 || $response->getStatusCode() == 500) {
        // Find if there's any Exception in the HTML output
        $content = $response->getContent();
        if (strpos($content, 'Exception') !== false || strpos($content, 'Error') !== false) {
            echo "Possible error found in output!\n";
            preg_match('/<title>(.*?)<\/title>/s', $content, $matches);
            if (isset($matches[1])) echo "Title: " . trim($matches[1]) . "\n";
            // extract first few lines of error message
            preg_match('/<div class="exception-message">(.*?)<\/div>/s', $content, $matches);
            if (isset($matches[1])) echo "Message: " . strip_tags(trim($matches[1])) . "\n";
        } else {
            echo "Page rendered successfully without apparent exceptions.\n";
        }
    } else {
        echo substr($response->getContent(), 0, 2000);
    }
} catch (\Exception $e) {
    echo "Exception thrown directly: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
