<?php require_once '../config.php'; requireLogin(); ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Cerinta 3 - jQuery AJAX</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container container-large glass-panel">
    <div class="header-nav">
        <h2>Cerinta 3 — Paginare AJAX (jQuery)</h2>
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
        <button id="btn-prev" class="btn">&#8592; Previous 5</button>
        <span id="page-info" style="line-height:2.4em; color:#ccc;"></span>
        <button id="btn-next" class="btn btn-primary">Next 5 &#8594;</button>
    </div>
</div>

<script>
var offset = 0;
var limit  = 5;
var total  = 0;

function loadPage() {
    $.ajax({
        url: 'c1_data.php',
        data: { offset: offset },
        dataType: 'json',
        success: function(data) {
            total = data.total;
            var tbody = $('#table-body').empty();
            $.each(data.students, function(i, s) {
                tbody.append(
                    $('<tr>').append(
                        $('<td>').text(s.username),
                        $('<td>').text(s.full_name),
                        $('<td>').text(s.bio)
                    )
                );
            });
            var page  = Math.floor(offset / limit) + 1;
            var pages = Math.ceil(total / limit);
            $('#page-info').text('Pagina ' + page + ' din ' + pages + ' (' + total + ' studenti)');
            $('#btn-prev').prop('disabled', offset === 0);
            $('#btn-next').prop('disabled', offset + limit >= total);
        },
        error: function(xhr, status, err) {
            var msg = (status === 'error' && xhr.status === 0)
                ? 'Eroare de retea. Verifica conexiunea la internet.'
                : 'Eroare server: ' + xhr.status;
            $('#error-box').text(msg).show();
        }
    });
}

$('#btn-prev').on('click', function() { offset -= limit; loadPage(); });
$('#btn-next').on('click', function() { offset += limit; loadPage(); });

loadPage();
</script>
</body>
</html>
