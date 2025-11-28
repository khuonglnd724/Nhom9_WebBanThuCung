<?php
// Admin authentication guard
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Determine admin status from session
$loggedIn = isset($_SESSION['user_id']);
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : null);
$isAdminFlag = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : null;

$hasAdminRole = ($role === 'ADMIN' || $role === 'admin');
$isAdminBool = ($isAdminFlag === 1 || $isAdminFlag === true || $isAdminFlag === '1');

if (!$loggedIn || (!$hasAdminRole && !$isAdminBool)) {
    // Not authorized: redirect to login
    header('Location: ../frontend/login.php');
    exit;
}
