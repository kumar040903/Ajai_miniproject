<?php
require_once __DIR__.'/includes/header.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $err = "Please check your inputs (password must be 6+ characters)";
    } else {
        try {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            
            $id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';
            
            header("Location: /dkap/dashboard.php");
            exit;
        } catch (Exception $e) {
            $err = "Email already registered";
        }
    }
}
?>

<div class="auth-wrap">
  <h2>Join DKAP</h2>
  <p class="muted">Free forever. Start learning today.</p>
  
  <?php if ($err): ?>
    <div class="alert error"><?= e($err) ?></div>
  <?php endif; ?>
  
  <form method="post">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="name" required>
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" minlength="6" required>
    </div>
    <button class="btn-primary" style="width:100%">Create Account</button>
  </form>
  
  <p class="muted" style="text-align:center; margin-top:20px">
    Already have an account? <a href="/dkap/login.php">Login</a>
  </p>
</div>

<?php require_once __DIR__.'/includes/footer.php'; ?>