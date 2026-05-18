<?php
// includes/auth.php

// redirect() is now defined in functions.php (loaded via config/database.php)
// Do NOT redefine it here.

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) redirect('login.php');
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') redirect('dashboard.php');
}

function setAlert($type, $message) {
    $_SESSION['alert'] = ['type' => $type, 'message' => $message];
}

function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $a = $_SESSION['alert'];
        echo "<div class='alert alert-{$a['type']} alert-dismissible fade show' role='alert'>
                {$a['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Login attempt tracking
function checkLoginAttempts($pdo, $email) {
    $timeLimit = date('Y-m-d H:i:s', strtotime('-' . LOCKOUT_TIME_MINUTES . ' minutes'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = ? AND attempt_time > ?");
    $stmt->execute([$email, $timeLimit]);
    return $stmt->fetchColumn() < MAX_LOGIN_ATTEMPTS;
}

function recordFailedAttempt($pdo, $email) {
    $stmt = $pdo->prepare("INSERT INTO login_attempts (email) VALUES (?)");
    $stmt->execute([$email]);
}

function clearLoginAttempts($pdo, $email) {
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
}
?>