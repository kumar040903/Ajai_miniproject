<?php
require_once __DIR__.'/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    die("Invalid Resource ID");
}

$stmt = $pdo->prepare("SELECT r.*, c.name AS cat_name, c.icon 
                       FROM resources r 
                       LEFT JOIN categories c ON c.id = r.category_id 
                       WHERE r.id = ?");
$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) {
    http_response_code(404);
    die("Resource not found.");
}

// Increment views
$pdo->prepare("UPDATE resources SET views = views + 1 WHERE id = ?")->execute([$id]);

$user = current_user();
$progress = 0;
$saved = false;

if ($user) {
    // Update streak
    $today = date('Y-m-d');
    $pdo->prepare("UPDATE users SET last_active = ?, streak_days = IF(last_active = DATE_SUB(?, INTERVAL 1 DAY), streak_days + 1, 1) WHERE id = ?")
        ->execute([$today, $today, $user['id']]);

    // Get progress
    $p = $pdo->prepare("SELECT percent FROM reading_progress WHERE user_id=? AND resource_id=?");
    $p->execute([$user['id'], $id]);
    $progress = (int)($p->fetchColumn() ?? 0);

    // Check bookmark
    $b = $pdo->prepare("SELECT 1 FROM bookmarks WHERE user_id=? AND resource_id=?");
    $b->execute([$user['id'], $id]);
    $saved = (bool)$b->fetchColumn();
}

// Reviews
$rev = $pdo->prepare("SELECT rv.*, u.name FROM reviews rv JOIN users u ON u.id=rv.user_id WHERE rv.resource_id=? ORDER BY rv.created_at DESC");
$rev->execute([$id]);
$reviews = $rev->fetchAll();

$avg = $pdo->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS review_count FROM reviews WHERE resource_id=?");
$avg->execute([$id]);
$ag = $avg->fetch();

$pageTitle = $r['title'];
?>

<a href="/dkap/library.php" class="muted">← Back to Library</a>

<div class="detail">
  <div>
    <div class="cover-lg"><?= e($r['icon'] ?? '📚') ?></div>
    <span class="type"><?= e($r['type']) ?> · <?= e($r['cat_name'] ?? 'General') ?></span>
    <h1><?= e($r['title']) ?></h1>
    <div class="muted">by <?= e($r['author'] ?: 'Unknown') ?></div>

    <div class="stars" style="margin:12px 0">
      <?php 
      $avgRating = round($ag['avg_rating'] ?? 0);
      for($i=1; $i<=5; $i++) echo $i <= $avgRating ? '★' : '☆';
      ?>
      <span class="muted"> (<?= number_format($ag['avg_rating'] ?? 0, 1) ?> · <?= $ag['review_count'] ?> reviews)</span>
    </div>

    <p><?= nl2br(e($r['description'])) ?></p>

    <h3>Content</h3>
    <div style="background:var(--surface); padding:24px; border-radius:14px; border:1px solid var(--border); line-height:1.7">
      <?= nl2br(e($r['body'])) ?>
      <?php if (!empty($r['content_url'])): ?>
        <p style="margin-top:20px">
          <a href="<?= e($r['content_url']) ?>" target="_blank" class="btn-primary">Open External Resource ↗</a>
        </p>
      <?php endif; ?>
    </div>

    <!-- Reviews Section -->
    <h3 style="margin-top:40px">Reviews</h3>
    <?php if ($user): ?>
      <form method="post" action="/dkap/api/review.php" style="background:var(--surface);padding:20px;border-radius:14px;border:1px solid var(--border);margin-bottom:20px">
        <input type="hidden" name="resource_id" value="<?= $id ?>">
        <div class="form-group">
          <label>Your Rating</label>
          <select name="rating" required>
            <?php for($i=5; $i>=1; $i--): ?>
              <option value="<?= $i ?>"><?= $i ?> ★</option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Comment</label>
          <textarea name="comment" required placeholder="What did you think?"></textarea>
        </div>
        <button class="btn-primary">Post Review</button>
      </form>
    <?php else: ?>
      <p class="muted">Login to post a review.</p>
    <?php endif; ?>

    <?php foreach ($reviews as $rv): ?>
      <div class="review">
        <div style="display:flex; justify-content:space-between">
          <b><?= e($rv['name']) ?></b>
          <span class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=$rv['rating']?'★':'☆'; ?></span>
        </div>
        <div class="muted"><?= $rv['created_at'] ?></div>
        <p><?= nl2br(e($rv['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Sidebar -->
  <aside>
    <div class="side-card">
      <div class="muted">Your Progress</div>
      <div class="progress" style="margin:12px 0"><div style="width:<?= $progress ?>%"></div></div>
      
      <?php if ($user): ?>
        <input type="range" id="progress-slider" data-rid="<?= $id ?>" min="0" max="100" value="<?= $progress ?>" style="width:100%">
        <div class="muted" style="text-align:center" id="progress-out"><?= $progress ?>%</div>
      <?php else: ?>
        <a href="/dkap/login.php">Login to track progress</a>
      <?php endif; ?>

      <button class="btn-ghost" data-bookmark="<?= $id ?>" style="margin-top:12px; width:100%">
        <?= $saved ? '★ Saved' : '☆ Save for Later' ?>
      </button>
    </div>
  </aside>
</div>

<?php require_once __DIR__.'/includes/footer.php'; ?>