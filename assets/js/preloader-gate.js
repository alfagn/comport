/**
 * EMPETIX SOLUTION — Preloader Session Gate & Brand Logo Click Replay
 * File: assets/js/preloader-gate.js
 *
 * - Plays the logo assembly on first session load, on page reload (F5),
 *   AND whenever the user clicks the Empetix Solution brand logo (.brand)!
 */

(function () {
  "use strict";

  var STORAGE_KEY = "empetix_preloader_seen";
  var FORCE_KEY = "empetix_preloader_force";
  var root = document.documentElement;
  root.classList.add("js");

  var isReload = false;
  try {
    if (window.performance && typeof window.performance.getEntriesByType === "function") {
      var navs = window.performance.getEntriesByType("navigation");
      if (navs && navs.length > 0 && navs[0].type === "reload") {
        isReload = true;
      }
    }
  } catch (e) {}

  var seen = false;
  var forced = false;
  try {
    seen = window.sessionStorage.getItem(STORAGE_KEY) === "1";
    forced = window.sessionStorage.getItem(FORCE_KEY) === "1";
    if (forced || isReload) {
      window.sessionStorage.removeItem(FORCE_KEY);
      window.sessionStorage.removeItem(STORAGE_KEY);
      seen = false;
    }
  } catch (e) {
    seen = false;
  }

  if (seen) {
    root.classList.add("preloader-skip", "is-ready");
  } else {
    root.classList.add("is-loading");
  }

  // Replay preloader whenever the user clicks the Empetix Solution logo (.brand)
  window.addEventListener("DOMContentLoaded", function () {
    var brandLinks = Array.prototype.slice.call(document.querySelectorAll(".brand"));
    brandLinks.forEach(function (brand) {
      brand.addEventListener("click", function () {
        try {
          window.sessionStorage.removeItem(STORAGE_KEY);
          window.sessionStorage.setItem(FORCE_KEY, "1");
        } catch (e) {}
      });
    });

    // Fallback runner if ES module main.js is blocked on file:// protocol
    window.setTimeout(function () {
      if (window.__empetixPreloaderBooted) {
        return;
      }
      window.__empetixPreloaderBooted = true;

      var app = window.EmpetixApp || {};
      if (typeof app.initNav === "function") app.initNav();
      if (typeof app.initCounters === "function") app.initCounters();
      if (typeof app.initMedia === "function") app.initMedia();
      if (typeof app.initCareers === "function") app.initCareers();

      var preloader = document.getElementById("preloader");
      var counterEl = document.getElementById("preloader-counter");
      var srEl = document.getElementById("preloader-sr");

      var finishAll = function () {
        if (preloader) {
          preloader.hidden = true;
          preloader.setAttribute("aria-hidden", "true");
        }
        root.classList.remove("is-loading");
        root.classList.add("is-ready");
        if (typeof app.initReveal === "function") app.initReveal();
        window.dispatchEvent(new CustomEvent("preloader:complete"));
      };

      if (!preloader || root.classList.contains("preloader-skip")) {
        finishAll();
        return;
      }

      var reduced =
        typeof window.matchMedia === "function" &&
        window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      var minDur = reduced ? 600 : 1450;
      var maxDur = 3000;
      var exitDur = reduced ? 300 : 800;
      var tau = reduced ? 90 : 170;

      var fScore = 0.2;
      var iScore = 0.6;
      var lScore = document.readyState === "complete" ? 0.2 : 0;
      if (!lScore) {
        window.addEventListener("load", function () {
          lScore = 0.2;
        }, { once: true });
      }

      var t0 = performance.now();
      var prev = t0;
      var disp = 0;
      var milestone = 0;
      var exiting = false;

      var render = function (p) {
        var c = Math.max(0, Math.min(1, p));
        var pct = Math.round(c * 100);
        preloader.style.setProperty("--p", c.toFixed(4));
        if (counterEl) counterEl.textContent = String(pct);
        if (srEl) {
          if (pct >= 100 && milestone < 100) {
            milestone = 100;
            srEl.textContent = "Loaded";
          } else if (pct >= 75 && milestone < 75) {
            milestone = 75;
            srEl.textContent = "Loading 75%";
          } else if (pct >= 50 && milestone < 50) {
            milestone = 50;
            srEl.textContent = "Loading 50%";
          } else if (pct >= 25 && milestone < 25) {
            milestone = 25;
            srEl.textContent = "Loading 25%";
          }
        }
      };

      var step = function (now) {
        if (exiting) return;
        var elapsed = now - t0;
        var dt = Math.max(1, now - prev);
        prev = now;
        var real = Math.min(1, fScore + iScore + lScore);
        var cap = Math.min(1, elapsed / minDur);
        var forced = elapsed >= maxDur;
        var target = forced ? 1 : Math.min(real, cap);
        disp += (target - disp) * (1 - Math.exp(-dt / tau));

        if (forced || (target >= 1 && disp >= 0.992)) {
          exiting = true;
          render(1);
          try {
            window.sessionStorage.setItem(STORAGE_KEY, "1");
          } catch (e) {}
          preloader.classList.add("preloader--exiting");
          root.classList.remove("is-loading");
          root.classList.add("is-ready");
          if (typeof app.initReveal === "function") app.initReveal();
          window.setTimeout(function () {
            preloader.hidden = true;
            preloader.setAttribute("aria-hidden", "true");
            window.dispatchEvent(new CustomEvent("preloader:complete"));
          }, exitDur);
          return;
        }
        render(disp);
        window.requestAnimationFrame(step);
      };

      render(0);
      window.requestAnimationFrame(step);
    }, 40);
  });
})();
