/* ================================================
   TABLE.JS — Tabel sortabil (clasic + vertical)
   Date preluate din data.js (MASINI_DATA)
   Fără librării externe
   ================================================ */

(function () {
    "use strict";

    /* ============================================
       TABEL CLASIC SORTABIL
       ============================================ */
    var classicSortCol = null;
    var classicSortDir = "asc";

    var COLUMNS = [
        { key: "id",          label: "ID Auto" },
        { key: "marca",       label: "Marcă" },
        { key: "model",       label: "Model" },
        { key: "an",          label: "An Fabricație" },
        { key: "combustibil", label: "Tip Combustibil" },
        { key: "transmisie",  label: "Transmisie" },
        { key: "km",          label: "Rulaj (km)" },
        { key: "pret",        label: "Preț (EUR)" },
        { key: "stare",       label: "Stare" }
    ];

    function sortData(data, col, dir) {
        return data.slice().sort(function (a, b) {
            var va = a[col], vb = b[col];
            if (typeof va === "number" && typeof vb === "number") {
                return dir === "asc" ? va - vb : vb - va;
            }
            va = String(va).toLowerCase();
            vb = String(vb).toLowerCase();
            if (va < vb) return dir === "asc" ? -1 : 1;
            if (va > vb) return dir === "asc" ?  1 : -1;
            return 0;
        });
    }

    function renderClassicTable() {
        var thead = document.getElementById("sortableHead");
        var tbody = document.getElementById("sortableBody");
        if (!thead || !tbody) return;

        /* Header */
        thead.innerHTML = "";
        var tr = document.createElement("tr");
        COLUMNS.forEach(function (col) {
            var th = document.createElement("th");
            th.className = "sortable-th";
            if (classicSortCol === col.key) {
                th.classList.add("col-sorted");
                th.setAttribute("data-dir", classicSortDir);
            }
            th.textContent = col.label;
            th.setAttribute("data-col", col.key);
            th.setAttribute("title", "Sortează după " + col.label);
            th.addEventListener("click", function () {
                var c = this.getAttribute("data-col");
                if (classicSortCol === c) {
                    classicSortDir = classicSortDir === "asc" ? "desc" : "asc";
                } else {
                    classicSortCol = c;
                    classicSortDir = "asc";
                }
                renderClassicTable();
            });
            tr.appendChild(th);
        });
        /* Coloana Acțiuni — nesortabilă */
        var thAct = document.createElement("th");
        thAct.textContent = "Acțiuni";
        tr.appendChild(thAct);
        thead.appendChild(tr);

        /* Rows */
        var data = classicSortCol ? sortData(MASINI_DATA, classicSortCol, classicSortDir) : MASINI_DATA;
        tbody.innerHTML = "";
        data.forEach(function (row) {
            var tr2 = document.createElement("tr");
            COLUMNS.forEach(function (col) {
                var td = document.createElement("td");
                td.setAttribute("data-label", col.label);
                if (col.key === "km")   td.textContent = row[col.key].toLocaleString("ro-RO") + " km";
                else if (col.key === "pret") td.textContent = row[col.key].toLocaleString("ro-RO") + " €";
                else td.textContent = row[col.key];
                if (classicSortCol === col.key) td.classList.add("td-sorted");
                tr2.appendChild(td);
            });
            var tdAct = document.createElement("td");
            tdAct.setAttribute("data-label", "Acțiuni");
            var a = document.createElement("a");
            a.href = "edit5.html";
            a.textContent = "Modifică";
            a.className = "btn-table-action";
            tdAct.appendChild(a);
            tr2.appendChild(tdAct);
            tbody.appendChild(tr2);
        });
    }

    /* ============================================
       TABEL VERTICAL SORTABIL
       Rânduri = proprietăți; Coloane = mașini
       Click pe proprietate → sortare coloane
       ============================================ */
    var vertSortProp = null;
    var vertSortDir  = "asc";

    var VERT_ROWS = [
        { key: "id",          label: "ID Auto" },
        { key: "marca",       label: "Marcă" },
        { key: "model",       label: "Model" },
        { key: "an",          label: "An Fabricație" },
        { key: "combustibil", label: "Combustibil" },
        { key: "km",          label: "Rulaj (km)" },
        { key: "pret",        label: "Preț (EUR)" },
        { key: "stare",       label: "Stare" }
    ];

    function sortVertColumns(data, prop, dir) {
        return data.slice().sort(function (a, b) {
            var va = a[prop], vb = b[prop];
            if (typeof va === "number" && typeof vb === "number") {
                return dir === "asc" ? va - vb : vb - va;
            }
            va = String(va).toLowerCase();
            vb = String(vb).toLowerCase();
            if (va < vb) return dir === "asc" ? -1 : 1;
            if (va > vb) return dir === "asc" ?  1 : -1;
            return 0;
        });
    }

    function renderVerticalTable() {
        var wrapper = document.getElementById("verticalTableWrapper");
        if (!wrapper) return;

        var data = vertSortProp ? sortVertColumns(MASINI_DATA, vertSortProp, vertSortDir) : MASINI_DATA;

        var table = document.createElement("table");
        table.className = "vertical-table";

        VERT_ROWS.forEach(function (row) {
            var tr = document.createElement("tr");

            /* Celula antet (prima coloană) */
            var th = document.createElement("th");
            th.className = "vert-header sortable-th";
            if (vertSortProp === row.key) {
                th.classList.add("col-sorted");
                th.setAttribute("data-dir", vertSortDir);
            }
            th.textContent = row.label;
            th.setAttribute("title", "Sortează coloanele după " + row.label);
            th.setAttribute("data-prop", row.key);
            th.style.cursor = "pointer";
            th.addEventListener("click", function () {
                var p = this.getAttribute("data-prop");
                if (vertSortProp === p) {
                    vertSortDir = vertSortDir === "asc" ? "desc" : "asc";
                } else {
                    vertSortProp = p;
                    vertSortDir = "asc";
                }
                renderVerticalTable();
            });
            tr.appendChild(th);

            /* Celule date */
            data.forEach(function (car) {
                var td = document.createElement("td");
                if (row.key === "km")   td.textContent = car[row.key].toLocaleString("ro-RO") + " km";
                else if (row.key === "pret") td.textContent = car[row.key].toLocaleString("ro-RO") + " €";
                else td.textContent = car[row.key];
                tr.appendChild(td);
            });

            table.appendChild(tr);
        });

        wrapper.innerHTML = "";
        wrapper.appendChild(table);
    }

    /* ============================================
       INIȚIALIZARE
       ============================================ */
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof MASINI_DATA === "undefined") return;
        renderClassicTable();
        renderVerticalTable();
    });
})();
