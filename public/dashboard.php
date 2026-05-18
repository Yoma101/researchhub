<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$papers = $pdo->query("SELECT p.*, u.full_name as author FROM papers p JOIN users u ON p.user_id = u.id ORDER BY upload_date DESC LIMIT 10")->fetchAll();
$questions = $pdo->query("SELECT q.*, u.full_name as author FROM questions q JOIN users u ON q.user_id = u.id ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Dashboard</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Recent Research Papers</h2>
            <?php foreach($papers as $paper): ?>
                <div class="card mb-3"><div class="card-body">
                    <h5><a href="view-paper.php?id=<?=$paper['id']?>"><?=htmlspecialchars($paper['title'])?></a></h5>
                    <p class="text-muted">by <?=htmlspecialchars($paper['author'])?> | Downloads: <?=$paper['downloads']?></p>
                    <p><?=htmlspecialchars(substr($paper['abstract'], 0, 200))?>...</p>
                </div></div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-4">
            <h2>Recent Questions</h2>
            <?php foreach($questions as $q): ?>
                <div class="card mb-2"><div class="card-body">
                    <a href="answer-question.php?id=<?=$q['id']?>"><strong><?=htmlspecialchars($q['title'])?></strong></a>
                    <p class="small text-muted">by <?=htmlspecialchars($q['author'])?> | <?=timeAgo($q['created_at'])?></p>
                </div></div>
            <?php endforeach; ?>
            <a href="questions.php" class="btn btn-outline-primary btn-sm">View all Q&A</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>