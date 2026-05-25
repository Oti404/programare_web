/* ================================================
   ORIGINAL.JS — Calculator Preț Estimativ Live
   Funcționalitate originală: calculează prețul unui vehicul
   în timp real pe baza: marcă, model, an, km, dotări.
   Afișează animație de numărare și bare de progres CSS.
   Fără librării externe
   ================================================ */

(function () {
    "use strict";

    var animTimer = null;
    var lastPrice = 0;

    /* ---- Calcul preț ---- */
    function calcPrice() {
        var marcaEl = document.getElementById("calcMarca");
        var modelEl = document.getElementById("calcModel");
        var anEl = document.getElementById("calcAn");
        var kmEl = document.getElementById("calcKm");
        if (!marcaEl || !modelEl || !anEl || !kmEl) return;

        var marca = marcaEl.value;
        var model = modelEl.value;
        var an = parseInt(anEl.value) || 2020;
        var km = parseInt(kmEl.value) || 0;

        /* Preț de bază */
        var baza = 0;
        if (PRET_BAZA[marca] && PRET_BAZA[marca][model]) {
            baza = PRET_BAZA[marca][model];
        }
        if (baza === 0) { updateDisplay(0); return; }

        /* Depreciere an: 8% pe an față de 2024 */
        var ani = Math.max(0, 2024 - an);
        var deprecAn = baza * 0.08 * ani;

        /* Depreciere km: 0.05 EUR/km */
        var deprecKm = km * 0.05;

        /* Dotări */
        var dotari = 0;
        var dotariIds = ["calc_ac", "calc_gps", "calc_trapa", "calc_camera", "calc_scaune", "calc_pilot"];
        dotariIds.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.checked && DOTARI_PRET[id]) dotari += DOTARI_PRET[id];
        });

        var final = Math.max(500, Math.round(baza - deprecAn - deprecKm + dotari));

        /* Actualizează barele */
        updateBars(baza, deprecAn, deprecKm, dotari, final);

        /* Animație numărare */
        animatePrice(lastPrice, final);
        lastPrice = final;
    }

    function animatePrice(from, to) {
        clearInterval(animTimer);
        var steps = 25;
        var step = 0;
        var diff = to - from;
        animTimer = setInterval(function () {
            step++;
            var eased = from + diff * (step / steps);
            setDisplayValue(Math.round(eased));
            if (step >= steps) {
                clearInterval(animTimer);
                setDisplayValue(to);
            }
        }, 20);
    }

    function setDisplayValue(val) {
        var el = document.getElementById("calcPretFinal");
        if (el) el.textContent = val.toLocaleString("ro-RO") + " €";
    }

    function updateDisplay(val) {
        setDisplayValue(val);
        var el = document.getElementById("calcPretFinal");
        if (el) el.parentNode.classList.toggle("no-price", val === 0);
    }

    function updateBars(baza, deprecAn, deprecKm, dotari, final) {
        /* Bară bază */
        setBar("barBaza", baza, baza, baza.toLocaleString("ro-RO") + " €");
        /* Bară depreciere an */
        setBar("barDeprecAn", deprecAn, baza, "-" + Math.round(deprecAn).toLocaleString("ro-RO") + " €");
        /* Bară depreciere km */
        setBar("barDeprecKm", deprecKm, baza, "-" + Math.round(deprecKm).toLocaleString("ro-RO") + " €");
        /* Bară dotări */
        setBar("barDotari", dotari, baza, "+" + dotari.toLocaleString("ro-RO") + " €");
    }

    function setBar(id, value, max, label) {
        var bar = document.getElementById(id);
        if (!bar) return;
        var pct = max > 0 ? Math.min(100, Math.round((value / max) * 100)) : 0;
        var fill = bar.querySelector(".bar-fill");
        var lbl = bar.querySelector(".bar-label");
        if (fill) fill.style.width = pct + "%";
        if (lbl) lbl.textContent = label;
    }

    /* ---- Populare dinamică Model din Marcă ---- */
    function populateCalcModel(marca) {
        var modelEl = document.getElementById("calcModel");
        if (!modelEl) return;
        modelEl.innerHTML = "<option value=''>-- Selectați modelul --</option>";
        if (MARCI_MODELE[marca]) {
            MARCI_MODELE[marca].forEach(function (m) {
                var opt = document.createElement("option");
                opt.value = m;
                opt.textContent = m;
                modelEl.appendChild(opt);
            });
        }
        calcPrice();
    }

    /* ---- Inițializare ---- */
    function initCalculator() {
        var section = document.getElementById("calcSection");
        if (!section) return;
        if (typeof PRET_BAZA === "undefined" || typeof MARCI_MODELE === "undefined") return;

        /* Populare selecturi marcă */
        var marcaEl = document.getElementById("calcMarca");
        if (marcaEl) {
            Object.keys(MARCI_MODELE).forEach(function (m) {
                var opt = document.createElement("option");
                opt.value = m;
                opt.textContent = m;
                marcaEl.appendChild(opt);
            });
            marcaEl.addEventListener("change", function () {
                populateCalcModel(this.value);
            });
        }

        /* An range — afișare valoare */
        var anEl = document.getElementById("calcAn");
        var anLabel = document.getElementById("calcAnLabel");
        if (anEl && anLabel) {
            anLabel.textContent = anEl.value;
            anEl.addEventListener("input", function () {
                anLabel.textContent = this.value;
                calcPrice();
            });
        }

        /* Km range — afișare valoare */
        var kmEl = document.getElementById("calcKm");
        var kmLabel = document.getElementById("calcKmLabel");
        if (kmEl && kmLabel) {
            kmLabel.textContent = parseInt(kmEl.value).toLocaleString("ro-RO") + " km";
            kmEl.addEventListener("input", function () {
                kmLabel.textContent = parseInt(this.value).toLocaleString("ro-RO") + " km";
                calcPrice();
            });
        }

        /* Model & dotări */
        var modelEl = document.getElementById("calcModel");
        if (modelEl) modelEl.addEventListener("change", calcPrice);

        var checkboxes = section.querySelectorAll("input[type='checkbox']");
        checkboxes.forEach(function (cb) { cb.addEventListener("change", calcPrice); });

        calcPrice();
    }

    document.addEventListener("DOMContentLoaded", initCalculator);
})();
