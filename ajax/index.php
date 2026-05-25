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
                <a href="logout.php" class="btn btn-small btn-danger">Deconectare</a>
            </div>
        </div>

        <?php if ($_SESSION['role_name'] === 'admin'): ?>
        <div style="margin-bottom: 25px;">
            <h3>Cerinte AJAX</h3>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="ajax/c1.php" class="btn btn-small btn-primary">C1 — JSON Vanilla JS</a>
                <a href="ajax/c2.php" class="btn btn-small btn-primary">C2 — XML Vanilla JS</a>
                <a href="ajax/c3.php" class="btn btn-small btn-primary">C3 — jQuery AJAX</a>
                <a href="ajax/c4.php" class="btn btn-small btn-primary">C4 — Server-side</a>
                <a href="ajax/c5.php" class="btn btn-small btn-primary">C5 — Editor Vanilla JS</a>
                <a href="ajax/c6.php" class="btn btn-small btn-primary">C6 — Editor jQuery</a>
            </div>
        </div>
        <?php endif; ?>

        <div>
            <h3>Ultimele tale acțiuni</h3>
            <?php if (isset($logs_error)): ?>
                <p class="error"><?= $logs_error ?></p>
            <?php elseif (count($logs) > 0): ?>
                <table style="font-size:0.85em;">
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
                                <td><?= htmlspecialchars(substr($log['log_time'], 0, 16)) ?></td>
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
