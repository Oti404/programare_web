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
    <title>Cerinta 6 - Editor jQuery</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container glass-panel">
    <div class="header-nav">
        <h2>Cerinta 6 — Editor AJAX (jQuery)</h2>
        <a href="../index.php" class="btn btn-small">Inapoi</a>
    </div>

    <div id="error-box"   class="message-box error"   style="display:none;"></div>
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

        <button id="btn-save" class="btn btn-primary" disabled>Salveaza</button>
    </div>
</div>

<script>
var dirty       = false;
var savedValues = {};

function loadStudent(id) {
    if (!id) { $('#form-fields').hide(); return; }

    $.ajax({
        url: 'c5_get.php',
        data: { id: id },
        dataType: 'json',
        success: function(s) {
            $('#student-id').val(s.id);
            $('#field-username').val(s.username);
            $('#field-full-name').val(s.full_name || '');
            $('#field-bio').val(s.bio || '');
            savedValues = { full_name: s.full_name || '', bio: s.bio || '' };
            dirty = false;
            $('#btn-save').prop('disabled', true);
            $('#form-fields').show();
            hideMessages();
        },
        error: function(xhr, status) {
            var msg = (xhr.status === 0)
                ? 'Eroare de retea. Verifica conexiunea.'
                : 'Eroare server: ' + xhr.status;
            showError(msg);
        }
    });
}

function saveStudent(callback) {
    $.ajax({
        url: 'c5_save.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id:        parseInt($('#student-id').val()),
            full_name: $('#field-full-name').val(),
            bio:       $('#field-bio').val()
        }),
        dataType: 'json',
        success: function() {
            savedValues = { full_name: $('#field-full-name').val(), bio: $('#field-bio').val() };
            dirty = false;
            $('#btn-save').prop('disabled', true);
            showSuccess('Modificarile au fost salvate!');
            if (typeof callback === 'function') callback();
        },
        error: function(xhr) {
            var res = xhr.responseJSON;
            showError((res && res.error) ? res.error : 'Eroare la salvare.');
        }
    });
}

$('#student-select').on('change', function() {
    var newId = $(this).val();
    if (dirty) {
        if (confirm('Ai modificari nesalvate. Vrei sa le salvezi inainte de a continua?')) {
            saveStudent(function() { loadStudent(newId); });
        } else {
            loadStudent(newId);
        }
        return;
    }
    loadStudent(newId);
});

$('#field-full-name, #field-bio').on('input', function() {
    dirty = $('#field-full-name').val() !== savedValues.full_name ||
            $('#field-bio').val()       !== savedValues.bio;
    $('#btn-save').prop('disabled', !dirty);
});

$('#btn-save').on('click', function() { saveStudent(); });

function showError(msg)   { $('#error-box').text(msg).show(); $('#success-box').hide(); }
function showSuccess(msg) { $('#success-box').text(msg).show(); $('#error-box').hide(); }
function hideMessages()   { $('#error-box, #success-box').hide(); }
</script>
</body>
</html>
