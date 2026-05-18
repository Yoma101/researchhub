<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$questions = $pdo->query("SELECT q.*, u.full_name as author, (SELECT COUNT(*) FROM answers WHERE question_id = q.id) as answer_count FROM questions q JOIN users u ON q.user_id = u.id ORDER BY q.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>Q&A</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container mt-4"><h2>Discussion Forum</h2><a href="ask-question.php" class="btn btn-primary mb-3">Ask a Question</a>
    <?php foreach($questions as $q): ?>
        <div class="card mb-2"><div class="card-body">
            <h5><a href="answer-question.php?id=<?=$q['id']?>"><?=htmlspecialchars($q['title'])?></a></h5>
            <p><?=htmlspecialchars(substr($q['body'],0,150))?>...</p>
            <p class="small text-muted">by <?=htmlspecialchars($q['author'])?> | <?=$q['answer_count']?> answers | <?=timeAgo($q['created_at'])?></p>
        </div></div>
    <?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>