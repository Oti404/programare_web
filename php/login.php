<?php
require_once 'config.php';

// Verificare Remember Me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $conn = getMysqliConnection();
    if ($conn) {
        $stmt = $conn->prepare("SELECT u.id, u.username, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_name'] = $user['role_name'];
            
            // Log in SQLite (PDO)
            $pdo = getPDOConnection();
            $stmtPDO = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Login prin Remember Me')");
            $stmtPDO->execute([$user['id']]);

            header("Location: index.php");
            exit;
        }
    }
}

// Daca e deja autentificat, mergi la dashboard
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

    // Verificare CAPTCHA fata de valoarea salvata la afisarea formularului (GET)
    $captcha_ok = !empty($captcha) && strcasecmp(trim($captcha), $_SESSION['captcha_code'] ?? '') === 0;
    if (!$captcha_ok) {
        $error = "Cod CAPTCHA incorect!";
    } else {
        $conn = getMysqliConnection();
        if (!$conn) {
            $error = "Eroare la conectarea cu baza de date (Rulați setup.php mai întâi).";
        } else {
            $stmt = $conn->prepare("SELECT u.id, u.username, u.password_hash, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['role_name'] = $user['role_name'];

                    $pdo = getPDOConnection();
                    $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Login Standard')")->execute([$user['id']]);

                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (86400 * 30), "/");
                        $upd = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                        $upd->bind_param("si", $token, $user['id']);
                        $upd->execute();
                    }

                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Parolă incorectă!";
                }
            } else {
                $error = "Utilizatorul nu există!";
            }
            $conn->close();
        }
    }
}

// Generare CAPTCHA nou - DUPA procesarea POST, pentru afisarea formularului
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
                <label for="password">Parolă</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>CAPTCHA: Cât face <strong><?= $captcha_a ?> + <?= $captcha_b ?> = ?</strong></label>
                <input type="text" name="captcha" class="form-control" placeholder="Introduceți rezultatul" required autocomplete="off">
            </div>

            <div class="form-group">
                <label class="checkbox-group">
                    <input type="checkbox" name="remember"> Tine-mă minte
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Autentificare</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="setup.php">Inițializare / Setup DB</a>
        </div>
    </div>
</body>
</html>
