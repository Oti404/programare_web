/* ================================================
   LISTS.JS — Liste Colapsabile (jQuery)
   ================================================ */

$(function () {
    // 1. Pregătire structură: Identificăm <li> care au subliste
    $(".collapsible-list li").has("ul, ol").each(function() {
        var $li = $(this);
        $li.addClass("has-children");
        
        // Luăm doar textul direct al li-ului (fără subliste) și îl punem într-un label
        // Acest lucru ne permite să dăm click pe text fără să declanșăm click-ul sublistei
        var $sublist = $li.children("ul, ol").hide(); // Ascundem sublistele inițial (Cerința 5)
        
        // Extragem textul nodului părinte
        var originalNodes = $li.contents().filter(function() {
            return this.nodeType === 3; // Text nodes
        });
        
        if (originalNodes.length > 0) {
            var $label = $('<span class="collapsible-label"></span>');
            originalNodes.wrapAll($label);
        }
    });

    // 2. Logica de Toggle
    $(".collapsible-list").on("click", ".collapsible-label", function(e) {
        e.stopPropagation();
        var $parentLi = $(this).parent("li.has-children");
        
        $parentLi.toggleClass("expanded");
        $parentLi.children("ul, ol").slideToggle(250);
    });
    
    // Asigurăm că restul li-urilor care nu au copii au un cursor normal
    $(".collapsible-list li:not(.has-children)").css("cursor", "default");
});
