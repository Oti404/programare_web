/* ================================================
   CAROUSEL.JS — Simplificat
   ================================================ */

$(function () {
    if (typeof CAROUSEL_SLIDES === "undefined") return;

    var index = 0;
    var $track = $("#carouselTrack");

    // Construire slide-uri
    $.each(CAROUSEL_SLIDES, function (i, s) {
        var $s = $('<a href="' + s.link + '" class="carousel-slide"></a>').css({
            "background-image": "url(" + s.bg + ")",
            "background-size": "cover"
        }).html('<div class="carousel-content"><h2>' + s.text + '</h2><p>' + s.sub + '</p></div>');
        $track.append($s);
        $("#carouselDots").append('<button class="carousel-dot"></button>');
    });

    function showSlide(n) {
        index = (n + CAROUSEL_SLIDES.length) % CAROUSEL_SLIDES.length;
        $track.css("transform", "translateX(-" + (index * 100) + "%)");
        $(".carousel-dot").removeClass("active").eq(index).addClass("active");
    }

    // Butoane și Dots
    $("#carouselNext").click(function() { showSlide(index + 1); });
    $("#carouselPrev").click(function() { showSlide(index - 1); });
    $(".carousel-dot").click(function() { showSlide($(this).index()); });

    // Auto-play simplu
    var interval = setInterval(function() { showSlide(index + 1); }, 3000);
    $("#carouselContainer").hover(
        function() { clearInterval(interval); },
        function() { interval = setInterval(function() { showSlide(index + 1); }, 3000); }
    );

    showSlide(0);
});
