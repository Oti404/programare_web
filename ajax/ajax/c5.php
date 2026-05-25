<?php
require_once '../config.php';
requireLogin();

$pdo  = getPDOConnection();
$stmt = $pdo->query("SELECT u.id, u.username FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student' ORDER BY u.username");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Cerinta 5 - Editor Vanilla JS</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container glass-panel">
    <div class="header-nav">
        <h2>Cerinta 5 — Editor AJAX (Vanilla JS)</h2>
        <a href="../index.php" class="btn btn-small">Inapoi</a>
    </div>

    <div id="error-box" class="message-box error" style="display:none;"></div>
    <div id="success-box" class="message-box success" style="display:none;"></div>

    <div class="form-group">
        <label for="student-select">Selecteaza student:</label>
        <select id="student-select" class="form-control">
            <option value="">-- Alege --</option>
            <?php foreach ($students as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['username']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="form-fields" style="display:none;">
        <input type="hidden" id="student-id">

        <div class="form-group">
            <label>Username (doar citire)</label>
            <input type="text" id="field-username" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Nume Complet</label>
            <input type="text" id="field-full-name" class="form-control">
        </div>
        <div class="form-group">
            <label>Bio</label>
            <textarea id="field-bio" class="form-control" rows="3"></textarea>
        </div>

        <button id="btn-save" class="btn btn-primary" disabled onclick="saveStudent()">Salveaza</button>
    </div>
</div>

<script>
var dirty = false;
var savedValues = {};

var select    = document.getElementById('student-select');
var formFields = document.getElementById('form-fields');
var btnSave   = document.getElementById('btn-save');
var fName     = document.getElementById('field-full-name');
var fBio      = document.getElementById('field-bio');

select.addEventListener('change', function() {
    if (dirty) {
        var confirmed = confirm('Ai modificari nesalvate. Vrei sa le salvezi inainte de a continua?');
        if (confirmed) {
            saveStudent(function() { loadStudent(select.value); });
            return;
        }
    }
    dirty = false;
    loadStudent(select.value);
});

[fName, fBio].forEach(function(el) {
    el.addEventListener('input', function() {
        dirty = fName.value !== savedValues.full_name || fBio.value !== savedValues.bio;
        btnSave.disabled = !dirty;
    });
});

function loadStudent(id) {
    if (!id) { formFields.style.display = 'none'; return; }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'c5_get.php?id=' + id);
    xhr.onload = function() {
        try {
            var s = JSON.parse(xhr.responseText);
            if (xhr.status !== 200) { showError(s.error || 'Eroare server: ' + xhr.status); return; }
            document.getElementById('student-id').value     = s.id;
            document.getElementById('field-username').value = s.username;
            fName.value = s.full_name || '';
            fBio.value  = s.bio       || '';
            savedValues = { full_name: fName.value, bio: fBio.value };
            dirty = false;
            btnSave.disabled = true;
            formFields.style.display = 'block';
            hideMessages();
        } catch (e) {
            showError('Raspuns invalid de la server. Raspuns primit: ' + xhr.responseText.substring(0, 100));
        }
    };
    xhr.onerror = function() { showError('Eroare de retea. Verifica conexiunea.'); };
    xhr.send();
}

function saveStudent(callback) {
    var payload = {
        id:        parseInt(document.getElementById('student-id').value),
        full_name: fName.value,
        bio:       fBio.value
    };

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'c5_save.php');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
            savedValues = { full_name: fName.value, bio: fBio.value };
            dirty = false;
            btnSave.disabled = true;
            showSuccess('Modificarile au fost salvate!');
            if (typeof callback === 'function') callback();
        } else {
            var res = JSON.parse(xhr.responseText);
            showError(res.error || 'Eroare la salvare.');
        }
    };
    xhr.onerror = function() { showError('Eroare de retea.'); };
    xhr.send(JSON.stringify(payload));
}

function showError(msg)   { document.getElementById('error-box').textContent = msg; document.getElementById('error-box').style.display = 'block'; document.getElementById('success-box').style.display = 'none'; }
function showSuccess(msg) { document.getElementById('success-box').textContent = msg; document.getElementById('success-box').style.display = 'block'; document.getElementById('error-box').style.display = 'none'; }
function hideMessages()   { document.getElementById('error-box').style.display = 'none'; document.getElementById('success-box').style.display = 'none'; }
</script>
</body>
</html>
