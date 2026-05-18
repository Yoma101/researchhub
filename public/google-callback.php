<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

if (!isset($_GET['code'])) redirect('login.php');

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);
$oauth = new Google\Service\Oauth2($client);
$userInfo = $oauth->userinfo->get();

$email = $userInfo->email;
$googleId = $userInfo->id;
$fullName = $userInfo->name;

$stmt = $pdo->prepare("SELECT id, full_name, email, role, profile_pic, affiliation FROM users WHERE google_id = ? OR email = ?");
$stmt->execute([$googleId, $email]);
$user = $stmt->fetch();

if ($user) {
    if (empty($user['google_id'])) {
        $upd = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
        $upd->execute([$googleId, $user['id']]);
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['profile_pic'] = $user['profile_pic'];
    $_SESSION['affiliation'] = $user['affiliation'];
} else {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, google_id) VALUES (?, ?, ?)");
    $stmt->execute([$fullName, $email, $googleId]);
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_email'] = $email;
    $_SESSION['role'] = 'user';
    $_SESSION['profile_pic'] = 'default.png';
    $_SESSION['affiliation'] = null;
}
redirect('dashboard.php');