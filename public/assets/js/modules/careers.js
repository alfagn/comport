/**
 * EMPETIX CAREERS PORTAL (jobs.empetix.com) — Search, Filters & Apply Flow
 * File: assets/js/modules/careers.js
 */

(function (global) {
  "use strict";

  function initCareers() {
    // 1. Live Multi-Filter & Keyword Search on careers.html (jobs.empetix.com)
    var searchInput = document.getElementById("jobs-search-input");
    var deptSelect = document.getElementById("jobs-dept-select");
    var locSelect = document.getElementById("jobs-loc-select");
    var modeSelect = document.getElementById("jobs-mode-select");
    var resetBtn = document.getElementById("jobs-reset-btn");
    var jobCards = Array.prototype.slice.call(document.querySelectorAll("[data-job-card]"));
    var countEl = document.getElementById("jobs-results-count");
    var emptyEl = document.getElementById("jobs-empty-state");
    var trackCards = Array.prototype.slice.call(document.querySelectorAll("[data-track-filter]"));

    var applyJobFilters = function () {
      if (jobCards.length === 0) {
        return;
      }
      var q = searchInput ? searchInput.value.trim().toLowerCase() : "";
      var dept = deptSelect ? deptSelect.value : "all";
      var loc = locSelect ? locSelect.value : "all";
      var mode = modeSelect ? modeSelect.value : "all";

      var shown = 0;
      jobCards.forEach(function (card) {
        var cDept = card.getAttribute("data-job-dept") || "";
        var cLoc = card.getAttribute("data-job-loc") || "";
        var cMode = card.getAttribute("data-job-mode") || "";
        var cText = (card.textContent || "").toLowerCase();

        var matchQ = !q || cText.indexOf(q) !== -1;
        var matchDept = dept === "all" || cDept.indexOf(dept) !== -1;
        var matchLoc = loc === "all" || cLoc.indexOf(loc) !== -1;
        var matchMode = mode === "all" || cMode.indexOf(mode) !== -1;

        if (matchQ && matchDept && matchLoc && matchMode) {
          card.removeAttribute("hidden");
          shown++;
        } else {
          card.setAttribute("hidden", "");
        }
      });

      if (countEl) {
        countEl.textContent =
          "Showing " + shown + " of " + jobCards.length + " open positions on jobs.empetix.com";
      }
      if (emptyEl) {
        if (shown === 0) {
          emptyEl.removeAttribute("hidden");
        } else {
          emptyEl.setAttribute("hidden", "");
        }
      }

      trackCards.forEach(function (tc) {
        var tVal = tc.getAttribute("data-track-filter");
        tc.classList.toggle("is-active", tVal === dept);
      });
    };

    if (searchInput) {
      searchInput.addEventListener("input", applyJobFilters);
    }
    if (deptSelect) {
      deptSelect.addEventListener("change", applyJobFilters);
    }
    if (locSelect) {
      locSelect.addEventListener("change", applyJobFilters);
    }
    if (modeSelect) {
      modeSelect.addEventListener("change", applyJobFilters);
    }
    if (resetBtn) {
      resetBtn.addEventListener("click", function () {
        if (searchInput) searchInput.value = "";
        if (deptSelect) deptSelect.value = "all";
        if (locSelect) locSelect.value = "all";
        if (modeSelect) modeSelect.value = "all";
        applyJobFilters();
      });
    }

    // Track Cards click -> filter jobs section and scroll smoothly
    trackCards.forEach(function (tc) {
      tc.addEventListener("click", function () {
        var targetDept = tc.getAttribute("data-track-filter") || "all";
        if (deptSelect) {
          deptSelect.value = deptSelect.value === targetDept ? "all" : targetDept;
          applyJobFilters();
        }
        var jobsSection = document.getElementById("open-roles");
        if (jobsSection) {
          jobsSection.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    });

    // 2. Individual Job Detail Page: "Click Apply -> Reveal Application Form"
    var applyTriggers = Array.prototype.slice.call(
      document.querySelectorAll("[data-trigger-apply]")
    );
    var applyDrawer = document.getElementById("job-apply-drawer");
    var firstInput = document.getElementById("app-name");

    if (applyDrawer && applyTriggers.length > 0) {
      var openApplyForm = function (e) {
        if (e) {
          e.preventDefault();
        }
        applyDrawer.classList.add("is-open");
        applyDrawer.removeAttribute("hidden");
        applyDrawer.scrollIntoView({ behavior: "smooth", block: "start" });
        window.setTimeout(function () {
          if (firstInput) {
            firstInput.focus();
          }
        }, 450);
      };

      applyTriggers.forEach(function (btn) {
        btn.addEventListener("click", openApplyForm);
      });

      // Also open automatically if URL hash is #apply
      if (window.location.hash === "#apply" || window.location.hash === "#job-apply-drawer") {
        openApplyForm();
      }
    }

    // 3. Interactive CV / Resume File Picker Preview
    var fileInput = document.getElementById("app-cv-file");
    var dropzone = document.getElementById("cv-dropzone");
    var fileLabel = document.getElementById("cv-file-label");

    if (fileInput && fileLabel) {
      fileInput.addEventListener("change", function () {
        if (fileInput.files && fileInput.files.length > 0) {
          var f = fileInput.files[0];
          var sizeKB = Math.round(f.size / 1024);
          fileLabel.textContent = "Attached: " + f.name + " (" + sizeKB + " KB) \u2713";
          if (dropzone) {
            dropzone.classList.add("has-file");
          }
        }
      });
    }

  }

  global.EmpetixApp = global.EmpetixApp || {};
  global.EmpetixApp.initCareers = initCareers;
})(window);
