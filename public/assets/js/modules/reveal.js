/**
 * EMPETIX — Scroll Reveal, Stagger Engine & Micro-Interactions Module
 * File: assets/js/modules/reveal.js
 */

(function (global) {
  "use strict";

  function initReveal() {
    var reduced =
      window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    // Auto-assign stagger delays to children in grids
    var staggerGroups = Array.prototype.slice.call(
      document.querySelectorAll(".work-grid, .post-grid, .people, .contact-channels, .offices, .principles, .steps, .stats")
    );
    staggerGroups.forEach(function (group) {
      var children = Array.prototype.slice.call(group.children);
      children.forEach(function (child, idx) {
        if (!child.classList.contains("reveal")) {
          child.classList.add("reveal");
        }
        var delaySlot = (idx % 4) + 1;
        if (delaySlot > 1) {
          child.classList.add("reveal--d" + (delaySlot - 1));
        }
      });
    });

    // Also animate section headers
    var sectionHeads = Array.prototype.slice.call(document.querySelectorAll(".section-head"));
    sectionHeads.forEach(function (sh) {
      if (!sh.classList.contains("reveal")) {
        sh.classList.add("reveal");
      }
    });

    var items = Array.prototype.slice.call(document.querySelectorAll(".reveal"));
    if (items.length === 0) {
      return;
    }

    if (reduced || !("IntersectionObserver" in window)) {
      items.forEach(function (el) {
        el.classList.add("is-visible");
      });
      return;
    }

    var obs = new IntersectionObserver(
      function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      {
        rootMargin: "0px 0px -6% 0px",
        threshold: 0.08
      }
    );

    items.forEach(function (el) {
      obs.observe(el);
    });

    // One-click email copy buttons on Contact Us page
    var copyEmailBtns = Array.prototype.slice.call(
      document.querySelectorAll("[data-copy-email]")
    );
    copyEmailBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        var email = btn.getAttribute("data-copy-email");
        var orig = btn.textContent;
        if (navigator.clipboard && navigator.clipboard.writeText && email) {
          navigator.clipboard.writeText(email).catch(function () {});
        }
        btn.textContent = "Copied!";
        window.setTimeout(function () {
          btn.textContent = orig;
        }, 1800);
      });
    });
  }

  global.EmpetixApp = global.EmpetixApp || {};
  global.EmpetixApp.initReveal = initReveal;
})(window);
