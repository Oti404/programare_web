<?php
session_start();

$sqlite_file = dirname($_SERVER['DOCUMENT_ROOT']) . '/pw_database.sqlite';

function getPDOConnection() {
    global $sqlite_file;
    try {
        $pdo = new PDO("sqlite:" . $sqlite_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("PRAGMA foreign_keys = ON");
        $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE
        )");
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            bio TEXT,
            city VARCHAR(100),
            profile_pic VARCHAR(255),
            remember_token VARCHAR(255),
            role_id INTEGER,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
        )");
        $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            action VARCHAR(255),
            log_time DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
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
?>
