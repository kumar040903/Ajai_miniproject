<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['ok' => false, 'redirect' => '/dkap/login.php']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$rid = (int)($data['resource_id'] ?? 0);
$uid = $_SESSION['user_id'];

if ($rid === 0) {
    echo json_encode(['ok' => false, 'error' => 'Invalid resource']);
    exit;
}

$check = $pdo->prepare("SELECT id FROM bookmarks WHERE user_id=? AND resource_id=?");
$check->execute([$uid, $rid]);

if ($check->fetch()) {
    $pdo->prepare("DELETE FROM bookmarks WHERE user_id=? AND resource_id=?")->execute([$uid, $rid]);
    echo json_encode(['ok' => true, 'saved' => false]);
} else {
    $pdo->prepare("INSERT INTO bookmarks (user_id, resource_id) VALUES (?,?)")->execute([$uid, $rid]);
    echo json_encode(['ok' => true, 'saved' => true]);
}
?>