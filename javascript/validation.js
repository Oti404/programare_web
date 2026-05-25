/* ================================================
   VALIDATION.JS — Validare formulare cu feedback CSS
   Fără librării externe, fără jQuery
   ================================================ */

(function () {
    "use strict";

    /* ---- Utilitare vizuale ---- */
    function markError(el, msg) {
        el.classList.remove("field-ok");
        el.classList.add("field-error");
        var wrapper = el.parentNode;
        var old = wrapper.querySelector(".error-msg");
        if (old) old.remove();
        var span = document.createElement("span");
        span.className = "error-msg";
        span.textContent = msg;
        wrapper.appendChild(span);
    }

    function markOk(el) {
        el.classList.remove("field-error");
        el.classList.add("field-ok");
        var wrapper = el.parentNode;
        var old = wrapper.querySelector(".error-msg");
        if (old) old.remove();
    }

    function clearMark(el) {
        el.classList.remove("field-error", "field-ok");
        var wrapper = el.parentNode;
        var old = wrapper.querySelector(".error-msg");
        if (old) old.remove();
    }

    /* ---- Reguli de validare ---- */
    function validateRequired(el) {
        var val = el.value.trim();
        if (!val) { markError(el, "Câmp obligatoriu."); return false; }
        markOk(el); return true;
    }

    function validateMinLength(el, min) {
        if (!validateRequired(el)) return false;
        if (el.value.trim().length < min) {
            markError(el, "Minim " + min + " caractere."); return false;
        }
        markOk(el); return true;
    }

    function validateEmail(el) {
        if (!validateRequired(el)) return false;
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(el.value.trim())) {
            markError(el, "Adresă email invalidă."); return false;
        }
        markOk(el); return true;
    }

    function validatePhone(el) {
        if (!validateRequired(el)) return false;
        var re = /^[0-9\+\-\s]{7,15}$/;
        if (!re.test(el.value.trim())) {
            markError(el, "Număr de telefon invalid."); return false;
        }
        markOk(el); return true;
    }

    function validateNumberRange(el, min, max) {
        if (!validateRequired(el)) return false;
        var v = parseFloat(el.value);
        if (isNaN(v)) { markError(el, "Valoare numerică invalidă."); return false; }
        if (min !== null && v < min) { markError(el, "Valoare minimă: " + min + "."); return false; }
        if (max !== null && v > max) { markError(el, "Valoare maximă: " + max + "."); return false; }
        markOk(el); return true;
    }

    function validatePassword(el) {
        return validateMinLength(el, 6);
    }

    function validateDate(el) {
        if (!validateRequired(el)) return false;
        var d = new Date(el.value);
        if (isNaN(d.getTime())) { markError(el, "Dată invalidă."); return false; }
        markOk(el); return true;
    }

    function validateSelect(el) {
        if (!el.value || el.value === "") {
            markError(el, "Selectați o opțiune."); return false;
        }
        markOk(el); return true;
    }

    function validateCheckboxGroup(name, minChecked, container) {
        var boxes = container.querySelectorAll("input[name='" + name + "']:checked");
        return boxes.length >= minChecked;
    }

    /* ---- Formular 1: index5.html — formularService ---- */
    function validateServiceForm(form) {
        var ok = true;
        var numeClient = form.querySelector("[name='numeClient']");
        var parola     = form.querySelector("[name='parola']");
        var km         = form.querySelector("[name='km']");

        if (numeClient && !validateMinLength(numeClient, 3)) ok = false;
        if (parola     && !validatePassword(parola))         ok = false;
        if (km         && !validateNumberRange(km, 0, 500000)) ok = false;

        if (!ok) {
            var first = form.querySelector(".field-error");
            if (first) first.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return ok;
    }

    /* ---- Formular 2: edit5.html — formEditare ---- */
    function validateEditForm(form) {
        var ok = true;
        var numeModel = form.querySelector("[name='numeModel']");
        var pret      = form.querySelector("[name='pret']");
        var adminPass = form.querySelector("[name='adminPass']");

        if (numeModel && !validateMinLength(numeModel, 3)) ok = false;
        if (pret      && !validateNumberRange(pret, 100, 500000)) ok = false;
        if (adminPass && !validatePassword(adminPass)) ok = false;

        if (!ok) {
            var first = form.querySelector(".field-error");
            if (first) first.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return ok;
    }

    /* ---- Formular 3: adaugare.html — formAdaugare ---- */
    function validateAdaugareForm(form) {
        var ok = true;
        var numeVanzator = form.querySelector("[name='numeVanzator']");
        var email        = form.querySelector("[name='emailVanzator']");
        var telefon      = form.querySelector("[name='telefon']");
        var dataInmatr   = form.querySelector("[name='dataInmatriculare']");
        var kmInput      = form.querySelector("[name='kmAuto']");
        var pret         = form.querySelector("[name='pretAuto']");
        var marcaS       = form.querySelector("[name='marcaAuto']");
        var judetS       = form.querySelector("[name='judetAuto']");
        var parola       = form.querySelector("[name='parolaAdmin']");

        if (numeVanzator && !validateMinLength(numeVanzator, 3)) ok = false;
        if (email        && !validateEmail(email))               ok = false;
        if (telefon      && !validatePhone(telefon))             ok = false;
        if (dataInmatr   && !validateDate(dataInmatr))           ok = false;
        if (kmInput      && !validateNumberRange(kmInput, 0, 999999)) ok = false;
        if (pret         && !validateNumberRange(pret, 100, 1000000)) ok = false;
        if (marcaS       && !validateSelect(marcaS))             ok = false;
        if (judetS       && !validateSelect(judetS))             ok = false;
        if (parola       && !validatePassword(parola))           ok = false;

        if (!ok) {
            var first = form.querySelector(".field-error");
            if (first) first.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return ok;
    }

    /* ---- Live clearing on input ---- */
    function addLiveClear(form) {
        if (!form) return;
        var inputs = form.querySelectorAll("input, select, textarea");
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener("input", function () { clearMark(this); });
            inputs[i].addEventListener("change", function () { clearMark(this); });
        }
    }

    /* ---- Inițializare ---- */
    document.addEventListener("DOMContentLoaded", function () {
        var svcForm  = document.getElementById("formularService");
        var editForm = document.getElementById("formEditare");
        var addForm  = document.getElementById("formAdaugare");

        if (svcForm) {
            addLiveClear(svcForm);
            svcForm.addEventListener("submit", function (e) {
                if (!validateServiceForm(svcForm)) e.preventDefault();
            });
        }
        if (editForm) {
            addLiveClear(editForm);
            editForm.addEventListener("submit", function (e) {
                if (!validateEditForm(editForm)) e.preventDefault();
            });
        }
        if (addForm) {
            addLiveClear(addForm);
            addForm.addEventListener("submit", function (e) {
                if (!validateAdaugareForm(addForm)) e.preventDefault();
            });
        }
    });
})();
