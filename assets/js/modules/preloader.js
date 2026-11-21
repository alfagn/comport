/**
 * EMPETIX SOLUTION — Brand-Led Logo Fill Preloader (ES Module)
 * File: assets/js/modules/preloader.js
 *
 * Real weighted progress:
 * - 20% Web fonts (document.fonts.ready)
 * - 60% Above-the-fold images (excluding loading="lazy")
 * - 20% Window load event
 * Time-eased progress (--p from 0 to 1), min 1.2s (600ms reduced motion), max 3.0s.
 */

const STORAGE_KEY = "empetix_preloader_seen";
const WEIGHT_FONTS = 0.2;
const WEIGHT_IMAGES = 0.6;
const WEIGHT_LOAD = 0.2;

export function initPreloader(onReady) {
  window.__empetixPreloaderBooted = true;

  const root = document.documentElement;
  const preloader = document.getElementById("preloader");
  const counterEl = document.getElementById("preloader-counter");
  const srEl = document.getElementById("preloader-sr");

  const completeImmediately = () => {
    if (preloader) {
      preloader.hidden = true;
      preloader.setAttribute("aria-hidden", "true");
    }
    root.classList.remove("is-loading");
    root.classList.add("is-ready");
    if (typeof onReady === "function") {
      onReady();
    }
    window.dispatchEvent(new CustomEvent("preloader:complete"));
  };

  if (!preloader || root.classList.contains("preloader-skip")) {
    completeImmediately();
    return;
  }

  let seen = false;
  try {
    seen = window.sessionStorage.getItem(STORAGE_KEY) === "1";
  } catch {
    seen = false;
  }

  if (seen) {
    root.classList.add("preloader-skip");
    completeImmediately();
    return;
  }

  root.classList.add("is-loading");

  const reducedMotion =
    typeof window.matchMedia === "function" &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const minDuration = reducedMotion ? 600 : 1200;
  const maxDuration = 3000;
  const exitDuration = reducedMotion ? 300 : 800;
  const smoothingTau = reducedMotion ? 90 : 160;

  let fontsScore = 0;
  let imagesScore = 0;
  let loadScore = 0;

  // 1. Track Web Fonts (20%)
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready
      .then(() => {
        fontsScore = WEIGHT_FONTS;
      })
      .catch(() => {
        fontsScore = WEIGHT_FONTS;
      });
  } else {
    fontsScore = WEIGHT_FONTS;
  }

  // 2. Track Above-the-Fold Eager Images (60%)
  const allImgs = Array.from(document.querySelectorAll("img"));
  const eagerImgs = allImgs.filter((img) => {
    if (img.getAttribute("loading") === "lazy") {
      return false;
    }
    const rect = img.getBoundingClientRect();
    const vh = window.innerHeight || 900;
    return rect.top < vh * 1.25;
  });

  if (eagerImgs.length === 0) {
    imagesScore = WEIGHT_IMAGES;
  } else {
    let loadedImgs = 0;
    const totalImgs = eagerImgs.length;
    const onImgDone = () => {
      loadedImgs = Math.min(totalImgs, loadedImgs + 1);
      imagesScore = (loadedImgs / totalImgs) * WEIGHT_IMAGES;
    };

    eagerImgs.forEach((img) => {
      if (img.complete) {
        onImgDone();
      } else {
        img.addEventListener("load", onImgDone, { once: true });
        img.addEventListener("error", onImgDone, { once: true });
      }
    });
  }

  // 3. Track Window Load Event (20%)
  if (document.readyState === "complete") {
    loadScore = WEIGHT_LOAD;
  } else {
    window.addEventListener(
      "load",
      () => {
        loadScore = WEIGHT_LOAD;
      },
      { once: true }
    );
  }

  const startTime = performance.now();
  let lastFrameTime = startTime;
  let displayedProgress = 0;
  let lastAnnouncedMilestone = 0;
  let isExiting = false;

  const updateView = (p) => {
    const clamped = Math.max(0, Math.min(1, p));
    const percent = Math.round(clamped * 100);

    preloader.style.setProperty("--p", clamped.toFixed(4));

    if (counterEl) {
      counterEl.textContent = String(percent);
    }

    // Update screen reader status ONLY at 0, 25, 50, 75, and Loaded
    if (srEl) {
      if (percent >= 100 && lastAnnouncedMilestone < 100) {
        lastAnnouncedMilestone = 100;
        srEl.textContent = "Loaded";
      } else if (percent >= 75 && lastAnnouncedMilestone < 75) {
        lastAnnouncedMilestone = 75;
        srEl.textContent = "Loading 75%";
      } else if (percent >= 50 && lastAnnouncedMilestone < 50) {
        lastAnnouncedMilestone = 50;
        srEl.textContent = "Loading 50%";
      } else if (percent >= 25 && lastAnnouncedMilestone < 25) {
        lastAnnouncedMilestone = 25;
        srEl.textContent = "Loading 25%";
      }
    }
  };

  const triggerExit = () => {
    if (isExiting) {
      return;
    }
    isExiting = true;
    updateView(1);

    try {
      window.sessionStorage.setItem(STORAGE_KEY, "1");
    } catch {
      // Ignore storage errors in private browsing
    }

    // Start curtain wipe & trigger hero entrance immediately
    preloader.classList.add("preloader--exiting");
    root.classList.remove("is-loading");
    root.classList.add("is-ready");

    if (typeof onReady === "function") {
      onReady();
    }

    window.setTimeout(() => {
      preloader.hidden = true;
      preloader.setAttribute("aria-hidden", "true");
      window.dispatchEvent(new CustomEvent("preloader:complete"));
    }, exitDuration);
  };

  const tick = (now) => {
    if (isExiting) {
      return;
    }

    const elapsed = now - startTime;
    const dt = Math.max(1, now - lastFrameTime);
    lastFrameTime = now;

    const realProgress = Math.min(1, fontsScore + imagesScore + loadScore);
    const timeCap = Math.min(1, elapsed / minDuration);
    const forceComplete = elapsed >= maxDuration;

    // Displayed progress never runs ahead of elapsed minimum time
    const target = forceComplete ? 1 : Math.min(realProgress, timeCap);

    // Time-based exponential smoothing (frame-rate independent)
    const alpha = 1 - Math.exp(-dt / smoothingTau);
    displayedProgress += (target - displayedProgress) * alpha;

    if (forceComplete || (target >= 1 && displayedProgress >= 0.992)) {
      triggerExit();
      return;
    }

    updateView(displayedProgress);
    window.requestAnimationFrame(tick);
  };

  updateView(0);
  window.requestAnimationFrame(tick);
}

// Attach to global namespace as well for non-module fallback
window.EmpetixApp = window.EmpetixApp || {};
window.EmpetixApp.initPreloader = initPreloader;
