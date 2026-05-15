<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM resources WHERE id = ?")->execute([$id]);
}
header("Location: /dkap/admin/index.php");
exit;
?>