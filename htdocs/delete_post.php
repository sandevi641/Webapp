<?php


// include authentication, database connection, and helper functions
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// ensure the user is loggedin
$user = requireAuth();

// allow only post requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// get csrf token
$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    echo "Invalid CSRF token.";
    exit;
}

// tke post id to delete
$post_id = $_POST['post_id'] ?? null;
if (!$post_id) {
    echo "No post specified.";
    exit;
}

// fetch post details from database
$stmt = $pdo->prepare("SELECT user_id FROM blogPost WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found.";
    exit;
}

// ensure the logged-in user owns this post
if ($post['user_id'] != $user['id']) {
    echo "Not authorized to delete.";
    exit;
}

// delete post from database
$stmt = $pdo->prepare("DELETE FROM blogPost WHERE id = ?");
$stmt->execute([$post_id]);

// redirect to homepage
header('Location: index.php');
exit;
?>
