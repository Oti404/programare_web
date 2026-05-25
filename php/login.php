<?php
require_once 'config.php';

// Verificare Remember Me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT u.id, u.username, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role_name'] = $user['role_name'];
        $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Login prin Remember Me')")->execute([$user['id']]);
        header("Location: index.php");
        exit;
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha  = $_POST['captcha'] ?? '';
    $remember = isset($_POST['remember']);

    $captcha_ok = !empty($captcha) && strcasecmp(trim($captcha), $_SESSION['captcha_code'] ?? '') === 0;
    if (!$captcha_ok) {
        $error = "Cod CAPTCHA incorect!";
    } else {
        $pdo  = getPDOConnection();
        $stmt = $pdo->prepare("SELECT u.id, u.username, u.password_hash, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role_name'] = $user['role_name'];

            $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Login Standard')")->execute([$user['id']]);

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), "/");
                $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?")->execute([$token, $user['id']]);
            }

            header("Location: index.php");
            exit;
        } else {
            $error = $user ? "Parola incorecta!" : "Utilizatorul nu exista!";
        }
    }
}

$captcha_a = rand(1, 15);
$captcha_b = rand(1, 15);
$_SESSION['captcha_code'] = (string)($captcha_a + $captcha_b);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login - Lab 7 PHP</title>
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
            <div class="form-group">
                <label>CAPTCHA: Cat face <strong><?= $captcha_a ?> + <?= $captcha_b ?> = ?</strong></label>
                <input type="text" name="captcha" class="form-control" placeholder="Introduceti rezultatul" required autocomplete="off">
            </div>
            <div class="form-group">
                <label class="checkbox-group">
                    <input type="checkbox" name="remember"> Tine-ma minte
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Autentificare</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="setup.php">Initializare / Setup DB</a>
        </div>
    </div>
</body>
</html>
