<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$followedId = (int)$_GET['id'];
if ($followedId == $_SESSION['user_id']) { setAlert('danger', 'You cannot follow yourself'); redirect('profile.php?id=' . $_SESSION['user_id']); }

$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$followedId]);
if (!$stmt->fetch()) { setAlert('danger', 'User not found'); redirect('dashboard.php'); }

$isFollowing = isFollowing($pdo, $_SESSION['user_id'], $followedId);
if ($isFollowing) {
    $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?")->execute([$_SESSION['user_id'], $followedId]);
    setAlert('info', 'Unfollowed');
} else {
    $pdo->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $followedId]);
    setAlert('success', 'Now following');
}
redirect('profile.php?id=' . $followedId);