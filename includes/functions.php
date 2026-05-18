<?php
function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit();
}
// includes/functions.php

function timeAgo($timestamp) {
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return floor($diff/60) . ' minutes ago';
    if ($diff < 86400) return floor($diff/3600) . ' hours ago';
    return date('M j, Y', strtotime($timestamp));
}

function isFollowing($pdo, $follower_id, $followed_id) {
    $stmt = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->execute([$follower_id, $followed_id]);
    return $stmt->fetch() !== false;
}
?>