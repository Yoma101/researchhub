<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$papers = $pdo->query("SELECT p.*, u.full_name as author FROM papers p JOIN users u ON p.user_id = u.id ORDER BY upload_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>Papers</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4"><h2>All Research Papers</h2>
    <div class="row"><?php foreach($papers as $p): ?>
        <div class="col-md-6"><div class="card mb-3"><div class="card-body">
            <h5><a href="view-paper.php?id=<?=$p['id']?>"><?=htmlspecialchars($p['title'])?></a></h5>
            <p class="small">by <?=htmlspecialchars($p['author'])?> | <?=timeAgo($p['upload_date'])?></p>
            <p><?=htmlspecialchars(substr($p['abstract'],0,150))?>...</p>
        </div></div></div>
    <?php endforeach; ?></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>