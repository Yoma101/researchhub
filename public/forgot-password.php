<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isLoggedIn()) redirect('dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    if (!validateEmail($email)) {
        $error = "Invalid email";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            // Generate 6-digit OTP
            $otp = sprintf("%06d", mt_rand(1, 999999));
            // Expiration time (use PHP datetime to match server timezone)
            $expires = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));
            
            // Invalidate previous OTPs for this email
            $pdo->prepare("UPDATE password_resets SET used = 1 WHERE email = ?")->execute([$email]);
            
            // Insert new OTP
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, otp, expires_at) VALUES (?, ?, ?)");
            if (!$stmt->execute([$email, $otp, $expires])) {
                $error = "Failed to store OTP. Please try again.";
            } else {
                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = SMTP_ENCRYPTION;
                    $mail->Port = SMTP_PORT;
                    $mail->setFrom(SMTP_USERNAME, 'ResearchHub');
                    $mail->addAddress($email);
                    $mail->Subject = 'Password Reset OTP';
                    $mail->Body = "Your OTP for password reset is: $otp\n\nValid for " . OTP_EXPIRY_MINUTES . " minutes.";
                    $mail->send();
                    $_SESSION['reset_email'] = $email;
                    setAlert('success', 'OTP sent to your email');
                    redirect('verify-otp.php');
                } catch (Exception $e) {
                    $error = "Failed to send OTP: " . $mail->ErrorInfo;
                }
            }
        } else {
            $error = "Email not found";
        }
    }
}
?>
<!DOCTYPE html>
<html><head><title>Forgot Password</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6">
    <div class="card"><div class="card-header bg-warning"><h4>Reset Password</h4></div>
    <div class="card-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST"><div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <button type="submit" class="btn btn-primary w-100">Send OTP</button></form>
        <div class="text-center mt-3"><a href="login.php">Back to Login</a></div>
    </div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>