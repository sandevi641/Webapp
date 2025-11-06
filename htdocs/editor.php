<?php
// editor.php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$user = requireAuth();

$post = null;
$isEdit = false;

if (!empty($_GET['post_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM blogPost WHERE id = ?");
    $stmt->execute([$_GET['post_id']]);
    $post = $stmt->fetch();

    if (!$post) {
        echo "Post not found.";
        exit;
    }

    if ($post['user_id'] != $user['id']) {
        echo "Not authorized.";
        exit;
    }

    $isEdit = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = $post['image'] ?? null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $filename;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg','jpeg','png','gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $filename;
            } else {
                echo "Error uploading image.";
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }

    // Insert or update post
    if ($isEdit) {
        $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $content, $imagePath, $post['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO blogPost (title, content, user_id, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $user['id'], $imagePath]);
    }

    header('Location: index.php');
    exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $isEdit ? 'Edit' : 'New'; ?> Post</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <h1><a href="index.php">My Blog</a></h1>
    <nav>
      <span><?php echo e($user['username']); ?></span>
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container">
  <h2><?php echo $isEdit ? 'Edit Post' : 'Create New Post'; ?></h2>

  <div class="editor-container">
    <form method="post" enctype="multipart/form-data">
      <label class="form-label">Title</label>
      <input class="input-field" name="title" value="<?php echo e($post['title'] ?? ''); ?>" required placeholder="Enter your post title">

      <label class="form-label">Content (Markdown)</label>
      <textarea id="content" name="content" rows="18" class="textarea-field" required placeholder="Write your post here..."><?php echo e($post['content'] ?? ''); ?></textarea>

      <label class="form-label">Upload Image</label>
      <input type="file" name="image" accept="image/*">
      <?php if (!empty($post['image'])): ?>
        <p>Current Image:</p>
        <img src="<?php echo e($post['image']); ?>" alt="Post Image" style="max-width:200px; border-radius:8px; margin-top:5px;">
      <?php endif; ?>

      <div class="editor-actions">
        <button type="submit" class="btn-primary"><?php echo $isEdit ? 'Update Post' : 'Publish Post'; ?></button>
        <a href="index.php" class="btn-secondary cancel-btn">Cancel</a>
      </div>
    </form>

    <h3 class="preview-title">Live Preview</h3>
    <div id="preview" class="preview-box">
      <?php if ($isEdit) echo markdown_to_html($post['content']); ?>
    </div>
  </div>

</main>

<script src="assets/app.js"></script>
</body>
</html>
