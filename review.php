<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_login();

$rid = (int)($_POST['resource_id'] ?? 0);
$rating = max(1, min(5, (int)($_POST['rating'] ?? 0)));
$comment = trim($_POST['comment'] ?? '');

if ($rid && $comment && $rating) {
    $pdo->prepare("INSERT INTO reviews (user_id, resource_id, rating, comment) VALUES (?, ?, ?, ?)")
        ->execute([$_SESSION['user_id'], $rid, $rating, $comment]);
}

header("Location: /dkap/resource.php?id=$rid&review=1");
exit;
?>