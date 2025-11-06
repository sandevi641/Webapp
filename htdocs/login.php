<?php
// login.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);
    $token = $_POST['csrf_token'] ?? '';
    $next = $_POST['next'] ?? 'index.php';

    if (!validate_csrf($token)) $errors[] = "Invalid CSRF token.";
    if ($emailOrUsername === '' || $password === '') $errors[] = "Provide credentials.";

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id, password FROM user WHERE email = ? OR username = ?");
        $stmt->execute([$emailOrUsername, $emailOrUsername]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            login_user($user['id'], $remember);
            header("Location: " . $next);
            exit;
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
} else {
    $next = $_GET['next'] ?? 'index.php';
}

$registered = !empty($_GET['registered']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container auth-card">
    <h2>Login</h2>

    <?php if ($registered): ?>
      <div class="success">Account created. Please login.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="errors">
        <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="next" value="<?php echo e($next); ?>">

      <label>Email or Username<br>
        <input name="email" value="<?php echo e($_POST['email'] ?? '') ?>">
      </label>

      <label>Password<br>
        <input type="password" name="password">
      </label>

      <label><input type="checkbox" name="remember"> Remember me</label>

      <button type="submit">Login</button>
    </form>

    <p>No account? <a href="register.php">Register</a></p>
  </div>
</body>
</html>
