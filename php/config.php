<?php
session_start();

// Credentiale implicite (local XAMPP)
$mysql_host = '127.0.0.1';
$mysql_user = 'root';
$mysql_pass = '';
$mysql_db   = 'pw_lab7';
$mysql_port = 3306;

// Daca exista config.local.php (pe server, gitignored), il folosim
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Date pentru baza de date SQLite (utilizată cu PDO)
$sqlite_file = __DIR__ . '/database.sqlite';

/**
 * Conectare MySQL (mysqli)
 */
function getMysqliConnection() {
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port;
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port);
    if ($conn->connect_error) {
        return null;
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Conectare la MySQL (fara sa selectam DB, folosit pt creare DB in setup.php)
 */
function getMysqliConnectionNoDB() {
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_port;
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($mysql_host, $mysql_user, $mysql_pass, '', $mysql_port);
    return $conn;
}

/**
 * Conectare SQLite (PDO)
 */
function getPDOConnection() {
    global $sqlite_file;
    try {
        $pdo = new PDO("sqlite:" . $sqlite_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
