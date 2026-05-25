<?php
require_once 'config.php';
requireRole('admin'); // doar admin poate vedea toate logurile

$logs = [];
try {
    $pdo = getPDOConnection();
    $stmt = $pdo->query("SELECT l.id, l.user_id, l.action, l.log_time FROM logs l ORDER BY l.log_time DESC");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Loguri SQLite - Lab 7 PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-large glass-panel">
        <div class="header-nav">
            <h2>Audit Log — SQLite via PDO</h2>
            <div class="nav-links">
                <a href="index.php" class="btn btn-small">Înapoi la Dashboard</a>
            </div>
        </div>

        <p>Această pagină citește direct din baza de date <strong>SQLite</strong> folosind <strong>PDO</strong>.</p>
        <p>Fișier: <code>database.sqlite</code> &nbsp;|&nbsp; Tabel: <code>logs</code> &nbsp;|&nbsp; Total înregistrări: <strong><?= count($logs) ?></strong></p>

        <?php if (isset($error)): ?>
            <p class="error">Eroare: <?= htmlspecialchars($error) ?></p>
        <?php elseif (count($logs) === 0): ?>
            <p>Nicio înregistrare în baza de date SQLite. Fă login/logout pentru a genera loguri.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User ID</th>
                        <th>Acțiune</th>
                        <th>Data și Ora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['id']) ?></td>
                            <td><?= htmlspecialchars($log['user_id']) ?></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td><?= htmlspecialchars($log['log_time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
