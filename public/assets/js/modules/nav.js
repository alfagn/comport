/**
 * EMPETIX SOLUTIONS — Link Development-Style Hover Mega Menu, Sticky Header & Search
 * File: assets/js/modules/nav.js
 *
 * - Opens Mega Menu IMMEDIATELY on hover (mouseenter) over Capabilities, Solutions, or Blogs!
 * - Smoothly keeps Mega Menu open while hovering inside the panel, and closes when mouse leaves.
 * - Full keyboard (Arrow keys, Escape) and touch click support.
 */

(function (global) {
  "use strict";

  var DIR_KEY = "empetix_lang_dir";

  function initNav() {
    var head = document.getElementById("site-head");
    var mega = document.getElementById("mega-menu");
    var triggers = Array.prototype.slice.call(document.querySelectorAll("[data-mega-trigger]"));
    var panels = Array.prototype.slice.call(document.querySelectorAll("[data-mega-panel]"));

    var searchToggles = Array.prototype.slice.call(
      document.querySelectorAll("#search-toggle, [data-search-toggle]")
    );
    var lastSearchTrigger = searchToggles[0] || null;
    var searchPanel = document.getElementById("search-panel");
    var searchInput = document.getElementById("search-input");
    var searchClose = document.getElementById("search-close");

    var menuToggle = document.getElementById("menu-toggle");
    var drawer = document.getElementById("mobile-drawer");
    var drawerClose = document.getElementById("drawer-close");

    var langBtns = Array.prototype.slice.call(document.querySelectorAll("[data-lang-btn]"));

    // 1. Smart Hide-on-Scroll-Down / Show-on-Scroll-Up Navbar
    var isCompact = false;
    var ticking = false;
    var lastScrollY = window.scrollY || window.pageYOffset || 0;

    var updateHeader = function () {
      ticking = false;
      if (!head) {
        return;
      }
      var y = window.scrollY || window.pageYOffset || 0;
      var delta = y - lastScrollY;

      if (!isCompact && y > 48) {
        isCompact = true;
        head.classList.add("is-compact");
      } else if (isCompact && y < 12) {
        isCompact = false;
        head.classList.remove("is-compact");
      }

      // Hide navbar when scrolling down past 100px; show when scrolling up or near top
      if (y <= 80) {
        head.classList.remove("is-hidden");
      } else if (delta > 6 && y > 100) {
        if (!head.classList.contains("is-hidden")) {
          head.classList.add("is-hidden");
          if (mega && mega.classList.contains("is-open")) {
            closeMega(false);
          }
          if (searchPanel && searchPanel.classList.contains("is-open")) {
            closeSearch();
          }
        }
      } else if (delta < -8) {
        head.classList.remove("is-hidden");
      }

      lastScrollY = y < 0 ? 0 : y;
    };

    window.addEventListener(
      "scroll",
      function () {
        if (!ticking) {
          ticking = true;
          window.requestAnimationFrame(updateHeader);
        }
      },
      { passive: true }
    );
    updateHeader();

    // 2. Instant Hover + Click Mega Menu Engine
    var activeBtn = null;
    var closeTimer = null;

    var cancelCloseTimer = function () {
      if (closeTimer) {
        window.clearTimeout(closeTimer);
        closeTimer = null;
      }
    };

    var closeMega = function (returnFocus) {
      cancelCloseTimer();
      if (!mega) {
        return;
      }
      mega.classList.remove("is-open");
      if (head) {
        head.classList.remove("is-menu-open");
      }
      triggers.forEach(function (btn) {
        btn.setAttribute("aria-expanded", "false");
      });
      panels.forEach(function (panel) {
        panel.classList.remove("is-active");
      });
      if (returnFocus && activeBtn) {
        activeBtn.focus();
      }
      activeBtn = null;
    };

    var scheduleCloseMega = function () {
      cancelCloseTimer();
      closeTimer = window.setTimeout(function () {
        closeMega(false);
      }, 130);
    };

    var openMega = function (btn) {
      cancelCloseTimer();
      if (!mega) {
        return;
      }
      var target = btn.getAttribute("data-mega-trigger");

      if (searchPanel && searchPanel.classList.contains("is-open")) {
        searchPanel.classList.remove("is-open");
        searchToggles.forEach(function (t) {
          t.setAttribute("aria-expanded", "false");
        });
      }

      triggers.forEach(function (b) {
        var sameTarget = b.getAttribute("data-mega-trigger") === target;
        b.setAttribute("aria-expanded", sameTarget ? "true" : "false");
      });

      panels.forEach(function (p) {
        var match = p.getAttribute("data-mega-panel") === target;
        p.classList.toggle("is-active", match);
      });

      mega.classList.add("is-open");
      if (head) {
        head.classList.add("is-menu-open");
      }
      activeBtn = btn;
    };

    triggers.forEach(function (btn, idx) {
      // Instant open on mouse hover!
      btn.addEventListener("mouseenter", function () {
        openMega(btn);
      });

      btn.addEventListener("mouseleave", function () {
        scheduleCloseMega();
      });

      // Also open on keyboard focus
      btn.addEventListener("focus", function () {
        openMega(btn);
      });

      // Click behaviour: if it's a button, toggle; if it's an <a href="...">, let click navigate or open on touch
      btn.addEventListener("click", function (e) {
        var isAnchor = btn.tagName.toLowerCase() === "a" && btn.getAttribute("href");
        if (!isAnchor) {
          e.preventDefault();
          e.stopPropagation();
          if (mega && mega.classList.contains("is-open") && activeBtn === btn) {
            closeMega(false);
          } else {
            openMega(btn);
          }
        }
      });

      btn.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight") {
          e.preventDefault();
          triggers[(idx + 1) % triggers.length].focus();
        } else if (e.key === "ArrowLeft") {
          e.preventDefault();
          triggers[(idx - 1 + triggers.length) % triggers.length].focus();
        } else if (e.key === "ArrowDown") {
          openMega(btn);
          var activePanel = panels.find(function (p) {
            return p.classList.contains("is-active");
          });
          var firstLink = activePanel ? activePanel.querySelector("a") : null;
          if (firstLink) {
            e.preventDefault();
            firstLink.focus();
          }
        }
      });
    });

    if (mega) {
      mega.addEventListener("mouseenter", cancelCloseTimer);
      mega.addEventListener("mouseleave", scheduleCloseMega);
    }

    // Close mega menu when hovering over non-trigger nav links
    var plainNavLinks = Array.prototype.slice.call(
      document.querySelectorAll(".nav__link:not([data-mega-trigger])")
    );
    plainNavLinks.forEach(function (link) {
      link.addEventListener("mouseenter", function () {
        closeMega(false);
      });
    });

    document.addEventListener("click", function (e) {
      if (!mega || !mega.classList.contains("is-open")) {
        return;
      }
      var inside = mega.contains(e.target);
      var onTrigger = triggers.some(function (b) {
        return b.contains(e.target);
      });
      if (!inside && !onTrigger) {
        closeMega(false);
      }
    });

    // 3. Search Panel (Desktop Utility Toggle + Mobile Header Toggle)
    var setSearchExpanded = function (expanded) {
      searchToggles.forEach(function (btn) {
        btn.setAttribute("aria-expanded", expanded ? "true" : "false");
      });
    };

    var closeSearch = function () {
      if (!searchPanel) {
        return;
      }
      searchPanel.classList.remove("is-open");
      setSearchExpanded(false);
    };

    if (searchToggles.length > 0 && searchPanel) {
      searchToggles.forEach(function (btn) {
        btn.addEventListener("click", function () {
          lastSearchTrigger = btn;
          var open = searchPanel.classList.toggle("is-open");
          setSearchExpanded(open);
          if (open) {
            closeMega(false);
            if (head) {
              head.classList.add("is-menu-open");
            }
            if (searchInput) {
              searchInput.focus();
            }
          } else if (head) {
            head.classList.remove("is-menu-open");
          }
        });
      });
    }

    if (searchClose) {
      searchClose.addEventListener("click", function () {
        closeSearch();
        if (head) {
          head.classList.remove("is-menu-open");
        }
        if (lastSearchTrigger) {
          lastSearchTrigger.focus();
        }
      });
    }

    // 4. Mobile Drawer
    var closeDrawer = function () {
      if (!drawer) {
        return;
      }
      drawer.classList.remove("is-open");
      drawer.setAttribute("aria-hidden", "true");
      if (menuToggle) {
        menuToggle.setAttribute("aria-expanded", "false");
      }
      document.body.classList.remove("is-locked");
    };

    var openDrawer = function () {
      if (!drawer) {
        return;
      }
      closeMega(false);
      closeSearch();
      drawer.classList.add("is-open");
      drawer.setAttribute("aria-hidden", "false");
      if (menuToggle) {
        menuToggle.setAttribute("aria-expanded", "true");
      }
      document.body.classList.add("is-locked");
      if (drawerClose) {
        drawerClose.focus();
      }
    };

    if (menuToggle) {
      menuToggle.addEventListener("click", function () {
        if (drawer && drawer.classList.contains("is-open")) {
          closeDrawer();
        } else {
          openDrawer();
        }
      });
    }

    if (drawerClose) {
      drawerClose.addEventListener("click", function () {
        closeDrawer();
        if (menuToggle) {
          menuToggle.focus();
        }
      });
    }

    if (drawer) {
      Array.prototype.slice.call(drawer.querySelectorAll("a")).forEach(function (a) {
        a.addEventListener("click", closeDrawer);
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        if (mega && mega.classList.contains("is-open")) {
          closeMega(true);
        }
        if (searchPanel && searchPanel.classList.contains("is-open")) {
          closeSearch();
          if (lastSearchTrigger) {
            lastSearchTrigger.focus();
          }
        }
        if (drawer && drawer.classList.contains("is-open")) {
          closeDrawer();
          if (menuToggle) {
            menuToggle.focus();
          }
        }
      }
    });

    // 5. Language / RTL Direction Persistence
    var applyLang = function (lang) {
      var dir = lang === "ar" ? "rtl" : "ltr";
      document.documentElement.setAttribute("lang", lang);
      document.documentElement.setAttribute("dir", dir);
      langBtns.forEach(function (b) {
        var active = b.getAttribute("data-lang-btn") === lang;
        b.setAttribute("aria-pressed", active ? "true" : "false");
      });
      try {
        window.sessionStorage.setItem(DIR_KEY, lang);
      } catch (e) {}
    };

    try {
      var savedLang = window.sessionStorage.getItem(DIR_KEY);
      if (savedLang === "ar" || savedLang === "en") {
        applyLang(savedLang);
      }
    } catch (e) {}

    langBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        applyLang(btn.getAttribute("data-lang-btn"));
      });
    });

    // 6. Live Studio Clock
    var clockEls = Array.prototype.slice.call(document.querySelectorAll("[data-tz-clock]"));
    if (clockEls.length > 0) {
      var updateClocks = function () {
        var now = new Date();
        clockEls.forEach(function (el) {
          var tz = el.getAttribute("data-tz-clock") || "Africa/Cairo";
          try {
            el.textContent = new Intl.DateTimeFormat("en-GB", {
              hour: "2-digit",
              minute: "2-digit",
              timeZone: tz
            }).format(now);
          } catch (e) {
            el.textContent = "12:00";
          }
        });
      };
      updateClocks();
    }
  }

  global.EmpetixApp = global.EmpetixApp || {};
  global.EmpetixApp.initNav = initNav;
})(window);
