<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
if (isLoggedIn()) redirect('dashboard.php');
if (!isset($_SESSION['reset_verified']) || !isset($_SESSION['reset_email'])) redirect('forgot-password.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];
    if (strlen($password) < 6) $error = "Password too short";
    elseif ($password !== $confirm) $error = "Passwords do not match";
    else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?")->execute([$hashed, $email]);
        $pdo->prepare("UPDATE password_resets SET used = 1 WHERE email = ?")->execute([$email]);
        unset($_SESSION['reset_verified'], $_SESSION['reset_email']);
        setAlert('success', 'Password reset successful! Please login.');
        redirect('login.php');
    }
}
?>
<!DOCTYPE html>
<html><head><title>Reset Password</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6">
    <div class="card"><div class="card-header bg-success"><h4>New Password</h4></div>
    <div class="card-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST"><div class="mb-3"><label>New Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
        <button type="submit" class="btn btn-primary w-100">Reset Password</button></form>
    </div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>