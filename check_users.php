<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \App\Models\User::count();
    echo "User count: $count\n";
    if ($count > 0) {
        $users = \App\Models\User::all(['id', 'email', 'role']);
        foreach ($users as $user) {
            echo "User: {$user->id}, {$user->email}, {$user->role}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
