<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing token creation...\n";

// Get a user
$user = Illuminate\Support\Facades\DB::table('users')->first();
if (!$user) {
    echo "No users found in database\n";
    exit(1);
}

echo "User ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";

// Load the user model
$userModel = App\Models\User::find($user->id);
if (!$userModel) {
    echo "Could not load user model\n";
    exit(1);
}

// Try to create a token
try {
    $token = $userModel->createToken('test-token');
    echo "Token created successfully!\n";
    echo "Token ID: " . $token->accessToken->id . "\n";
    echo "Token: " . $token->plainTextToken . "\n";
} catch (Exception $e) {
    echo "Token creation failed: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}