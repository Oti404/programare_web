<?php
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    try {
        $pdo = getPDOConnection();

        // Roluri implicite
        $pdo->exec("INSERT OR IGNORE INTO roles (name) VALUES ('admin'), ('profesor'), ('student')");

        // ID-uri roluri
        $roles = [];
        foreach (['admin', 'profesor', 'student'] as $r) {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$r]);
            $roles[$r] = $stmt->fetchColumn();
        }

        $pwdHash = password_hash('password', PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, password_hash, full_name, bio, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin',    $pwdHash, 'Administrator',    'Bio pentru admin',                    $roles['admin']]);
        $stmt->execute(['profesor', $pwdHash, 'Profesor Popescu', 'Bio pentru profesor',                 $roles['profesor']]);
        $stmt->execute(['student',  $pwdHash, 'Student Ionescu',  'Sunt un student pasionat de Web.',    $roles['student']]);

        $message .= "<p class='success'>Baza de date SQLite initializata cu succes!</p>";
    } catch (Exception $e) {
        $message .= "<p class='error'>Eroare: " . $e->getMessage() . "</p>";
    }

    // Creare folder uploads
    if (!file_exists(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0777, true);
        $message .= "<p class='success'>Folderul 'uploads' a fost creat.</p>";
    } else {
        $message .= "<p class='success'>Folderul 'uploads' exista deja.</p>";
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
        <h1>Initializare Aplicatie</h1>
        <p>Apasa butonul de mai jos pentru a crea baza de date SQLite, tabelele si utilizatorii de test.</p>
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
            <button type="submit" name="setup" class="btn btn-primary">Ruleaza Setup-ul</button>
        </form>
        <br>
        <a href="login.php" class="btn">Mergi la Login</a>
    </div>
</body>
</html>
