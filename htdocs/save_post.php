<?php
// save_post.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$user = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    echo "Invalid CSRF token.";
    exit;
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$post_id = $_POST['post_id'] ?? null;

if ($title === '' || $content === '') {
    echo "Title and content are required.";
    exit;
}

if ($post_id) {
    // Update existing post
    $stmt = $pdo->prepare("SELECT user_id FROM blogPost WHERE id = ?");
    $stmt->execute([$post_id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        echo "Post not found.";
        exit;
    }

    if ($existing['user_id'] != $user['id']) {
        echo "Not authorized to edit this post.";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$title, $content, $post_id]);
    header("Location: view.php?id=" . urlencode($post_id));
    exit;
} else {
    // Create new post
    $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $title, $content]);
    $newId = $pdo->lastInsertId();
    header("Location: view.php?id=" . urlencode($newId));
    exit;
}
