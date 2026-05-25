/* ================================================
   TABLE.JS — Gestiune Tabele (Classic & Vertical)
   ================================================ */

$(function () {
    if (typeof MASINI_DATA === "undefined") return;

    var currentSortKey = null;
    var currentSortDir = 'asc'; // 'asc' or 'desc'

    // --- 1. TABEL CLASIC (ORIZONTAL) ---

    function renderClassicTable(data) {
        var $body = $("#sortableBody").empty();
        $.each(data, function (i, car) {
            var $tr = $("<tr>");
            $tr.append("<td>" + car.id + "</td>");
            $tr.append("<td>" + car.marca + "</td>");
            $tr.append("<td>" + car.model + "</td>");
            $tr.append("<td>" + car.an + "</td>");
            $tr.append("<td>" + car.combustibil + "</td>");
            $tr.append("<td>" + car.km.toLocaleString() + " km</td>");
            $tr.append("<td>" + car.pret.toLocaleString() + " €</td>");
            $tr.append('<td><a href="edit5.html" class="btn-table-action">Edit</a></td>');

            // Highlight coloana sortată
            if (currentSortKey) {
                var keys = ["id", "marca", "model", "an", "combustibil", "km", "pret"];
                var idx = keys.indexOf(currentSortKey);
                if (idx !== -1) {
                    $tr.find("td").eq(idx).addClass("td-sorted");
                }
            }

            $body.append($tr);
        });
    }

    function initClassicHeader() {
        var $head = $("#sortableHead").empty();
        var headers = [
            { label: "ID", key: "id" },
            { label: "Marcă", key: "marca" },
            { label: "Model", key: "model" },
            { label: "An", key: "an" },
            { label: "Motor", key: "combustibil" },
            { label: "KM", key: "km" },
            { label: "Preț", key: "pret" },
            { label: "Acțiuni", key: null }
        ];

        var $tr = $("<tr>");
        $.each(headers, function (i, h) {
            var $th = $('<th class="sortable-th">').text(h.label);
            if (h.key) {
                $th.attr("data-key", h.key);
                if (h.key === currentSortKey) {
                    $th.addClass("col-sorted").attr("data-dir", currentSortDir);
                }
            } else {
                $th.removeClass("sortable-th");
            }
            $tr.append($th);
        });
        $head.append($tr);
    }

    // Event listener pentru sortare tabel clasic
    $("#sortableHead").on("click", "th.sortable-th", function () {
        var key = $(this).data("key");
        if (currentSortKey === key) {
            currentSortDir = (currentSortDir === 'asc' ? 'desc' : 'asc');
        } else {
            currentSortKey = key;
            currentSortDir = 'asc';
        }

        sortData(MASINI_DATA, currentSortKey, currentSortDir);
        initClassicHeader();
        renderClassicTable(MASINI_DATA);

        // Dacă există și tabelul vertical, îl resincronizăm
        renderVerticalTable(MASINI_DATA);
    });

    // --- 2. TABEL VERTICAL ---

    function renderVerticalTable(data) {
        var $wrapper = $("#verticalTableWrapper").empty();
        if (!data.length) return;

        var rows = [
            { label: "ID Vehicul", key: "id" },
            { label: "Marcă", key: "marca" },
            { label: "Model", key: "model" },
            { label: "An Fabricație", key: "an" },
            { label: "Combustibil", key: "combustibil" },
            { label: "Kilometraj", key: "km" },
            { label: "Preț Estimativ", key: "pret" }
        ];

        var $table = $('<table class="vertical-table">');

        $.each(rows, function (i, row) {
            var $tr = $("<tr>");
            var $th = $('<th class="vert-header">').text(row.label).attr("data-key", row.key);

            if (row.key === currentSortKey) {
                $th.addClass("col-sorted").attr("data-dir", currentSortDir);
            }

            $tr.append($th);

            $.each(data, function (j, car) {
                var val = car[row.key];
                if (row.key === 'km') val = val.toLocaleString() + " km";
                if (row.key === 'pret') val = val.toLocaleString() + " €";

                var $td = $("<td>").text(val);
                if (row.key === currentSortKey) $td.addClass("td-sorted");
                $tr.append($td);
            });

            $table.append($tr);
        });

        $wrapper.append($table);
    }

    // Event listener pentru sortare tabel vertical (click pe prima coloană)
    $("#verticalTableWrapper").on("click", "th.vert-header", function () {
        var key = $(this).data("key");
        if (currentSortKey === key) {
            currentSortDir = (currentSortDir === 'asc' ? 'desc' : 'asc');
        } else {
            currentSortKey = key;
            currentSortDir = 'asc';
        }

        sortData(MASINI_DATA, currentSortKey, currentSortDir);
        renderVerticalTable(MASINI_DATA);

        // Resincronizăm și tabelul clasic
        initClassicHeader();
        renderClassicTable(MASINI_DATA);
    });

    // --- LOGICĂ SORTARE COMUNĂ ---

    function sortData(arr, key, dir) {
        arr.sort(function (a, b) {
            var valA = a[key];
            var valB = b[key];

            var res = 0;
            if (typeof valA === 'number' && typeof valB === 'number') {
                res = valA - valB;
            } else {
                res = valA.toString().localeCompare(valB.toString());
            }

            return (dir === 'asc' ? res : -res);
        });
    }

    // Inițializare
    initClassicHeader();
    renderClassicTable(MASINI_DATA);
    renderVerticalTable(MASINI_DATA);
});
