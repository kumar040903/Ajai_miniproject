<?php
require_once __DIR__.'/includes/header.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $verify = password_verify($pass, $user['password']);
        if ($verify) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];
            header("Location: /dkap/dashboard.php");
            exit;
        } else {
            $err = "Password verify failed.<br>Stored hash: " . substr($user['password'], 0, 20) . "...";
        }
    } else {
        $err = "Email not found";
    }
}
?>

<div class="auth-wrap">
  <h2>Welcome Back</h2>
  
  <?php if ($err): ?>
    <div class="alert error"><?= $err ?></div>
  <?php endif; ?>
  
  <form method="post">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" value="admin@dkap.local" required>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" value="admin123" required>
    </div>
    <button class="btn-primary" style="width:100%">Login</button>
  </form>
</div>

<?php require_once __DIR__.'/includes/footer.php'; ?>