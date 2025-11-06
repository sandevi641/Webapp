<?php
require_once __DIR__ . '/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) session_start();

// Get currently logged-in user
function curUser() {
    global $pdo;
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id, username, email, role FROM user WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    // Check "remember me" cookie
    if (!empty($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $stmt = $pdo->prepare("SELECT id, username, email, role FROM user WHERE remember_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            return $user;
        }
    }

    return null;
}

// Require login before accessing a page
function requireAuth() {
    $u = curUser();
    if (!$u) {
        $next = urlencode($_SERVER['REQUEST_URI']);
        // ✅ Updated path for your live domain
        header("Location: /login.php?next=$next");
        exit;
    }
    return $u;
}

// Log in user and optionally remember them
function login_user($user_id, $remember = false) {
    global $pdo;
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user_id;

    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE user SET remember_token = ? WHERE id = ?");
        $stmt->execute([$token, $user_id]);

        // ✅ Fixed cookie path for your domain
        setcookie(
            'remember_token',
            $token,
            time() + 60 * 60 * 24 * 30,
            '/', // available site-wide
            '',  // domain auto
            false,
            true
        );
    }
}

// Log out user
function logout_user() {
    global $pdo;
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE user SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }

    // Clear session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Clear cookie
    setcookie('remember_token', '', time() - 3600, '/');
}
