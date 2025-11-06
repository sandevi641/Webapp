<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// get current logged-in user 
$user = curUser();

// fetch all posts from the database with author username, latest posts first
$stmt = $pdo->query("SELECT p.id, p.title, p.content, p.created_at, p.user_id, u.username
                     FROM blogPost p
                     JOIN user u ON p.user_id = u.id
                     ORDER BY p.created_at DESC");
$posts = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Blog - Home</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

  <header class="site-header">
    <div class="container">
      <h1><a href="index.php">All Blog</a></h1>
      <nav>
        <?php if ($user): ?>
          <span>Hello, <?php echo e($user['username']); ?></span>
          <a href="editor.php">New Post</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="container">
    <?php if (!$posts): ?>
      <!-- if no posts exist, show message -->
      <p>No posts yet. <?php if ($user) echo '<a href="editor.php">Be the first to write one</a>'; ?></p>
    <?php else: foreach ($posts as $p): ?>
      <!-- display each post as a snippet -->
      <article class="post-snippet">
        <!-- post title linking to full view -->
        <h2><a href="view.php?id=<?php echo e($p['id']); ?>"><?php echo e($p['title']); ?></a></h2>

        <div class="meta">
          By <?php echo e($p['username']); ?> • <?php echo e(date('F j, Y', strtotime($p['created_at']))); ?>
        </div>

        <div class="excerpt">
          <?php
            $snippet = substr(strip_tags($p['content']), 0, 300);
            echo e($snippet) . (strlen($p['content']) > 300 ? '...' : '');
          ?>
        </div>

        <!-- Read more link -->
        <a class="readmore" href="view.php?id=<?php echo e($p['id']); ?>">Read</a>
      </article>
    <?php endforeach; endif; ?>
  </main>

  <footer class="site-footer">
    <div class="container"> BlogNet </div>
  </footer>

</body>
</html>
