/**
 * EMPETIX — Navigation, Hysteresis Sticky Header, Mega Menu, Search, Mobile Drawer & RTL
 * File: assets/js/modules/nav.js
 */

(function (global) {
  "use strict";

  var DIR_KEY = "empetix_lang_dir";

  function initNav() {
    var head = document.getElementById("site-head");
    var mega = document.getElementById("mega-menu");
    var triggers = Array.prototype.slice.call(document.querySelectorAll("[data-mega-trigger]"));
    var panels = Array.prototype.slice.call(document.querySelectorAll("[data-mega-panel]"));

    var searchToggle = document.getElementById("search-toggle");
    var searchPanel = document.getElementById("search-panel");
    var searchInput = document.getElementById("search-input");
    var searchClose = document.getElementById("search-close");

    var menuToggle = document.getElementById("menu-toggle");
    var drawer = document.getElementById("mobile-drawer");
    var drawerClose = document.getElementById("drawer-close");

    var langBtns = Array.prototype.slice.call(document.querySelectorAll("[data-lang-btn]"));

    // 1. Hysteresis Scroll Handler (Eliminates any flicker when scrolling near top)
    var isCompact = false;
    var ticking = false;

    var updateHeader = function () {
      ticking = false;
      if (!head) {
        return;
      }
      var y = window.scrollY || window.pageYOffset || 0;
      if (!isCompact && y > 64) {
        isCompact = true;
        head.classList.add("is-compact");
      } else if (isCompact && y < 16) {
        isCompact = false;
        head.classList.remove("is-compact");
      }
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

    // 2. Mega Menu with Full Keyboard Support
    var activeBtn = null;

    var closeMega = function (returnFocus) {
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

    var toggleMega = function (btn) {
      if (!mega) {
        return;
      }
      var target = btn.getAttribute("data-mega-trigger");
      var expanded = btn.getAttribute("aria-expanded") === "true";

      if (expanded) {
        closeMega(false);
        return;
      }

      if (searchPanel && searchPanel.classList.contains("is-open")) {
        searchPanel.classList.remove("is-open");
        if (searchToggle) {
          searchToggle.setAttribute("aria-expanded", "false");
        }
      }

      triggers.forEach(function (b) {
        b.setAttribute("aria-expanded", b === btn ? "true" : "false");
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
      btn.addEventListener("click", function (e) {
        e.stopPropagation();
        toggleMega(btn);
      });

      btn.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight") {
          e.preventDefault();
          triggers[(idx + 1) % triggers.length].focus();
        } else if (e.key === "ArrowLeft") {
          e.preventDefault();
          triggers[(idx - 1 + triggers.length) % triggers.length].focus();
        } else if (e.key === "ArrowDown") {
          if (btn.getAttribute("aria-expanded") !== "true") {
            toggleMega(btn);
          }
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

    // 3. Search Panel
    var closeSearch = function () {
      if (!searchPanel) {
        return;
      }
      searchPanel.classList.remove("is-open");
      if (searchToggle) {
        searchToggle.setAttribute("aria-expanded", "false");
      }
    };

    if (searchToggle && searchPanel) {
      searchToggle.addEventListener("click", function () {
        var open = searchPanel.classList.toggle("is-open");
        searchToggle.setAttribute("aria-expanded", open ? "true" : "false");
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
    }

    if (searchClose) {
      searchClose.addEventListener("click", function () {
        closeSearch();
        if (head) {
          head.classList.remove("is-menu-open");
        }
        if (searchToggle) {
          searchToggle.focus();
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
          if (searchToggle) {
            searchToggle.focus();
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
      } catch (e) {
        // Ignore
      }
    };

    try {
      var savedLang = window.sessionStorage.getItem(DIR_KEY);
      if (savedLang === "ar" || savedLang === "en") {
        applyLang(savedLang);
      }
    } catch (e) {
      // Ignore
    }

    langBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        applyLang(btn.getAttribute("data-lang-btn"));
      });
    });

    // 6. Live Studio Clocks in Footer & Contact Page
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
