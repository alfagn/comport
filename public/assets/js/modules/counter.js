/**
 * EMPETIX — Tabular Number Counter on Scroll
 * File: assets/js/modules/counter.js
 */

(function (global) {
  "use strict";

  function initCounters() {
    var els = Array.prototype.slice.call(document.querySelectorAll("[data-counter]"));
    if (els.length === 0) {
      return;
    }

    var reduced =
      window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    var run = function (el) {
      var target = parseFloat(el.getAttribute("data-counter-target") || "0");
      var decimals = parseInt(el.getAttribute("data-counter-decimals") || "0", 10);
      var dur = 1400;

      if (reduced) {
        el.textContent = target.toFixed(decimals);
        return;
      }

      var start = performance.now();
      var step = function (now) {
        var p = Math.min(1, (now - start) / dur);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = (target * eased).toFixed(decimals);
        if (p < 1) {
          window.requestAnimationFrame(step);
        } else {
          el.textContent = target.toFixed(decimals);
        }
      };
      window.requestAnimationFrame(step);
    };

    if (!("IntersectionObserver" in window)) {
      els.forEach(run);
      return;
    }

    var obs = new IntersectionObserver(
      function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            run(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.35 }
    );

    els.forEach(function (el) {
      obs.observe(el);
    });
  }

  global.EmpetixApp = global.EmpetixApp || {};
  global.EmpetixApp.initCounters = initCounters;
})(window);
