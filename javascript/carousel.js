/* ================================================
   CAROUSEL.JS — Carusel auto-play 3s cu navigare manuală
   Date preluate din data.js (CAROUSEL_SLIDES)
   Fără librării externe
   ================================================ */

(function () {
    "use strict";

    var current = 0;
    var total   = 0;
    var timer   = null;
    var track, dotsContainer;

    function buildSlides(container) {
        total = CAROUSEL_SLIDES.length;
        track = document.getElementById("carouselTrack");
        dotsContainer = document.getElementById("carouselDots");

        /* Generare slide-uri */
        for (var i = 0; i < total; i++) {
            var s = CAROUSEL_SLIDES[i];
            var slide = document.createElement("a");
            slide.href = s.link;
            slide.className = "carousel-slide";
            /* Imagine de fundal reală + overlay întunecat deasupra */
            slide.style.backgroundImage = "linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)), url(" + s.bg + ")";
            slide.style.backgroundSize = "cover";
            slide.style.backgroundPosition = "center";
            slide.style.backgroundColor = s.color || "#1a237e"; /* fallback dacă imaginea nu se încarcă */
            slide.innerHTML =
                '<div class="carousel-content">' +
                    '<span class="carousel-icon">' + s.icon + '</span>' +
                    '<h2 class="carousel-title">' + s.text + '</h2>' +
                    '<p class="carousel-sub">' + s.sub + '</p>' +
                '</div>';
            track.appendChild(slide);

            /* Dot indicator */
            var dot = document.createElement("button");
            dot.className = "carousel-dot";
            dot.setAttribute("aria-label", "Slide " + (i + 1));
            dot.setAttribute("data-index", i);
            dot.addEventListener("click", function () {
                stopAuto();
                goTo(parseInt(this.getAttribute("data-index")));
                startAuto();
            });
            dotsContainer.appendChild(dot);
        }

        /* Butoane prev/next */
        var prevBtn = document.getElementById("carouselPrev");
        var nextBtn = document.getElementById("carouselNext");
        if (prevBtn) prevBtn.addEventListener("click", function () {
            stopAuto(); prev(); startAuto();
        });
        if (nextBtn) nextBtn.addEventListener("click", function () {
            stopAuto(); next(); startAuto();
        });

        goTo(0);
        startAuto();
    }

    function goTo(index) {
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;
        current = index;

        /* Mișcă track-ul */
        track.style.transform = "translateX(-" + (current * 100) + "%)";

        /* Actualizează dots */
        var dots = dotsContainer.querySelectorAll(".carousel-dot");
        for (var i = 0; i < dots.length; i++) {
            dots[i].classList.toggle("active", i === current);
        }
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function startAuto() {
        timer = setInterval(next, 3000);
    }

    function stopAuto() {
        clearInterval(timer);
        timer = null;
    }

    document.addEventListener("DOMContentLoaded", function () {
        var container = document.getElementById("carouselContainer");
        if (container && typeof CAROUSEL_SLIDES !== "undefined") {
            buildSlides(container);
        }
    });
})();
