<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (isLoggedIn()) redirect('dashboard.php');

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $affiliation = sanitize($_POST['affiliation']);

    if (empty($fullName) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif (!validateEmail($email)) {
        $error = "Invalid email";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, affiliation) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$fullName, $email, $hashed, $affiliation])) {
                $success = "Registration successful! <a href='login.php'>Login now</a>";
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6">
    <div class="card shadow"><div class="card-header bg-primary text-white"><h4>Register</h4></div>
    <div class="card-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST">
            <div class="mb-3"><label>Full Name</label><input type="text" name="full_name" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
            <div class="mb-3"><label>Affiliation</label><input type="text" name="affiliation" class="form-control"></div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <div class="text-center mt-3"><a href="login.php">Already have an account? Login</a></div>
    </div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>