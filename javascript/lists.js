/* ================================================
   LISTS.JS — Liste imbricate colapsabile
   Fără librării externe
   ================================================ */

(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        /* Selectează toate listele marcate cu clasa collapsible-list */
        var lists = document.querySelectorAll(".collapsible-list");

        lists.forEach(function (list) {
            /* Găsește elementele de prim nivel care au subliste */
            var topItems = list.children;
            for (var i = 0; i < topItems.length; i++) {
                var item = topItems[i];
                var subList = item.querySelector("ul, ol");
                if (subList) {
                    item.classList.add("has-children");
                    subList.classList.add("sub-list");

                    /* Creează un wrapper pentru textul item-ului (fără sublista) */
                    var label = document.createElement("span");
                    label.className = "collapsible-label";
                    /* Mutăm nodurile de text și inline elements în label */
                    var children = Array.prototype.slice.call(item.childNodes);
                    children.forEach(function (child) {
                        if (child !== subList) {
                            label.appendChild(child);
                        }
                    });
                    item.insertBefore(label, subList);

                    /* Click pe label → toggle */
                    label.addEventListener("click", function (e) {
                        e.stopPropagation();
                        var parentLi = this.parentNode;
                        parentLi.classList.toggle("expanded");
                    });
                }
            }
        });

        /* Subliste de nivel 2 (imbricate) */
        var subItems = document.querySelectorAll(".collapsible-list .sub-list > li");
        subItems.forEach(function (item) {
            var subSub = item.querySelector("ul, ol");
            if (subSub) {
                item.classList.add("has-children");
                subSub.classList.add("sub-list");

                var label2 = document.createElement("span");
                label2.className = "collapsible-label";
                var kids = Array.prototype.slice.call(item.childNodes);
                kids.forEach(function (child) {
                    if (child !== subSub) label2.appendChild(child);
                });
                item.insertBefore(label2, subSub);

                label2.addEventListener("click", function (e) {
                    e.stopPropagation();
                    item.classList.toggle("expanded");
                });
            }
        });
    });
})();
