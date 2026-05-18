<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (empty($user['password_hash'])) $error = "No password set (Google login). Use 'Forgot Password' to set one.";
    elseif (!password_verify($current, $user['password_hash'])) $error = "Current password incorrect";
    elseif (strlen($new) < 6) $error = "New password too short";
    elseif ($new !== $confirm) $error = "Passwords do not match";
    else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hashed, $userId]);
        setAlert('success', 'Password changed!');
        redirect('dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html><head><title>Change Password</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4"><div class="row justify-content-center"><div class="col-md-6">
    <div class="card"><div class="card-header">Change Password</div><div class="card-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
            <div class="mb-3"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
            <div class="mb-3"><label>Confirm New Password</label><input type="password" name="confirm_password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>