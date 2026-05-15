<?php
require_once __DIR__.'/../includes/header.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$resource = ['title'=>'','author'=>'','description'=>'','type'=>'article','category_id'=>'','cover_url'=>'','content_url'=>'','body'=>'','tags'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id=?");
    $stmt->execute([$id]);
    $resource = $stmt->fetch() ?: $resource;
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    if ($title) {
        if ($id) {
            $pdo->prepare("UPDATE resources SET title=?, author=?, description=?, type=?, category_id=?, cover_url=?, content_url=?, body=?, tags=? WHERE id=?")
                ->execute([$title, $_POST['author'], $_POST['description'], $_POST['type'], $_POST['category_id'], $_POST['cover_url'], $_POST['content_url'], $_POST['body'], $_POST['tags'], $id]);
        } else {
            $pdo->prepare("INSERT INTO resources (title,author,description,type,category_id,cover_url,content_url,body,tags) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$title, $_POST['author'], $_POST['description'], $_POST['type'], $_POST['category_id'], $_POST['cover_url'], $_POST['content_url'], $_POST['body'], $_POST['tags']]);
        }
        header("Location: /dkap/admin/index.php");
        exit;
    }
}
?>

<h1><?= $id ? 'Edit' : 'Add New' ?> Resource</h1>

<form method="post" style="max-width:800px">
  <div class="form-group"><label>Title</label><input name="title" value="<?= e($resource['title']) ?>" required></div>
  <div class="form-group"><label>Author</label><input name="author" value="<?= e($resource['author']) ?>"></div>
  <div class="form-group"><label>Description</label><textarea name="description"><?= e($resource['description']) ?></textarea></div>
  
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="form-group">
      <label>Type</label>
      <select name="type">
        <?php foreach(['book','article','video','course','podcast'] as $t): ?>
          <option value="<?= $t ?>" <?= $resource['type']==$t?'selected':'' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Category</label>
      <select name="category_id">
        <option value="">— Select —</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $resource['category_id']==$c['id']?'selected':'' ?>><?= e($c['icon'].' '.$c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group"><label>Content URL (optional)</label><input name="content_url" value="<?= e($resource['content_url']) ?>"></div>
  <div class="form-group"><label>Body / Notes</label><textarea name="body" style="min-height:180px"><?= e($resource['body']) ?></textarea></div>
  <div class="form-group"><label>Tags (comma separated)</label><input name="tags" value="<?= e($resource['tags']) ?>"></div>

  <button class="btn-primary">Save Resource</button>
</form>

<?php require_once __DIR__.'/../includes/footer.php'; ?>