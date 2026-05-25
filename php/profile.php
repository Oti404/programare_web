<?php
require_once 'config.php';
requireLogin();

$message = '';
$user_id = $_SESSION['user_id'];
$pdo = getPDOConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'] ?? '';
    $bio       = $_POST['bio'] ?? '';
    $city      = $_POST['city'] ?? '';

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ?, city = ? WHERE id = ?");
    if ($stmt->execute([$full_name, $bio, $city, $user_id])) {
        $message = "<div class='message-box success'>Profil actualizat cu succes!</div>";
        $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Actualizare Profil')")->execute([$user_id]);
    } else {
        $message = "<div class='message-box error'>Eroare la actualizare.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_pic'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = basename($_FILES['profile_pic']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
            $upload_dir = __DIR__ . '/uploads/';

            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?")->execute([$new_file_name, $user_id]);
                $message = "<div class='message-box success'>Poza incarcata cu succes!</div>";
                $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Upload Poza Profil')")->execute([$user_id]);
            } else {
                $message = "<div class='message-box error'>Eroare la mutarea fisierului.</div>";
            }
        } else {
            $message = "<div class='message-box error'>Format invalid. Doar JPG, PNG, GIF sunt permise.</div>";
        }
    } else {
        $message = "<div class='message-box error'>Eroare la upload sau niciun fisier selectat.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_pic'])) {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user_data['profile_pic'])) {
        $file_path = __DIR__ . '/uploads/' . $user_data['profile_pic'];
        if (file_exists($file_path)) unlink($file_path);

        $pdo->prepare("UPDATE users SET profile_pic = NULL WHERE id = ?")->execute([$user_id]);
        $message = "<div class='message-box success'>Poza de profil a fost stearsa!</div>";
        $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, 'Stergere Poza Profil')")->execute([$user_id]);
    }
}

$stmt = $pdo->prepare("SELECT u.username, u.full_name, u.bio, u.city, u.profile_pic, u.role_id FROM users u WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$roles_stmt = $pdo->query("SELECT id, name FROM roles ORDER BY id");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <div style="flex: 2; min-width: 300px;">
                <h3>Date Personale</h3>
                <form method="POST" action="profile.php">
                    <div class="form-group">
                        <label for="username">Nume Utilizator <small>(input)</small></label>
                        <input type="text" id="username" class="form-control"
                               value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Nume Complet <small>(input)</small></label>
                        <input type="text" id="full_name" name="full_name" class="form-control"
                               value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="city">Oras <small>(select & option)</small></label>
                        <select id="city" name="city" class="form-control">
                            <?php
                            $cities = ['Cluj-Napoca', 'Bucuresti', 'Timisoara', 'Iasi'];
                            foreach ($cities as $c):
                            ?>
                                <option value="<?= $c ?>" <?= ($user['city'] ?? '') === $c ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bio">Biografie <small>(textarea)</small></label>
                        <textarea id="bio" name="bio" class="form-control"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Salveaza Modificarile</button>
                </form>
            </div>

            <div style="flex: 1; min-width: 250px; text-align: center; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 20px;">
                <h3>Poza de Profil</h3>

                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Poza profil" class="profile-img-preview" style="margin: 0 auto 15px auto;">
                    <form method="POST" action="profile.php">
                        <button type="submit" name="delete_pic" class="btn btn-danger btn-small">Sterge Poza curenta</button>
                    </form>
                <?php else: ?>
                    <div style="width:150px; height:150px; border-radius:50%; background:rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center; margin: 0 auto 15px auto;">
                        Fara poza
                    </div>
                <?php endif; ?>

                <form method="POST" action="profile.php" enctype="multipart/form-data" style="margin-top: 20px;">
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
