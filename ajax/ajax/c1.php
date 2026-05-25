<?php require_once '../config.php'; requireLogin(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Cerinta 1 - JSON + Vanilla JS</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container container-large glass-panel">
    <div class="header-nav">
        <h2>Cerinta 1 — Paginare AJAX (JSON)</h2>
        <a href="../index.php" class="btn btn-small">Inapoi</a>
    </div>

    <div id="error-box" class="message-box error" style="display:none;"></div>

    <table>
        <thead>
            <tr><th>Username</th><th>Nume Complet</th><th>Bio</th></tr>
        </thead>
        <tbody id="table-body"></tbody>
    </table>

    <div style="display:flex; gap:10px; margin-top:15px;">
        <button id="btn-prev" class="btn" onclick="changePage(-1)">&#8592; Previous 5</button>
        <span id="page-info" style="line-height:2.4em; color:#ccc;"></span>
        <button id="btn-next" class="btn btn-primary" onclick="changePage(1)">Next 5 &#8594;</button>
    </div>
</div>

<script>
var offset = 0;
var limit  = 5;
var total  = 0;

function loadPage() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'c1_data.php?offset=' + offset);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            total = data.total;
            renderTable(data.students);
            updateControls();
        } else {
            showError('Eroare server: ' + xhr.status);
        }
    };
    xhr.onerror = function() {
        showError('Eroare de retea. Verifica conexiunea la internet.');
    };
    xhr.send();
}

function renderTable(students) {
    var tbody = document.getElementById('table-body');
    tbody.innerHTML = '';
    students.forEach(function(s) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>' + escHtml(s.username)  + '</td>' +
                       '<td>' + escHtml(s.full_name) + '</td>' +
                       '<td>' + escHtml(s.bio)       + '</td>';
        tbody.appendChild(tr);
    });
}

function updateControls() {
    document.getElementById('btn-prev').disabled = (offset === 0);
    document.getElementById('btn-next').disabled = (offset + limit >= total);
    var page = Math.floor(offset / limit) + 1;
    var pages = Math.ceil(total / limit);
    document.getElementById('page-info').textContent = 'Pagina ' + page + ' din ' + pages + ' (' + total + ' studenti)';
}

function changePage(dir) {
    offset += dir * limit;
    loadPage();
}

function showError(msg) {
    var box = document.getElementById('error-box');
    box.textContent = msg;
    box.style.display = 'block';
}

function escHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

loadPage();
</script>
</body>
</html>
