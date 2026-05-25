<?php
require_once 'config.php';
requireLogin();

// Preluare istoric actiuni din SQLite (PDO)
$logs = [];
try {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT l.action, l.log_time FROM logs l WHERE l.user_id = ? ORDER BY l.log_time DESC LIMIT 10");
    $stmt->execute([$_SESSION['user_id']]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $logs_error = "Eroare preluare loguri: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Lab 7 PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-large glass-panel">
        <div class="header-nav">
            <h2>Bun venit, <?= htmlspecialchars($_SESSION['username']) ?>! (Rol: <?= htmlspecialchars($_SESSION['role_name']) ?>)</h2>
            <div class="nav-links">
                <a href="profile.php" class="btn btn-small">Profilul meu</a>
                <?php if ($_SESSION['role_name'] === 'profesor' || $_SESSION['role_name'] === 'admin'): ?>
                    <a href="profesor.php" class="btn btn-small btn-primary">Pagină Profesor</a>
                <?php endif; ?>
                <?php if ($_SESSION['role_name'] === 'admin'): ?>
                    <a href="logs.php" class="btn btn-small">Loguri SQLite</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-small btn-danger">Deconectare</a>
            </div>
        </div>

        <div>
            <h3>Ultimele tale acțiuni (din SQLite via PDO)</h3>
            <?php if (isset($logs_error)): ?>
                <p class="error"><?= $logs_error ?></p>
            <?php elseif (count($logs) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Acțiune</th>
                            <th>Data și Ora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['log_time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nicio acțiune înregistrată încă.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
