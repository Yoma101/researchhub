<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$paperId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, u.full_name as author, u.id as author_id FROM papers p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$paperId]);
$paper = $stmt->fetch();
if (!$paper) { setAlert('danger', 'Paper not found'); redirect('papers.php'); }

// Increment downloads if download requested
if (isset($_GET['download'])) {
    $pdo->prepare("UPDATE papers SET downloads = downloads + 1 WHERE id = ?")->execute([$paperId]);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($paper['title']) . '.pdf"');
    readfile('../assets/uploads/papers/' . $paper['file_path']);
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = sanitize($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (paper_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$paperId, $_SESSION['user_id'], $comment]);
        setAlert('success', 'Comment added');
        redirect("view-paper.php?id=$paperId");
    }
}

$comments = $pdo->prepare("SELECT c.*, u.full_name as author FROM comments c JOIN users u ON c.user_id = u.id WHERE c.paper_id = ? ORDER BY c.created_at DESC");
$comments->execute([$paperId]);
?>
<!DOCTYPE html>
<html><head><title><?=htmlspecialchars($paper['title'])?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="card mb-4"><div class="card-body">
        <h2><?=htmlspecialchars($paper['title'])?></h2>
        <p class="text-muted">by <?=htmlspecialchars($paper['author'])?> | Uploaded <?=timeAgo($paper['upload_date'])?> | Downloads: <?=$paper['downloads']?></p>
        <h5>Abstract</h5><p><?=nl2br(htmlspecialchars($paper['abstract']))?></p>
        <a href="?id=<?=$paperId?>&download=1" class="btn btn-success">Download Paper</a>
    </div></div>

    <h4>Comments</h4>
    <?php while($c = $comments->fetch()): ?>
        <div class="card mb-2"><div class="card-body">
            <strong><?=htmlspecialchars($c['author'])?></strong> <small class="text-muted"><?=timeAgo($c['created_at'])?></small>
            <p><?=nl2br(htmlspecialchars($c['comment']))?></p>
        </div></div>
    <?php endwhile; ?>

    <form method="POST"><div class="mb-3"><label>Add a comment</label><textarea name="comment" class="form-control" rows="3" required></textarea></div>
    <button type="submit" class="btn btn-primary">Post Comment</button></form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>