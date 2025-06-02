<?php
require 'vendor/autoload.php';
require 'auth_db.php';

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/google_auth.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();
    
    // Check if user exists in database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$userInfo->email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Register new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, google_id) VALUES (?, ?, ?)");
        $username = str_replace(' ', '', $userInfo->name) . rand(100, 999);
        $stmt->execute([$username, $userInfo->email, $userInfo->id]);
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $user['id'];
    }
    
    // Start session
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $user['username'] ?? $username;
    $_SESSION['email'] = $userInfo->email;
    $_SESSION['google_user'] = true;
    
    header("Location: home.php");
    exit();
}

header("Location: login.php");
exit();
?>