<?php
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    try {
        $pdo = getPDOConnection();

        $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
            id   INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            username       VARCHAR(50)  NOT NULL UNIQUE,
            password       VARCHAR(255) NOT NULL,
            full_name      VARCHAR(100),
            bio            TEXT,
            profile_pic    VARCHAR(255),
            remember_token VARCHAR(255),
            role_id        INTEGER,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
            id       INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id  INTEGER,
            action   VARCHAR(255),
            log_time DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("INSERT OR IGNORE INTO roles (name) VALUES ('admin'), ('profesor'), ('student')");

        $roles = [];
        foreach (['admin', 'profesor', 'student'] as $r) {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$r]);
            $roles[$r] = $stmt->fetchColumn();
        }

        $pwd = password_hash('password', PASSWORD_DEFAULT);

        $insert = $pdo->prepare("INSERT OR IGNORE INTO users (username, password, full_name, bio, role_id) VALUES (?, ?, ?, ?, ?)");
        $insert->execute(['admin',    $pwd, 'Administrator',    'Bio pentru admin',                 $roles['admin']]);
        $insert->execute(['profesor', $pwd, 'Profesor Popescu', 'Bio pentru profesor',              $roles['profesor']]);
        $insert->execute(['student',  $pwd, 'Student Ionescu',  'Sunt un student pasionat de Web.', $roles['student']]);

        $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $update->execute([$pwd, 'admin']);
        $update->execute([$pwd, 'profesor']);
        $update->execute([$pwd, 'student']);

        $prenume = ['Alexandru', 'Andrei', 'Mihai', 'Cristian', 'Ionut', 'Gabriel', 'Razvan',
                    'Bogdan', 'Vlad', 'Stefan', 'Maria', 'Ana', 'Elena', 'Ioana', 'Andreea',
                    'Cristina', 'Laura', 'Diana', 'Alina', 'Roxana', 'Marius', 'Florin',
                    'Catalin', 'Lucian', 'Robert', 'Claudia', 'Monica', 'Simona', 'Raluca',
                    'Denisa'];
        $nume    = ['Popescu', 'Ionescu', 'Constantin', 'Gheorghe', 'Dumitru', 'Stan', 'Stoica',
                    'Mihai', 'Popa', 'Radu', 'Marinescu', 'Dinu', 'Serban', 'Tudor', 'Apostol',
                    'Barbu', 'Nistor', 'Neagu', 'Oprea', 'Niculescu'];

        $insert_student = $pdo->prepare("INSERT OR IGNORE INTO users (username, password, full_name, bio, role_id) VALUES (?, ?, ?, ?, ?)");
        $added = 0;
        for ($i = 1; $i <= 50; $i++) {
            $p         = $prenume[array_rand($prenume)];
            $n         = $nume[array_rand($nume)];
            $username  = 'student' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $full_name = $p . ' ' . $n;
            $bio       = "Student nr. $i — pasionat de programare web.";
            $insert_student->execute([$username, $pwd, $full_name, $bio, $roles['student']]);
            $added++;
        }

        $message .= "<p class='success'>&#10003; Baza de date initializata cu succes!</p>";
        $message .= "<p class='success'>&#10003; $added studenti adaugati (student01 – student50, parola: <code>password</code>).</p>";
    } catch (Exception $e) {
        $message .= "<p class='error'>Eroare: " . $e->getMessage() . "</p>";
    }

    if (!file_exists(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0755, true);
        $message .= "<p class='success'>&#10003; Folderul 'uploads' a fost creat.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Setup - Lab 8 (Vulnerabil)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container glass-panel">
        <h1>Initializare Aplicatie</h1>
        <p>Parolele sunt stocate cu <strong>password_hash</strong> (bcrypt) si verificate cu <strong>password_verify</strong>.</p>
        <div style="background: rgba(0,0,0,0.1); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3>Conturi implicite (Parola: <code>password</code>)</h3>
            <ul>
                <li><strong>Admin:</strong> <code>admin</code></li>
                <li><strong>Profesor:</strong> <code>profesor</code></li>
                <li><strong>Student:</strong> <code>student</code></li>
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
