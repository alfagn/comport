/**
 * EMPETIX TEAM DIRECTORY — Department Filter Tabs
 * File: assets/js/modules/team.js
 */

(function () {
  "use strict";

  function initTeamFilters() {
    var buttons = Array.prototype.slice.call(document.querySelectorAll("[data-team-filter]"));
    var groups = Array.prototype.slice.call(document.querySelectorAll("[data-team-group]"));

    if (buttons.length === 0 || groups.length === 0) {
      return;
    }

    buttons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        var target = btn.getAttribute("data-team-filter") || "all";

        buttons.forEach(function (b) {
          b.setAttribute("aria-pressed", b === btn ? "true" : "false");
        });

        groups.forEach(function (group) {
          var key = group.getAttribute("data-team-group");
          if (target === "all" || key === target) {
            group.removeAttribute("hidden");
          } else {
            group.setAttribute("hidden", "");
          }
        });
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initTeamFilters);
  } else {
    initTeamFilters();
  }
})();
