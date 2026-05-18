<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Handle deletions
if (isset($_GET['delete_user'])) {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$_GET['delete_user']]);
    setAlert('success', 'User deleted');
    redirect('admin.php');
}
if (isset($_GET['delete_paper'])) {
    $pdo->prepare("DELETE FROM papers WHERE id = ?")->execute([$_GET['delete_paper']]);
    setAlert('success', 'Paper deleted');
    redirect('admin.php');
}
if (isset($_GET['delete_comment'])) {
    $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$_GET['delete_comment']]);
    setAlert('success', 'Comment deleted');
    redirect('admin.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$papers = $pdo->query("SELECT p.*, u.full_name as author FROM papers p JOIN users u ON p.user_id = u.id ORDER BY upload_date DESC")->fetchAll();
$comments = $pdo->query("SELECT c.*, u.full_name as author, p.title as paper_title FROM comments c JOIN users u ON c.user_id = u.id JOIN papers p ON c.paper_id = p.id ORDER BY c.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>Admin Panel</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <div class="row"><div class="col-md-4">
        <div class="card"><div class="card-header">Users</div><ul class="list-group list-group-flush">
        <?php foreach($users as $u): ?>
            <li class="list-group-item d-flex justify-content-between"><?=htmlspecialchars($u['full_name'])?> (<?=$u['email']?>)
                <a href="?delete_user=<?=$u['id']?>" onclick="return confirm('Delete user?')" class="btn btn-sm btn-danger">Delete</a>
            </li>
        <?php endforeach; ?></ul></div>
    </div><div class="col-md-4">
        <div class="card"><div class="card-header">Papers</div><ul class="list-group list-group-flush">
        <?php foreach($papers as $p): ?>
            <li class="list-group-item d-flex justify-content-between"><?=htmlspecialchars($p['title'])?> by <?=$p['author']?>
                <a href="?delete_paper=<?=$p['id']?>" onclick="return confirm('Delete paper?')" class="btn btn-sm btn-danger">Delete</a>
            </li>
        <?php endforeach; ?></ul></div>
    </div><div class="col-md-4">
        <div class="card"><div class="card-header">Comments</div><ul class="list-group list-group-flush">
        <?php foreach($comments as $c): ?>
            <li class="list-group-item d-flex justify-content-between">On "<?=htmlspecialchars($c['paper_title'])?>" by <?=$c['author']?>
                <a href="?delete_comment=<?=$c['id']?>" onclick="return confirm('Delete comment?')" class="btn btn-sm btn-danger">Delete</a>
            </li>
        <?php endforeach; ?></ul></div>
    </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>