<?php
require_once 'config.php';
requireRole('profesor');

$students = [];
try {
    $pdo  = getPDOConnection();
    $stmt = $pdo->prepare("SELECT u.username, u.full_name, u.bio FROM users u INNER JOIN roles r ON u.role_id = r.id WHERE r.name = 'student'");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Pagina Profesor - Lab 7 PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-large glass-panel">
        <div class="header-nav">
            <h2>Panou de control - Profesori</h2>
            <div class="nav-links">
                <a href="index.php" class="btn btn-small">Inapoi la Dashboard</a>
            </div>
        </div>

        <p>Aceasta pagina este accesibila <strong>doar</strong> utilizatorilor cu rolul de <code>profesor</code> sau <code>admin</code>.</p>

        <h3>Lista Studentilor</h3>
        <?php if (count($students) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nume Complet</th>
                        <th>Username</th>
                        <th>Biografie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['username']) ?></td>
                            <td><?= htmlspecialchars($student['bio']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nu exista studenti in baza de date.</p>
        <?php endif; ?>
    </div>
</body>
</html>
