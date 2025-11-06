<?php
// view.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$user = curUser();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.username FROM blogPost p JOIN user u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found.";
    exit;
}

$canEdit = $user && $user['id'] == $post['user_id'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo e($post['title']); ?> - My Blog</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container">
      <h1><a href="index.php">All Blog</a></h1>
      <nav>
        <?php if ($user): ?>
          <span><?php echo e($user['username']); ?></span>
          <a href="editor.php?post_id=<?php echo e($post['id']); ?>" class="btn-edit">Edit</a>
          <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="container">
    <article class="single-post">
      <h1><?php echo e($post['title']); ?></h1>
      <div class="meta">By <?php echo e($post['username']); ?> • <?php echo e(date('F j, Y', strtotime($post['created_at']))); ?></div>
      <div class="content">
        <?php echo markdown_to_html($post['content']); ?>
      </div>

      <?php if ($canEdit): ?>
        <div class="post-actions">
          <a href="editor.php?post_id=<?php echo e($post['id']); ?>" class="btn-edit">Edit</a>
          <form method="post" action="delete_post.php" onsubmit="return confirm('Delete this post?');" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="post_id" value="<?php echo e($post['id']); ?>">
            <button type="submit" class="btn-delete">Delete</button>
          </form>
        </div>
      <?php endif; ?>
    </article>
  </main>
</body>
</html>
