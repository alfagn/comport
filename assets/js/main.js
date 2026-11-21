/**
 * EMPETIX — Main Application Entry Point
 * File: assets/js/main.js
 *
 * Works seamlessly on both file:// (double-click) and HTTP / Laravel Blade servers.
 */

(function (global) {
  "use strict";

  document.documentElement.classList.add("js");

  var boot = function () {
    var app = global.EmpetixApp || {};

    if (typeof app.initNav === "function") {
      app.initNav();
    }
    if (typeof app.initCounters === "function") {
      app.initCounters();
    }
    if (typeof app.initMedia === "function") {
      app.initMedia();
    }
    if (typeof app.initCareers === "function") {
      app.initCareers();
    }

    if (typeof app.initPreloader === "function") {
      app.initPreloader(function () {
        if (typeof app.initReveal === "function") {
          app.initReveal();
        }
      });
    } else if (typeof app.initReveal === "function") {
      app.initReveal();
    }
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot, { once: true });
  } else {
    boot();
  }
})(window);
