<?php
require_once __DIR__.'/includes/header.php';

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);
$type = $_GET['type'] ?? '';

$where = []; 
$params = [];
if ($q !== '') {
    $where[] = "(title LIKE ? OR author LIKE ? OR description LIKE ? OR tags LIKE ? OR body LIKE ?)";
    $like = "%$q%";
    $params = array_fill(0, 5, $like);
}
if ($cat) { 
    $where[] = "category_id = ?"; 
    $params[] = $cat; 
}
if ($type) { 
    $where[] = "type = ?"; 
    $params[] = $type; 
}

$sql = "SELECT r.*, c.name AS cat_name, c.icon 
        FROM resources r 
        LEFT JOIN categories c ON c.id = r.category_id";
if (count($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$pageTitle = $q ? "Search: " . e($q) : "Library";
?>

<h1>Library</h1>
<p class="muted">Search the entire knowledge base</p>

<form class="search-bar" method="get">
  <input name="q" value="<?= e($q) ?>" placeholder="Search anything..." />
  <select name="type">
    <option value="">All Types</option>
    <?php foreach (['book','article','video','course','podcast'] as $t): ?>
      <option value="<?= $t ?>" <?= $type===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn-primary" type="submit">Search</button>
</form>

<div class="chips" style="margin:25px 0">
  <a class="chip <?= !$cat?'active':'' ?>" href="/dkap/library.php">All</a>
  <?php foreach ($cats as $c): ?>
    <a class="chip <?= $cat==$c['id']?'active':'' ?>" href="/dkap/library.php?cat=<?= $c['id'] ?>">
      <?= e($c['icon']) ?> <?= e($c['name']) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="section">
  <p class="muted"><?= count($results) ?> result<?= count($results) != 1 ? 's' : '' ?></p>
  
  <?php if (empty($results)): ?>
    <div class="side-card"><b>No results found.</b></div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($results as $r): ?>
        <a class="card" href="/dkap/resource.php?id=<?= $r['id'] ?>">
          <div class="cover"><?= e($r['icon'] ?? '📚') ?></div>
          <div class="body">
            <span class="type"><?= e($r['type']) ?></span>
            <h3><?= e($r['title']) ?></h3>
            <div class="muted"><?= e($r['author']) ?></div>
            <p class="muted" style="font-size:0.85rem;margin-top:8px"><?= e(substr($r['description'] ?? '', 0, 90)) ?>...</p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__.'/includes/footer.php'; ?>