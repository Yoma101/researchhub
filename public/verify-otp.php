<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
if (isLoggedIn()) redirect('dashboard.php');
if (!isset($_SESSION['reset_email'])) redirect('forgot-password.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = sanitize($_POST['otp']);
    $email = $_SESSION['reset_email'];
    
    // Compare OTP, not used, and not expired
    $stmt = $pdo->prepare("SELECT id FROM password_resets 
                           WHERE email = ? AND otp = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$email, $otp]);
    if ($stmt->fetch()) {
        // Mark this OTP as used (optional, but prevents reuse)
        $pdo->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND otp = ?")->execute([$email, $otp]);
        $_SESSION['reset_verified'] = true;
        redirect('reset-password.php');
    } else {
        $error = "Invalid or expired OTP. Please request a new one.";
    }
}
?>
<!DOCTYPE html>
<html><head><title>Verify OTP</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6">
    <div class="card"><div class="card-header bg-info"><h4>Verify OTP</h4></div>
    <div class="card-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST"><div class="mb-3"><label>6-digit OTP</label><input type="text" name="otp" class="form-control" maxlength="6" required></div>
        <button type="submit" class="btn btn-primary w-100">Verify</button></form>
    </div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>