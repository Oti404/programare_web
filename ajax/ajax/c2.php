<?php require_once '../config.php'; requireLogin(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Cerinta 2 - XML + Vanilla JS</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container container-large glass-panel">
    <div class="header-nav">
        <h2>Cerinta 2 — Paginare AJAX (XML)</h2>
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
    xhr.open('GET', 'c2_data.php?offset=' + offset);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var xml = xhr.responseXML;
            var root = xml.getElementsByTagName('response')[0];
            total = parseInt(root.getAttribute('total'));
            renderTable(xml.getElementsByTagName('student'));
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
    for (var i = 0; i < students.length; i++) {
        var s  = students[i];
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>' + getText(s, 'username')  + '</td>' +
                       '<td>' + getText(s, 'full_name') + '</td>' +
                       '<td>' + getText(s, 'bio')       + '</td>';
        tbody.appendChild(tr);
    }
}

function getText(node, tag) {
    var el = node.getElementsByTagName(tag)[0];
    var d  = document.createElement('div');
    d.appendChild(document.createTextNode(el ? el.textContent : ''));
    return d.innerHTML;
}

function updateControls() {
    document.getElementById('btn-prev').disabled = (offset === 0);
    document.getElementById('btn-next').disabled = (offset + limit >= total);
    var page  = Math.floor(offset / limit) + 1;
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

loadPage();
</script>
</body>
</html>
