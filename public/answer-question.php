<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$questionId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT q.*, u.full_name as author FROM questions q JOIN users u ON q.user_id = u.id WHERE q.id = ?");
$stmt->execute([$questionId]);
$question = $stmt->fetch();
if (!$question) { setAlert('danger', 'Question not found'); redirect('questions.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = sanitize($_POST['answer']);
    if (!empty($answer)) {
        $stmt = $pdo->prepare("INSERT INTO answers (question_id, user_id, answer) VALUES (?, ?, ?)");
        $stmt->execute([$questionId, $_SESSION['user_id'], $answer]);
        setAlert('success', 'Answer posted');
        redirect("answer-question.php?id=$questionId");
    }
}

$answers = $pdo->prepare("SELECT a.*, u.full_name as author FROM answers a JOIN users u ON a.user_id = u.id WHERE a.question_id = ? ORDER BY a.created_at ASC");
$answers->execute([$questionId]);
?>
<!DOCTYPE html>
<html><head><title><?=htmlspecialchars($question['title'])?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="card mb-4"><div class="card-body">
        <h2><?=htmlspecialchars($question['title'])?></h2>
        <p class="text-muted">asked by <?=htmlspecialchars($question['author'])?> | <?=timeAgo($question['created_at'])?></p>
        <p><?=nl2br(htmlspecialchars($question['body']))?></p>
    </div></div>

    <h4>Answers</h4>
    <?php while($a = $answers->fetch()): ?>
        <div class="card mb-2"><div class="card-body">
            <strong><?=htmlspecialchars($a['author'])?></strong> <small class="text-muted"><?=timeAgo($a['created_at'])?></small>
            <p><?=nl2br(htmlspecialchars($a['answer']))?></p>
        </div></div>
    <?php endwhile; ?>

    <form method="POST"><div class="mb-3"><label>Your Answer</label><textarea name="answer" class="form-control" rows="4" required></textarea></div>
    <button type="submit" class="btn btn-success">Post Answer</button></form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>