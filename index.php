<?php
require_once __DIR__.'/../includes/header.php';
require_admin();

$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'resources' => $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn(),
    'reviews' => $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
];

$resources = $pdo->query("SELECT r.*, c.name AS cat_name FROM resources r LEFT JOIN categories c ON c.id=r.category_id ORDER BY r.created_at DESC")->fetchAll();
?>

<h1>Admin Panel</h1>
<p class="muted">Manage Knowledge Base</p>

<div class="stats">
  <div class="stat"><div class="n"><?= $stats['users'] ?></div><div class="l">Users</div></div>
  <div class="stat"><div class="n"><?= $stats['resources'] ?></div><div class="l">Resources</div></div>
  <div class="stat"><div class="n"><?= $stats['reviews'] ?></div><div class="l">Reviews</div></div>
</div>

<div class="section">
  <div class="section-head">
    <h2>Resources</h2>
    <a class="btn-primary" href="/dkap/admin/resource_form.php">+ Add New</a>
  </div>
  <table class="tbl">
    <tr><th>Title</th><th>Type</th><th>Category</th><th>Views</th><th>Actions</th></tr>
    <?php foreach ($resources as $r): ?>
    <tr>
      <td><?= e($r['title']) ?></td>
      <td><?= e($r['type']) ?></td>
      <td><?= e($r['cat_name'] ?? '-') ?></td>
      <td><?= $r['views'] ?></td>
      <td>
        <a href="/dkap/admin/resource_form.php?id=<?= $r['id'] ?>">Edit</a> |
        <a href="/dkap/admin/delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete this resource?')" style="color:var(--danger)">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__.'/../includes/footer.php'; ?>