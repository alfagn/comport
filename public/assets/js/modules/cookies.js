/**
 * EMPETIX SOLUTION — Cookie Consent & Granular Privacy Preferences
 * File: assets/js/modules/cookies.js
 *
 * Asks new visitors whether they allow optional cookies on their device.
 * - Accept All / Custom Optional: writes `empetix_cookie_consent` cookie (1 year) + localStorage.
 * - Essential Only (Decline): refuses optional cookies, deletes `empetix_cookie_consent`,
 *   and actively expires known analytics/marketing cookies (_ga, _gid, _fbp, etc.).
 */
(function () {
  "use strict";

  const STORAGE_KEY = "empetix_cookie_prefs_v1";
  const COOKIE_NAME = "empetix_cookie_consent";
  const OPTIONAL_TRACKING_COOKIES = [
    "_ga",
    "_gid",
    "_gat",
    "_fbp",
    "_fbc",
    "_gcl_au",
    "_ttp",
  ];

  function readCookie(name) {
    const prefix = name + "=";
    const parts = document.cookie ? document.cookie.split("; ") : [];
    for (let i = 0; i < parts.length; i++) {
      if (parts[i].indexOf(prefix) === 0) {
        return decodeURIComponent(parts[i].slice(prefix.length));
      }
    }
    return null;
  }

  function writeConsentCookie(prefs) {
    const payload = encodeURIComponent(
      JSON.stringify({
        necessary: true,
        analytics: Boolean(prefs.analytics),
        marketing: Boolean(prefs.marketing),
        t: prefs.updatedAt,
      })
    );
    const secure = window.location.protocol === "https:" ? "; Secure" : "";
    document.cookie =
      COOKIE_NAME +
      "=" +
      payload +
      "; Max-Age=31536000; Path=/; SameSite=Lax" +
      secure;
  }

  function expireCookieEverywhere(name) {
    const host = window.location.hostname;
    const domains = [null, host];
    if (host.indexOf(".") !== -1) {
      domains.push("." + host);
      const root = host.split(".").slice(-2).join(".");
      domains.push("." + root);
    }
    domains.forEach(function (domain) {
      const domainAttr = domain ? "; Domain=" + domain : "";
      document.cookie =
        name + "=; Max-Age=0; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/" + domainAttr;
    });
  }

  function purgeUnconsentedCookies(prefs) {
    if (!prefs.analytics && !prefs.marketing) {
      expireCookieEverywhere(COOKIE_NAME);
    }
    if (!prefs.analytics || !prefs.marketing) {
      OPTIONAL_TRACKING_COOKIES.forEach(expireCookieEverywhere);
      // Also clear dynamic _ga_* measurement cookies when analytics is declined
      if (!prefs.analytics && document.cookie) {
        document.cookie.split("; ").forEach(function (entry) {
          const eq = entry.indexOf("=");
          const key = eq > -1 ? entry.slice(0, eq) : entry;
          if (key.indexOf("_ga_") === 0 || key.indexOf("_gat_") === 0) {
            expireCookieEverywhere(key);
          }
        });
      }
    }
  }

  function loadSavedPreferences() {
    try {
      const raw = window.localStorage.getItem(STORAGE_KEY);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === "object") {
          return {
            necessary: true,
            analytics: Boolean(parsed.analytics),
            marketing: Boolean(parsed.marketing),
            updatedAt: parsed.updatedAt || null,
          };
        }
      }
    } catch (_) {
      // Ignore storage access restrictions (e.g. strict private mode)
    }

    const fromCookie = readCookie(COOKIE_NAME);
    if (fromCookie) {
      try {
        const parsed = JSON.parse(fromCookie);
        return {
          necessary: true,
          analytics: Boolean(parsed.analytics),
          marketing: Boolean(parsed.marketing),
          updatedAt: parsed.t || null,
        };
      } catch (_) {
        return null;
      }
    }

    return null;
  }

  function applyPreferencesToDocument(prefs) {
    const root = document.documentElement;
    root.dataset.cookieConsent = prefs
      ? prefs.analytics || prefs.marketing
        ? "accepted"
        : "essential"
      : "pending";
    root.dataset.cookieAnalytics = prefs && prefs.analytics ? "1" : "0";
    root.dataset.cookieMarketing = prefs && prefs.marketing ? "1" : "0";
  }

  function persistPreferences(analytics, marketing) {
    const prefs = {
      necessary: true,
      analytics: Boolean(analytics),
      marketing: Boolean(marketing),
      updatedAt: new Date().toISOString(),
    };

    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    } catch (_) {
      // Continue gracefully if localStorage is unavailable
    }

    if (prefs.analytics || prefs.marketing) {
      writeConsentCookie(prefs);
    }
    purgeUnconsentedCookies(prefs);
    applyPreferencesToDocument(prefs);

    window.dispatchEvent(
      new CustomEvent("empetix:cookie-consent", { detail: prefs })
    );

    return prefs;
  }

  function initCookies() {
    const banner = document.getElementById("cookie-consent-banner");
    const modal = document.getElementById("cookie-settings-modal");
    if (!banner && !modal) return;

    const analyticsInput = modal
      ? modal.querySelector('[data-cookie-cat="analytics"]')
      : null;
    const marketingInput = modal
      ? modal.querySelector('[data-cookie-cat="marketing"]')
      : null;
    const statusBadge = modal
      ? modal.querySelector("[data-cookie-current-status]")
      : null;

    let lastFocusedElement = null;

    function syncModalUI(prefs) {
      if (analyticsInput) {
        analyticsInput.checked = Boolean(prefs && prefs.analytics);
      }
      if (marketingInput) {
        marketingInput.checked = Boolean(prefs && prefs.marketing);
      }
      if (statusBadge) {
        if (!prefs) {
          statusBadge.textContent = "Awaiting Choice";
          statusBadge.removeAttribute("data-state");
        } else if (prefs.analytics && prefs.marketing) {
          statusBadge.textContent = "All Cookies Allowed";
          statusBadge.setAttribute("data-state", "accepted");
        } else if (prefs.analytics || prefs.marketing) {
          statusBadge.textContent = "Custom Preferences";
          statusBadge.setAttribute("data-state", "accepted");
        } else {
          statusBadge.textContent = "Essential Only";
          statusBadge.setAttribute("data-state", "essential");
        }
      }
    }

    function hideBanner() {
      if (banner) banner.hidden = true;
    }

    function openModal(triggerEl) {
      if (!modal) return;
      lastFocusedElement = triggerEl || document.activeElement;
      syncModalUI(loadSavedPreferences());
      modal.hidden = false;
      document.body.style.overflow = "hidden";
      const closeBtn = modal.querySelector("[data-cookie-close].cookie-modal__close");
      if (closeBtn) closeBtn.focus();
    }

    function closeModal() {
      if (!modal || modal.hidden) return;
      modal.hidden = true;
      document.body.style.overflow = "";
      if (lastFocusedElement && typeof lastFocusedElement.focus === "function") {
        lastFocusedElement.focus();
      }
    }

    function handleDecision(analytics, marketing) {
      const saved = persistPreferences(analytics, marketing);
      syncModalUI(saved);
      hideBanner();
      closeModal();
    }

    // Bind global click delegates for banner, modal, and footer trigger
    document.addEventListener("click", function (e) {
      const openBtn = e.target.closest("[data-cookie-settings]");
      if (openBtn) {
        e.preventDefault();
        openModal(openBtn);
        return;
      }

      const acceptBtn = e.target.closest("[data-cookie-accept-all]");
      if (acceptBtn) {
        e.preventDefault();
        handleDecision(true, true);
        return;
      }

      const rejectBtn = e.target.closest("[data-cookie-reject-optional]");
      if (rejectBtn) {
        e.preventDefault();
        handleDecision(false, false);
        return;
      }

      const saveBtn = e.target.closest("[data-cookie-save]");
      if (saveBtn) {
        e.preventDefault();
        handleDecision(
          analyticsInput ? analyticsInput.checked : false,
          marketingInput ? marketingInput.checked : false
        );
        return;
      }

      const closeBtn = e.target.closest("[data-cookie-close]");
      if (closeBtn) {
        e.preventDefault();
        closeModal();
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal && !modal.hidden) {
        closeModal();
      }
    });

    // Initial check on page load
    const existing = loadSavedPreferences();
    applyPreferencesToDocument(existing);
    if (existing) {
      purgeUnconsentedCookies(existing);
      hideBanner();
    } else if (banner && banner.dataset.cookieAutoPrompt === "1") {
      banner.hidden = false;
    }

    window.EmpetixCookies = {
      getPreferences: loadSavedPreferences,
      openSettings: openModal,
      acceptAll: function () {
        handleDecision(true, true);
      },
      rejectOptional: function () {
        handleDecision(false, false);
      },
    };
  }

  window.EmpetixApp = window.EmpetixApp || {};
  window.EmpetixApp.initCookies = initCookies;
})();
