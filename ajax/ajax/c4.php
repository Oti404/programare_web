<?php
require_once '../config.php';
requireLogin();

$limit  = 5;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$pdo   = getPDOConnection();
$total = (int)$pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student'")->fetchColumn();
$pages = (int)ceil($total / $limit);
$page  = min($page, $pages);

$stmt = $pdo->prepare("SELECT username, full_name, bio FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student' ORDER BY username LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Cerinta 4 - Server-side</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container container-large glass-panel">
    <div class="header-nav">
        <h2>Cerinta 4 — Paginare Server-side</h2>
        <a href="../index.php" class="btn btn-small">Inapoi</a>
    </div>

    <table>
        <thead>
            <tr><th>Username</th><th>Nume Complet</th><th>Bio</th></tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['username'])  ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['bio'])       ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:flex; gap:10px; margin-top:15px; align-items:center;">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn">&#8592; Previous 5</a>
        <?php else: ?>
            <button class="btn" disabled>&#8592; Previous 5</button>
        <?php endif; ?>

        <span style="color:#ccc;">Pagina <?= $page ?> din <?= $pages ?> (<?= $total ?> studenti)</span>

        <?php if ($page < $pages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-primary">Next 5 &#8594;</a>
        <?php else: ?>
            <button class="btn btn-primary" disabled>Next 5 &#8594;</button>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
