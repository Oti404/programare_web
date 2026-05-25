<?php
require_once 'config.php';
requireLogin();

$content  = '';
$error    = '';
$filename = $_GET['file'] ?? '';

$available = [
    'curs1.txt' => 'Cursul 1 - Introducere in Programare Web',
    'curs2.txt' => 'Cursul 2 - Baze de Date',
    'curs3.txt' => 'Cursul 3 - Securitate Web',
];

if ($filename !== '') {
    if (!array_key_exists($filename, $available)) {
        $error = "Fisier invalid sau neautorizat.";
    } else {
        $filepath = __DIR__ . '/docs/' . $filename;
        if (file_exists($filepath) && is_file($filepath)) {
            $content = file_get_contents($filepath);
        } else {
            $error = "Fisierul nu a fost gasit: <code>" . htmlspecialchars($filename) . "</code>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Viewer Materiale - Lab 8</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-large glass-panel">
        <div class="header-nav">
            <h2>Viewer Materiale de Curs</h2>
            <div class="nav-links">
                <a href="index.php" class="btn btn-small">Inapoi la Dashboard</a>
            </div>
        </div>

        <p>Selecteaza un fisier de curs din lista sau acceseaza direct prin URL: <code>?file=curs1.txt</code></p>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px;">
            <?php foreach ($available as $f => $label): ?>
                <a href="pta.php?file=<?= urlencode($f) ?>" class="btn btn-small <?= ($filename === $f) ? 'btn-primary' : '' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($error): ?>
            <div class="message-box error"><?= $error ?></div>
        <?php elseif ($content !== ''): ?>
            <div style="margin-top: 10px;">
                <h3>Continut: <code><?= htmlspecialchars($filename) ?></code></h3>
                <pre style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1);
                            border-radius: 8px; padding: 20px; white-space: pre-wrap;
                            word-break: break-all; color: #e0e0e0; font-size: 0.95em;">
<?= htmlspecialchars($content) ?></pre>
            </div>
        <?php else: ?>
            <p>Niciun fisier selectat.</p>
        <?php endif; ?>

    </div>
</body>
</html>
