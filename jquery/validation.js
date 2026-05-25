/* ================================================
   VALIDATION.JS — Îmbunătățit (jQuery)
   Logica de validare și dependențe între câmpuri
   ================================================ */

$(function () {
    // 1. Populare și Dependențe
    function populeazaSelect($sel, date) {
        $sel.empty().append('<option value="">-- Alege --</option>');
        $.each(date, function (cheie) {
            $sel.append('<option value="' + cheie + '">' + cheie + '</option>');
        });
    }

    // Marca -> Model (pentru toate paginile)
    var $marca = $("#marcaAuto, #marcaEdit, #calcMarca");

    // Inițializare select-uri de marcă
    if ($("#marcaAuto").length) populeazaSelect($("#marcaAuto"), MARCI_MODELE);
    if ($("#marcaEdit").length) populeazaSelect($("#marcaEdit"), MARCI_MODELE);

    $marca.on("change", function () {
        var marcaSels = $(this).val();
        var $form = $(this).closest("form, section, .container");
        var $modelDest = $form.find("select").filter(function () {
            var id = this.id || "";
            return id.toLowerCase().indexOf("model") > -1;
        });

        $modelDest.empty().append('<option value="">-- Alege model --</option>');
        if (MARCI_MODELE[marcaSels]) {
            $.each(MARCI_MODELE[marcaSels], function (i, m) {
                $modelDest.append('<option value="' + m + '">' + m + '</option>');
            });
        }
    });

    // Județ -> Localitate
    if ($("#judetAuto").length) {
        populeazaSelect($("#judetAuto"), JUDETE_LOCALITATI);
        $("#judetAuto").on("change", function () {
            var j = $(this).val();
            var $l = $("#localitateAuto");
            $l.empty().append('<option value="">-- Alege localitate --</option>');
            if (JUDETE_LOCALITATI[j]) {
                $.each(JUDETE_LOCALITATI[j], function (i, loc) {
                    $l.append('<option value="' + loc + '">' + loc + '</option>');
                });
            }
        });
    }

    // Actualizare label range în timp real
    $("#discountRange").on("input", function () {
        $("#discountVal").text($(this).val() + "%");
    });

    // 2. Validare Formulare
    $("form").on("submit", function (e) {
        var formOk = true;
        $(".error-msg").remove();
        $(".field-error").removeClass("field-error");

        var $form = $(this);

        // 2a. Validare câmpuri obligatorii și reguli specifice
        $form.find("input, select, textarea").each(function () {
            var $el = $(this);
            var val = $.trim($el.val());
            var type = $el.attr("type");
            var id = $el.attr("id");
            var name = $el.attr("name");

            // Ignorăm butoanele și câmpurile ascunse/readonly
            if (type === "submit" || type === "reset" || type === "hidden" || $el.prop("readonly")) return;

            var isRequired = $el.prop("required") || $el.closest("td").prev().text().indexOf("*") > -1;

            if (isRequired && !val) {
                if (type !== "radio" && type !== "checkbox") {
                    $el.addClass("field-error").after('<span class="error-msg">Câmp obligatoriu!</span>');
                    formOk = false;
                }
            }

            // Validări specifice dacă avem valoare
            if (val) {
                if (type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    $el.addClass("field-error").after('<span class="error-msg">Format email invalid!</span>');
                    formOk = false;
                }
                if (type === "tel" && !/^\d{10,15}$/.test(val.replace(/\s/g, ""))) {
                    $el.addClass("field-error").after('<span class="error-msg">Telefon invalid (min. 10 cifre)!</span>');
                    formOk = false;
                }
                if ((id && id.toLowerCase().indexOf("nume") > -1) && val.length < 3) {
                    $el.addClass("field-error").after('<span class="error-msg">Numele trebuie să aibă minim 3 caractere!</span>');
                    formOk = false;
                }
                if (type === "password" && val.length < 6) {
                    $el.addClass("field-error").after('<span class="error-msg">Parola trebuie să aibă minim 6 caractere!</span>');
                    formOk = false;
                }
            }
        });

        if (!formOk) {
            e.preventDefault();
            // Scroll fluid la prima eroare
            var $firstError = $(".field-error").first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 120
                }, 500);
            }
        }
    });

    // Curățare eroare la scriere/interacțiune
    $("input, select, textarea").on("input change focus", function () {
        $(this).removeClass("field-error").next(".error-msg").remove();
    });
});
