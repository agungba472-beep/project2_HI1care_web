<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::whereNotNull('expo_push_token')->get(['id', 'username', 'expo_push_token']);
echo "Tokens:\n" . $users->toJson(JSON_PRETTY_PRINT);
