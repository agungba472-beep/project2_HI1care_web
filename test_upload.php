<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'pasien')->doesntHave('pasien')->first();
if (!$user) {
    echo "No such user found.";
    exit;
}

$request = new \Illuminate\Http\Request();
$request->setUserResolver(function() use ($user) { return $user; });
$file = \Illuminate\Http\UploadedFile::fake()->image('photo.jpg');
$request->files->set('photo', $file);

$controller = new \App\Http\Controllers\Api\ProfileController();
try {
    $response = $controller->uploadPhoto($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
