/**
 * EMPETIX SOLUTION — Main Application Entry Point (ES Module)
 * File: assets/js/main.js
 */

import { initPreloader } from "./modules/preloader.js";

const boot = () => {
  const app = window.EmpetixApp || {};

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

  initPreloader(() => {
    if (typeof app.initReveal === "function") {
      app.initReveal();
    }
  });
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", boot, { once: true });
} else {
  boot();
}
