<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $body = sanitize($_POST['body']);
    if (empty($title) || empty($body)) $error = "Both fields required";
    else {
        $stmt = $pdo->prepare("INSERT INTO questions (user_id, title, body) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $body]);
        setAlert('success', 'Question posted');
        redirect('questions.php');
    }
}
?>
<!DOCTYPE html>
<html><head><title>Ask Question</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4"><div class="row justify-content-center"><div class="col-md-8">
    <div class="card"><div class="card-header">Ask a Research Question</div>
    <div class="card-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST"><div class="mb-3"><label>Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="mb-3"><label>Details</label><textarea name="body" rows="5" class="form-control" required></textarea></div>
        <button type="submit" class="btn btn-primary">Post Question</button></form>
    </div></div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>