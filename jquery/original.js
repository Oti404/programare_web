/* ================================================
   ORIGINAL.JS — Calculator Simplificat
   ================================================ */

$(function () {
    if (!$("#calcSection").length) return;

    // Populare mărci
    $.each(MARCI_MODELE, function (m) {
        $("#calcMarca").append('<option value="' + m + '">' + m + '</option>');
    });

    function calculeaza() {
        var marca = $("#calcMarca").val();
        var model = $("#calcModel").val();
        var an = parseInt($("#calcAn").val());
        var km = parseInt($("#calcKm").val());

        $("#calcAnLabel").text(an);
        $("#calcKmLabel").text(km.toLocaleString() + " km");

        var pretBaza = 0, deprecAn = 0, deprecKm = 0, extraDotari = 0;

        if (PRET_BAZA[marca] && PRET_BAZA[marca][model]) {
            pretBaza  = PRET_BAZA[marca][model];
            deprecAn  = (2024 - an) * 500;
            deprecKm  = Math.round((km / 1000) * 50);

            $("#calcSection input:checked").each(function() {
                var id = $(this).attr("id");
                if (DOTARI_PRET[id]) extraDotari += DOTARI_PRET[id];
            });
        }

        var pretFinal = Math.max(500, pretBaza - deprecAn - deprecKm + extraDotari);
        $("#calcPretFinal").text(pretFinal.toLocaleString() + " €");

        // Actualizare bare detaliere preț
        var ref = pretBaza || 1;
        $("#barBaza .bar-fill").css("width", pretBaza ? "100%" : "0%");
        $("#barBaza .bar-label").text(pretBaza.toLocaleString() + " €");

        var pAn = Math.min(100, (deprecAn / ref) * 100);
        $("#barDeprecAn .bar-fill").css("width", pAn.toFixed(1) + "%");
        $("#barDeprecAn .bar-label").text("-" + deprecAn.toLocaleString() + " €");

        var pKm = Math.min(100, (deprecKm / ref) * 100);
        $("#barDeprecKm .bar-fill").css("width", pKm.toFixed(1) + "%");
        $("#barDeprecKm .bar-label").text("-" + deprecKm.toLocaleString() + " €");

        var pDot = Math.min(100, (extraDotari / ref) * 100);
        $("#barDotari .bar-fill").css("width", pDot.toFixed(1) + "%");
        $("#barDotari .bar-label").text("+" + extraDotari.toLocaleString() + " €");
    }

    $("#calcSection select, #calcSection input").on("change input", calculeaza);
    calculeaza();
});
