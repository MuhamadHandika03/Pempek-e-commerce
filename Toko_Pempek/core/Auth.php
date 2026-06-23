<?php
/**
 * Helper Authentication dan Security untuk Admin Panel
 */

function is_logged_in() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function is_owner() {
    return isset($_SESSION['admin_level']) && $_SESSION['admin_level'] === 'owner';
}

function get_admin_name() {
    return $_SESSION['admin_nama'] ?? 'Admin';
}

function get_admin_level() {
    return $_SESSION['admin_level'] ?? 'kasir';
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// CSRF Protection Functions
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function get_csrf_input() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generate_csrf_token()) . '">';
}

function csrf_validate_post() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(403);
            die('Error: Invalid CSRF Token.');
        }
    }
}
