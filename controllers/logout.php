<?php
/**
 * Logout Controller
 * Handles user logout and session cleanup
 */

require_once '../includes/auth.php';

// Ensure user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: /views/auth/login.php');
    exit();
}

// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Destroy session and redirect
session_destroy();
header('Location: /views/auth/login.php?logout=1');
exit();
?>