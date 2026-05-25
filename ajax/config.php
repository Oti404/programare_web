<?php
session_start();

$sqlite_file = __DIR__ . '/database.sqlite';

function getPDOConnection() {
    global $sqlite_file;
    try {
        $pdo = new PDO("sqlite:" . $sqlite_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("PRAGMA foreign_keys = ON");
        return $pdo;
    } catch (PDOException $e) {
        die("Eroare conectare SQLite: " . $e->getMessage());
    }
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($role_name) {
    requireLogin();
    if ($_SESSION['role_name'] !== $role_name && $_SESSION['role_name'] !== 'admin') {
        die("<html><head><link rel='stylesheet' href='style.css'></head><body><div class='container glass-panel'><h1 style='color:var(--danger-color);'>Acces Interzis</h1><p>Nu ai permisiunea de a vizualiza această pagină.</p><a href='index.php' class='btn'>Înapoi acasă</a></div></body></html>");
    }
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}
?>
