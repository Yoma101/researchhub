<?php
session_start();
date_default_timezone_set('Asia/Manila');

// config/database.php
$db_host = getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: 'localhost');
$db_name = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: 'researchhub');
$db_user = getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: 'root');
$db_pass = getenv('MYSQLPASSWORD') ?: (getenv('DB_PASS') ?: '');

define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/researchhub/public/');
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI', SITE_URL . 'google-callback.php');
define('GITHUB_TOKEN', getenv('GITHUB_TOKEN') ?: '');
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');
define('MAX_LOGIN_ATTEMPTS', getenv('MAX_LOGIN_ATTEMPTS') ?: 5);
define('LOCKOUT_TIME_MINUTES', getenv('LOCKOUT_TIME_MINUTES') ?: 15);
define('OTP_EXPIRY_MINUTES', getenv('OTP_EXPIRY_MINUTES') ?: 5);

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/functions.php';
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
?>
