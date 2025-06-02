<?php
require 'vendor/autoload.php';

// Path to your client secret file
$clientSecretPath = __DIR__ . '/client_secret_197862708310-402s844138kcbb3ifh871kaisln3ovqq.apps.googleusercontent.com.json';

// Load client secrets
$client = new Google_Client();
$client->setAuthConfig($clientSecretPath);
$client->addScope('email');
$client->addScope('profile');

// Generate authentication URL
$authUrl = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login with Google</title>
    <style>
        .google-btn {
            display: inline-block;
            background: #4285F4;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .google-btn:hover {
            background: #357ABD;
        }
    </style>
</head>
<body>
    <h1>Login with Google</h1>
    <a class="google-btn" href="<?= htmlspecialchars($authUrl) ?>">
        Sign in with Google
    </a>
</body>
</html>