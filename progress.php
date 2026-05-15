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
$percent = max(0, min(100, (int)($data['percent'] ?? 0)));
$uid = $_SESSION['user_id'];

if ($rid === 0) {
    echo json_encode(['ok' => false, 'error' => 'Invalid resource']);
    exit;
}

$pdo->prepare("INSERT INTO reading_progress (user_id, resource_id, percent) 
               VALUES (?, ?, ?) 
               ON DUPLICATE KEY UPDATE percent = VALUES(percent)")
     ->execute([$uid, $rid, $percent]);

echo json_encode(['ok' => true]);
?>