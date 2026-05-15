<?php
require_once __DIR__.'/includes/header.php';
require_login();

$u = current_user();
$stmt = $pdo->prepare("
    SELECT r.*, c.name AS cat_name, c.icon 
    FROM bookmarks b 
    JOIN resources r ON r.id = b.resource_id 
    LEFT JOIN categories c ON c.id = r.category_id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->execute([$u['id']]);
$items = $stmt->fetchAll();
?>

<h1>Saved Resources</h1>
<p class="muted"><?= count($items) ?> bookmarked item<?= count($items) !== 1 ? 's' : '' ?></p>

<?php if (empty($items)): ?>
  <div class="side-card" style="margin-top:30px">
    You haven't saved anything yet.<br>
    Click <strong>☆ Save</strong> on any resource.
  </div>
<?php else: ?>
  <div class="grid" style="margin-top:25px">
    <?php foreach ($items as $r): ?>
      <a class="card" href="/dkap/resource.php?id=<?= $r['id'] ?>">
        <div class="cover"><?= e($r['icon'] ?? '📚') ?></div>
        <div class="body">
          <span class="type"><?= e($r['type']) ?></span>
          <h3><?= e($r['title']) ?></h3>
          <div class="muted"><?= e($r['author']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__.'/includes/footer.php'; ?>