<?php
require_once 'config.php';
requireLogin();

$message  = '';
$user_id  = $_SESSION['user_id'];
$pdo      = getPDOConnection();

// Procesare actualizare profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "<div class='message-box error'>Token CSRF invalid. Reincarca pagina.</div>";
    } else {
        $full_name = $_POST['full_name'] ?? '';
        $bio       = $_POST['bio']       ?? '';

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $bio, $user_id])) {
            $message = "<div class='message-box success'>Profil actualizat cu succes!</div>";
            $log = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Actualizare Profil')");
            $log->execute([$user_id]);
        } else {
            $message = "<div class='message-box error'>Eroare la actualizare.</div>";
        }
    }
}

// Procesare upload poza profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_pic'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = "<div class='message-box error'>Token CSRF invalid. Reincarca pagina.</div>";
    } elseif (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowed_ext  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['profile_pic']['tmp_name']);
        finfo_close($finfo);

        $original_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (!in_array($mime_type, $allowed_mime) || !in_array($original_ext, $allowed_ext)) {
            $message = "<div class='message-box error'>Tip de fisier nepermis. Sunt acceptate doar imagini (jpg, png, gif, webp).</div>";
        } else {
            $safe_name  = bin2hex(random_bytes(16)) . '.' . $original_ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $safe_name)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $stmt->execute([$safe_name, $user_id]);
                $message = "<div class='message-box success'>Fisier incarcat cu succes!</div>";
                $log = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Upload Fisier')");
                $log->execute([$user_id]);
            } else {
                $message = "<div class='message-box error'>Eroare la mutarea fisierului.</div>";
            }
        }
    } else {
        $message = "<div class='message-box error'>Eroare la upload sau niciun fisier selectat.</div>";
    }
}

// Procesare stergere poza profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_pic']) && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user_data['profile_pic'])) {
        $file_path = __DIR__ . '/uploads/' . $user_data['profile_pic'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $stmt = $pdo->prepare("UPDATE users SET profile_pic = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = "<div class='message-box success'>Poza de profil a fost stearsa!</div>";
        $log = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Stergere Poza Profil')");
        $log->execute([$user_id]);
    }
}

// Preluare date curente
$stmt = $pdo->prepare("SELECT username, full_name, bio, profile_pic FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Profil - Lab 7 PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-large glass-panel">
        <div class="header-nav">
            <h2>Profilul Meu</h2>
            <div class="nav-links">
                <a href="index.php" class="btn btn-small">Inapoi la Dashboard</a>
            </div>
        </div>

        <?= $message ?>

        <div style="display: flex; gap: 40px; flex-wrap: wrap;">

            <!-- Formular Precompletat -->
            <div style="flex: 2; min-width: 300px;">
                <h3>Date Personale</h3>
                <form method="POST" action="profile.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                    <div class="form-group">
                        <label for="username">Nume Utilizator (Nu poate fi modificat)</label>
                        <input type="text" id="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Nume Complet</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bio">Biografie scurta</label>
                        <textarea id="bio" name="bio" class="form-control"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Salveaza Modificarile</button>
                </form>
            </div>

            <!-- Upload Fisiere -->
            <div style="flex: 1; min-width: 250px; text-align: center; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 20px;">
                <h3>Poza de Profil</h3>

                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Poza profil" class="profile-img-preview" style="margin: 0 auto 15px auto;">
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                        <button type="submit" name="delete_pic" class="btn btn-danger btn-small">Sterge Poza curenta</button>
                    </form>
                <?php else: ?>
                    <div style="width:150px; height:150px; border-radius:50%; background:rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center; margin: 0 auto 15px auto;">
                        Fara poza
                    </div>
                <?php endif; ?>

                <form method="POST" action="profile.php" enctype="multipart/form-data" style="margin-top: 20px;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                    <div class="form-group">
                        <label for="profile_pic">Incarca o poza noua (Max 2MB):</label>
                        <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="upload_pic" class="btn btn-primary btn-small">Incarca Fisier</button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>
