<?php
// register.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // basic validation
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($token)) $errors[] = "Invalid CSRF token.";
    if ($username === '') $errors[] = "Username required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $password2) $errors[] = "Passwords do not match.";

    if (!$errors) {
        // check for duplicate username/email
        $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        } else {
            // insert new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container auth-card">
    <h2>Register</h2>

    <?php if ($errors): ?>
      <div class="errors">
        <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

      <label>Username<br>
        <input name="username" value="<?php echo e($_POST['username'] ?? '') ?>">
      </label>

      <label>Email<br>
        <input name="email" value="<?php echo e($_POST['email'] ?? '') ?>">
      </label>

      <label>Password<br>
        <input type="password" name="password">
      </label>

      <label>Confirm Password<br>
        <input type="password" name="password2">
      </label>

      <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
