<?php
require_once __DIR__.'/includes/header.php';
require_login();

$u = current_user();

// Stats
$stats = $pdo->prepare("
    SELECT 
        (SELECT streak_days FROM users WHERE id = ?) as streak,
        (SELECT COUNT(*) FROM bookmarks WHERE user_id = ?) as bookmarks,
        (SELECT COUNT(*) FROM reviews WHERE user_id = ?) as reviews,
        (SELECT COUNT(*) FROM reading_progress WHERE user_id = ? AND percent = 100) as completed
");
$stats->execute([$u['id'], $u['id'], $u['id'], $u['id']]);
$st = $stats->fetch();

$inProgress = $pdo->prepare("
    SELECT r.*, rp.percent, c.icon 
    FROM reading_progress rp 
    JOIN resources r ON r.id = rp.resource_id 
    LEFT JOIN categories c ON c.id = r.category_id 
    WHERE rp.user_id = ? AND rp.percent < 100 
    ORDER BY rp.updated_at DESC LIMIT 6
");
$inProgress->execute([$u['id']]);
$ipRows = $inProgress->fetchAll();
?>

<h1>Welcome back, <?= e($u['name']) ?> 👋</h1>
<p class="muted">Keep the momentum going!</p>

<div class="stats" style="margin:25px 0">
  <div class="stat"><div class="n"><?= (int)$st['streak'] ?> 🔥</div><div class="l">Day Streak</div></div>
  <div class="stat"><div class="n"><?= (int)$st['bookmarks'] ?></div><div class="l">Saved</div></div>
  <div class="stat"><div class="n"><?= (int)$st['completed'] ?></div><div class="l">Completed</div></div>
  <div class="stat"><div class="n"><?= (int)$st['reviews'] ?></div><div class="l">Reviews</div></div>
</div>

<section class="section">
  <h2>Continue Reading</h2>
  <?php if (empty($ipRows)): ?>
    <p class="muted">No items in progress. <a href="/dkap/library.php">Browse Library →</a></p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($ipRows as $r): ?>
        <a class="card" href="/dkap/resource.php?id=<?= $r['id'] ?>">
          <div class="cover"><?= e($r['icon'] ?? '📚') ?></div>
          <div class="body">
            <h3><?= e($r['title']) ?></h3>
            <div class="progress" style="margin:10px 0"><div style="width:<?= $r['percent'] ?>%"></div></div>
            <div class="muted"><?= $r['percent'] ?>% complete</div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__.'/includes/footer.php'; ?>