<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getPDOConnection();
        $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Logout')")->execute([$_SESSION['user_id']]);
    } catch (Exception $e) {}
}

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    try {
        $pdo = getPDOConnection();
        $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?")->execute([$token]);
    } catch (Exception $e) {}
    setcookie('remember_token', '', time() - 3600, "/");
}

header("Location: login.php");
exit;
?>
