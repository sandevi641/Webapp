<?php
// logout.php
require_once __DIR__ . '/includes/auth.php';

// log out user
logout_user();

// redirect to home page
header('Location: index.php');
exit;
