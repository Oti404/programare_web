<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $pdo = getPDOConnection();

    $stmt = $pdo->prepare("SELECT u.id, u.username, u.password, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role_name'] = $user['role_name'];

        $log = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Login')");
        $log->execute([$user['id']]);

        header("Location: index.php");
        exit;
    } else {
        $error = "Username sau parola incorecte!";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login - Lab 8 (Vulnerabil)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container glass-panel">
        <h1>Autentificare</h1>

        <?php if (!empty($error)) echo "<div class='message-box error'>$error</div>"; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Nume Utilizator</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Parola</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Autentificare</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="setup.php">Initializare / Setup DB</a>
        </div>
    </div>
</body>
</html>
