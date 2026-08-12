<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$credentials = [
    'email' => 'superadmin@hotelbooking.com',
    'password' => 'password',
];

$success = \Illuminate\Support\Facades\Auth::guard('admin')->attempt($credentials);
echo "Attempt with 'password': " . ($success ? "true" : "false") . "\n";

if (!$success) {
    // Reset password
    $admin = App\Models\Admin::where('email', 'superadmin@hotelbooking.com')->first();
    $admin->password = \Illuminate\Support\Facades\Hash::make('password123');
    $admin->save();
    echo "Password reset to 'password123'\n";
    $credentials['password'] = 'password123';
    $success = \Illuminate\Support\Facades\Auth::guard('admin')->attempt($credentials);
    echo "Attempt with 'password123': " . ($success ? "true" : "false") . "\n";
}
