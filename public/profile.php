<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$userId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT id, full_name, email, affiliation, profile_pic, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user) { setAlert('danger', 'User not found'); redirect('dashboard.php'); }

$papers = $pdo->prepare("SELECT * FROM papers WHERE user_id = ? ORDER BY upload_date DESC");
$papers->execute([$userId]);

$followers = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE followed_id = ?");
$followers->execute([$userId]);
$followerCount = $followers->fetchColumn();

$following = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$following->execute([$userId]);
$followingCount = $following->fetchColumn();

$isFollowing = isFollowing($pdo, $_SESSION['user_id'], $userId);
?>
<!DOCTYPE html>
<html><head><title><?=htmlspecialchars($user['full_name'])?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center"><div class="card-body">
                <img src="../assets/uploads/avatars/<?=$user['profile_pic']?>" class="rounded-circle mb-3" width="120" height="120">
                <h4><?=htmlspecialchars($user['full_name'])?></h4>
                <p><?=htmlspecialchars($user['affiliation'])?: 'No affiliation'?></p>
                <p>Member since <?=date('M Y', strtotime($user['created_at']))?></p>
                <div class="mb-3">Followers: <?=$followerCount?> | Following: <?=$followingCount?></div>
                <?php if($userId != $_SESSION['user_id']): ?>
                    <a href="follow.php?id=<?=$userId?>" class="btn btn-sm <?=$isFollowing ? 'btn-secondary' : 'btn-primary'?>">
                        <?=$isFollowing ? 'Unfollow' : 'Follow'?>
                    </a>
                <?php else: ?>
                    <a href="change-password.php" class="btn btn-sm btn-warning">Change Password</a>
                <?php endif; ?>
            </div></div>
        </div>
        <div class="col-md-8">
            <h4>Research Papers</h4>
            <?php while($p = $papers->fetch()): ?>
                <div class="card mb-2"><div class="card-body">
                    <h5><a href="view-paper.php?id=<?=$p['id']?>"><?=htmlspecialchars($p['title'])?></a></h5>
                    <p class="small">Uploaded <?=timeAgo($p['upload_date'])?> | <?=$p['downloads']?> downloads</p>
                </div></div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>