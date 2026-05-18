<?php
require_once __DIR__ . '/../config/database.php';

// Set your desired admin password here
$new_password = 'Admin@123';   // change if you want

// Generate a new bcrypt hash
$hash = password_hash($new_password, PASSWORD_DEFAULT);

// Update the admin user
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@example.com'");
$stmt->execute([$hash]);

// Verify the update
$check = $pdo->prepare("SELECT email, password_hash FROM users WHERE email = 'admin@example.com'");
$check->execute();
$user = $check->fetch();

echo "<h3>Admin password has been reset</h3>";
echo "Email: " . $user['email'] . "<br>";
echo "New Password: " . $new_password . "<br>";
echo "Hash stored: " . $user['password_hash'] . "<br>";
echo "<hr>";
echo "<a href='login.php'>Go to Login Page</a>";
?>