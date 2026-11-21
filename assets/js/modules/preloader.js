/**
 * EMPETIX — Brand Preloader Module
 * File: assets/js/modules/preloader.js
 *
 * - Runs once per session (sessionStorage)
 * - Tied to real document/image/font load with ~1.2s min and ~2.6s max
 * - Smooth curtain wipe exit + hero intro trigger
 * - Works on both file:// and http:// without CORS issues
 */

(function (global) {
  "use strict";

  var SESSION_KEY = "empetix_intro_seen_v2";
  var MIN_MS = 1200;
  var MAX_MS = 2600;

  function initPreloader(onReady) {
    var root = document.documentElement;
    var body = document.body;
    var el = document.getElementById("preloader");
    var numEl = document.getElementById("preloader-num");
    var barEl = document.getElementById("preloader-bar");
    var statusEl = document.getElementById("preloader-status");

    var finishInstant = function () {
      if (el) {
        el.classList.add("is-done");
        el.setAttribute("aria-hidden", "true");
      }
      root.classList.remove("is-loading");
      root.classList.add("intro-seen", "is-ready");
      body.classList.remove("is-locked");
      if (typeof onReady === "function") {
        onReady();
      }
    };

    if (!el) {
      finishInstant();
      return;
    }

    var seen = false;
    try {
      seen = window.sessionStorage.getItem(SESSION_KEY) === "1";
    } catch (e) {
      seen = false;
    }

    if (seen) {
      finishInstant();
      return;
    }

    root.classList.add("is-loading");
    body.classList.add("is-locked");

    var reducedMotion =
      window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    var start = performance.now();
    var imgs = Array.prototype.slice.call(document.images || []);
    var total = imgs.length + 2;
    var loaded = 0;
    var assetRatio = 0;
    var shown = 0;
    var done = false;

    var bump = function () {
      loaded = Math.min(total, loaded + 1);
      assetRatio = loaded / total;
    };

    if (document.readyState === "interactive" || document.readyState === "complete") {
      bump();
    } else {
      document.addEventListener("DOMContentLoaded", bump, { once: true });
    }

    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(bump).catch(bump);
    } else {
      bump();
    }

    if (imgs.length === 0) {
      assetRatio = 1;
    } else {
      imgs.forEach(function (img) {
        if (img.complete) {
          bump();
        } else {
          img.addEventListener("load", bump, { once: true });
          img.addEventListener("error", bump, { once: true });
        }
      });
    }

    var render = function (val) {
      var pct = Math.max(0, Math.min(100, Math.round(val)));
      if (numEl) {
        numEl.textContent = String(pct).padStart(2, "0");
      }
      if (barEl) {
        barEl.style.transform = "scaleX(" + pct / 100 + ")";
      }
      if (statusEl) {
        if (pct < 35) {
          statusEl.textContent = "Loading Studio Systems";
        } else if (pct < 80) {
          statusEl.textContent = "Preparing Case Studies & Media";
        } else if (pct < 100) {
          statusEl.textContent = "Calibrating Layout Grid";
        } else {
          statusEl.textContent = "Empetix Ready";
        }
      }
    };

    var complete = function () {
      if (done) {
        return;
      }
      done = true;
      render(100);

      try {
        window.sessionStorage.setItem(SESSION_KEY, "1");
      } catch (e) {
        // Ignore private mode restrictions
      }

      var holdMs = reducedMotion ? 40 : 160;
      var wipeMs = reducedMotion ? 180 : 760;

      window.setTimeout(function () {
        el.classList.add("is-exiting");
        el.setAttribute("aria-hidden", "true");
        root.classList.remove("is-loading");
        root.classList.add("is-ready");
        body.classList.remove("is-locked");

        if (typeof onReady === "function") {
          onReady();
        }

        window.setTimeout(function () {
          el.classList.add("is-done");
          root.classList.add("intro-seen");
        }, wipeMs);
      }, holdMs);
    };

    var frame = function (now) {
      if (done) {
        return;
      }
      var elapsed = now - start;
      var minRatio = Math.min(1, elapsed / MIN_MS);
      var forced = elapsed >= MAX_MS;
      var target = forced ? 100 : Math.min(minRatio, Math.max(minRatio * 0.88, assetRatio)) * 100;

      shown += (target - shown) * 0.32;

      if (target >= 99.4 || forced) {
        complete();
        return;
      }

      render(shown);
      window.requestAnimationFrame(frame);
    };

    window.requestAnimationFrame(frame);
  }

  global.EmpetixApp = global.EmpetixApp || {};
  global.EmpetixApp.initPreloader = initPreloader;
})(window);
