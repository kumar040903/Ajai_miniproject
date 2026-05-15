<?php
require_once __DIR__.'/includes/header.php';

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$trending = $pdo->query("SELECT r.*, c.name AS cat_name, c.icon 
                         FROM resources r 
                         LEFT JOIN categories c ON c.id = r.category_id 
                         ORDER BY r.views DESC LIMIT 8")->fetchAll();
$fresh = $pdo->query("SELECT r.*, c.name AS cat_name, c.icon 
                      FROM resources r 
                      LEFT JOIN categories c ON c.id = r.category_id 
                      ORDER BY r.created_at DESC LIMIT 6")->fetchAll();
?>
<section class="hero">
  <div>
    <h1>Knowledge without <span>walls</span>.</h1>
    <p>DKAP is a beautiful digital library that makes every book, article, course and video instantly searchable.</p>
    
    <form class="search-bar" action="/dkap/library.php" method="get">
      <input name="q" placeholder="Search 'quantum computing', 'lean startup', 'habits'..." />
      <button class="btn-primary" type="submit">Search</button>
    </form>
  </div>
  <div class="hero-art">
    <div class="float f1">📖 1,240+ resources</div>
    <div class="float f2">⚡ Instant search</div>
    <div class="float f3">🔥 Daily streaks</div>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Browse by topic</h2><a href="/dkap/library.php" class="muted">View all →</a></div>
  <div class="chips">
    <?php foreach ($cats as $c): ?>
      <a class="chip" href="/dkap/library.php?cat=<?= $c['id'] ?>">
        <span><?= e($c['icon']) ?></span><?= e($c['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Trending now</h2></div>
  <div class="grid">
    <?php foreach ($trending as $r): ?>
      <a class="card" href="/dkap/resource.php?id=<?= $r['id'] ?>">
        <div class="cover"><?= e($r['icon'] ?? '📚') ?></div>
        <div class="body">
          <span class="type"><?= e($r['type']) ?></span>
          <h3><?= e($r['title']) ?></h3>
          <div class="muted"><?= e($r['author']) ?></div>
          <div class="meta"><span><?= e($r['cat_name'] ?? 'General') ?></span></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Just Added</h2></div>
  <div class="grid">
    <?php foreach ($fresh as $r): ?>
      <a class="card" href="/dkap/resource.php?id=<?= $r['id'] ?>">
        <div class="cover"><?= e($r['icon'] ?? '✨') ?></div>
        <div class="body">
          <span class="type"><?= e($r['type']) ?></span>
          <h3><?= e($r['title']) ?></h3>
          <div class="muted"><?= e($r['author']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__.'/includes/footer.php'; ?>