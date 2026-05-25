<?php
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    // 1. Creare MySQL Database si tabele
    $conn = getMysqliConnectionNoDB();
    if ($conn->connect_error) {
        $message .= "<p class='error'>Eroare conectare la MySQL: " . $conn->connect_error . "</p>";
    } else {
        // Pe serverul facultății NU avem voie să creăm baze de date. Folosim baza de date deja alocată.
        global $is_server, $mysql_db;
        
        if (!$is_server) {
            $conn->query("CREATE DATABASE IF NOT EXISTS $mysql_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        
        if (!$conn->select_db($mysql_db)) {
            $message .= "<p class='error'>Eroare: Nu s-a putut selecta baza de date '$mysql_db'. Asigură-te că există.</p>";
        }

        // Tabele
        $conn->query("CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE
        )");

        $conn->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            bio TEXT,
            city VARCHAR(100),
            profile_pic VARCHAR(255),
            remember_token VARCHAR(255),
            role_id INT,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
        )");

        // Adaugare coloana city daca nu exista deja (pentru baze de date existente)
        $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL");

        // Insert default roles
        $conn->query("INSERT IGNORE INTO roles (name) VALUES ('admin'), ('profesor'), ('student')");

        // Insert default users (password is 'password' for all)
        $roles = [
            'admin' => $conn->query("SELECT id FROM roles WHERE name='admin'")->fetch_assoc()['id'],
            'profesor' => $conn->query("SELECT id FROM roles WHERE name='profesor'")->fetch_assoc()['id'],
            'student' => $conn->query("SELECT id FROM roles WHERE name='student'")->fetch_assoc()['id']
        ];

        $pwdHash = password_hash('password', PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT IGNORE INTO users (username, password_hash, full_name, bio, role_id) VALUES (?, ?, ?, ?, ?)");
        
        $admin = 'admin'; $adminName = 'Administrator'; $adminBio = 'Bio pentru admin';
        $stmt->bind_param("ssssi", $admin, $pwdHash, $adminName, $adminBio, $roles['admin']);
        $stmt->execute();

        $prof = 'profesor'; $profName = 'Profesor Popescu'; $profBio = 'Bio pentru profesor';
        $stmt->bind_param("ssssi", $prof, $pwdHash, $profName, $profBio, $roles['profesor']);
        $stmt->execute();

        $stud = 'student'; $studName = 'Student Ionescu'; $studBio = 'Sunt un student pasionat de Web.';
        $stmt->bind_param("ssssi", $stud, $pwdHash, $studName, $studBio, $roles['student']);
        $stmt->execute();

        $message .= "<p class='success'>✅ Baza de date MySQL inițializată cu succes!</p>";
        $conn->close();
    }

    // 2. Creare SQLite Database si tabele
    try {
        $pdo = getPDOConnection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            action VARCHAR(255),
            log_time DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        $message .= "<p class='success'>✅ Baza de date SQLite inițializată cu succes!</p>";
    } catch (Exception $e) {
        $message .= "<p class='error'>Eroare inițializare SQLite: " . $e->getMessage() . "</p>";
    }

    // 3. Creare folder uploads
    if (!file_exists(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0777, true);
        $message .= "<p class='success'>✅ Folderul 'uploads' a fost creat.</p>";
    } else {
        $message .= "<p class='success'>ℹ️ Folderul 'uploads' există deja.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Setup - Lab 7 PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container glass-panel">
        <h1>Inițializare Aplicație</h1>
        <p>Apasă butonul de mai jos pentru a crea bazele de date, tabelele și utilizatorii de test.</p>
        <div style="background: rgba(0,0,0,0.1); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3>Conturi implicite (Parola: <code>password</code>)</h3>
            <ul>
                <li><strong>Admin:</strong> username: <code>admin</code></li>
                <li><strong>Profesor:</strong> username: <code>profesor</code></li>
                <li><strong>Student:</strong> username: <code>student</code></li>
            </ul>
        </div>
        <?php if (!empty($message)) echo "<div class='message-box'>$message</div>"; ?>
        
        <form method="POST">
            <button type="submit" name="setup" class="btn btn-primary">Rulează Setup-ul</button>
        </form>
        <br>
        <a href="login.php" class="btn">Mergi la Login</a>
    </div>
</body>
</html>
