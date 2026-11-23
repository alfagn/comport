/**
 * EMPETIX — Hero Slider, Solutions Tab Switcher, Lightbox, Video Chapters, Filters & Forms
 * File: assets/js/modules/media.js
 */

(function (global) {
  "use strict";

  function initMedia() {
    // 0A. Link Development-Style Interactive Hero Slider with Auto-Progress Tabs
    var heroSlider = document.querySelector("[data-hero-slider]");
    if (heroSlider) {
      var slides = Array.prototype.slice.call(heroSlider.querySelectorAll("[data-hero-slide]"));
      var tabs = Array.prototype.slice.call(heroSlider.querySelectorAll("[data-hero-tab]"));
      var currentSlide = 0;
      var slideTimer = null;
      var SLIDE_INTERVAL = 6000;

      var activateSlide = function (index) {
        currentSlide = (index + slides.length) % slides.length;
        slides.forEach(function (s, i) {
          s.classList.toggle("is-active", i === currentSlide);
          s.setAttribute("aria-hidden", i === currentSlide ? "false" : "true");
        });
        tabs.forEach(function (t, i) {
          var isSelected = i === currentSlide;
          t.setAttribute("aria-selected", isSelected ? "true" : "false");
          // Reset CSS animation on the progress bar
          var bar = t.querySelector(".hero-tab__progress-bar");
          if (bar) {
            bar.style.animation = "none";
            // Trigger reflow
            void bar.offsetWidth;
            bar.style.animation = "";
          }
        });
      };

      var startAutoSlide = function () {
        stopAutoSlide();
        slideTimer = window.setInterval(function () {
          activateSlide(currentSlide + 1);
        }, SLIDE_INTERVAL);
      };

      var stopAutoSlide = function () {
        if (slideTimer) {
          window.clearInterval(slideTimer);
          slideTimer = null;
        }
      };

      tabs.forEach(function (tab, idx) {
        tab.addEventListener("click", function () {
          activateSlide(idx);
          startAutoSlide();
        });
      });

      heroSlider.addEventListener("mouseenter", stopAutoSlide);
      heroSlider.addEventListener("mouseleave", startAutoSlide);

      activateSlide(0);
      startAutoSlide();
    }

    // 0B. Interactive Solutions Hub Tab Switcher (Link Development Pattern)
    var solTabs = Array.prototype.slice.call(document.querySelectorAll("[data-solution-tab]"));
    var solPanels = Array.prototype.slice.call(document.querySelectorAll("[data-solution-panel]"));

    if (solTabs.length > 0 && solPanels.length > 0) {
      solTabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
          var target = tab.getAttribute("data-solution-tab");
          solTabs.forEach(function (t) {
            t.setAttribute("aria-selected", t === tab ? "true" : "false");
          });
          solPanels.forEach(function (p) {
            var match = p.getAttribute("data-solution-panel") === target;
            p.classList.toggle("is-active", match);
          });
        });
      });
    }

    // 1. Category Filter Tabs (Work, Blog/Journal)
    var filterBars = Array.prototype.slice.call(document.querySelectorAll("[data-filter-bar]"));
    filterBars.forEach(function (bar) {
      var scopeId = bar.getAttribute("data-filter-bar");
      var container = document.getElementById(scopeId);
      if (!container) {
        return;
      }
      var btns = Array.prototype.slice.call(bar.querySelectorAll("[data-filter]"));
      var items = Array.prototype.slice.call(container.querySelectorAll("[data-category]"));
      var countEl = bar.querySelector("[data-filter-count]");

      btns.forEach(function (btn) {
        btn.addEventListener("click", function () {
          var cat = btn.getAttribute("data-filter");
          btns.forEach(function (b) {
            b.setAttribute("aria-pressed", b === btn ? "true" : "false");
          });

          var visibleCount = 0;
          items.forEach(function (item) {
            var tags = (item.getAttribute("data-category") || "").split(/\s+/);
            var show = cat === "all" || tags.indexOf(cat) !== -1;
            if (show) {
              item.removeAttribute("hidden");
              visibleCount++;
            } else {
              item.setAttribute("hidden", "");
            }
          });

          if (countEl) {
            countEl.textContent = visibleCount + (visibleCount === 1 ? " entry" : " entries");
          }
        });
      });
    });

    // 2. Custom HTML5 Video Player + Chapter Timestamps
    var videoBlocks = Array.prototype.slice.call(document.querySelectorAll("[data-video-player]"));
    videoBlocks.forEach(function (wrap) {
      var video = wrap.querySelector("video");
      var playBtn = wrap.querySelector("[data-video-play]");
      if (!video || !playBtn) {
        return;
      }

      var startPlayback = function () {
        wrap.classList.add("is-playing");
        video.setAttribute("controls", "controls");
        var p = video.play();
        if (p && typeof p.catch === "function") {
          p.catch(function () {});
        }
      };

      playBtn.addEventListener("click", startPlayback);

      var playerId = wrap.getAttribute("data-video-player");
      if (playerId) {
        var chapterBtns = Array.prototype.slice.call(
          document.querySelectorAll('[data-video-target="' + playerId + '"][data-seek]')
        );
        chapterBtns.forEach(function (cBtn) {
          cBtn.addEventListener("click", function () {
            var sec = parseFloat(cBtn.getAttribute("data-seek") || "0");
            startPlayback();
            try {
              video.currentTime = sec;
            } catch (e) {}
            wrap.scrollIntoView({ behavior: "smooth", block: "center" });
          });
        });
      }
    });

    // 3. Photo Gallery Lightbox (<dialog>)
    var dialog = document.getElementById("photo-lightbox");
    var galleryTriggers = Array.prototype.slice.call(
      document.querySelectorAll("[data-lightbox-src]")
    );

    if (dialog && galleryTriggers.length > 0) {
      var lbImg = dialog.querySelector("[data-lightbox-img]");
      var lbCap = dialog.querySelector("[data-lightbox-caption]");
      var lbCount = dialog.querySelector("[data-lightbox-counter]");
      var prevBtn = dialog.querySelector("[data-lightbox-prev]");
      var nextBtn = dialog.querySelector("[data-lightbox-next]");
      var closeBtn = dialog.querySelector("[data-lightbox-close]");
      var currentIdx = 0;

      var showSlide = function (index) {
        currentIdx = (index + galleryTriggers.length) % galleryTriggers.length;
        var item = galleryTriggers[currentIdx];
        var src = item.getAttribute("data-lightbox-src");
        var cap = item.getAttribute("data-lightbox-caption") || "";
        if (lbImg) {
          lbImg.setAttribute("src", src);
          lbImg.setAttribute("alt", cap);
        }
        if (lbCap) {
          lbCap.textContent = cap;
        }
        if (lbCount) {
          lbCount.textContent =
            String(currentIdx + 1).padStart(2, "0") +
            " / " +
            String(galleryTriggers.length).padStart(2, "0");
        }
      };

      galleryTriggers.forEach(function (btn, idx) {
        btn.addEventListener("click", function () {
          showSlide(idx);
          if (typeof dialog.showModal === "function") {
            dialog.showModal();
          } else {
            dialog.setAttribute("open", "");
          }
        });
      });

      var closeLightbox = function () {
        if (typeof dialog.close === "function") {
          dialog.close();
        } else {
          dialog.removeAttribute("open");
        }
      };

      if (closeBtn) {
        closeBtn.addEventListener("click", closeLightbox);
      }
      if (prevBtn) {
        prevBtn.addEventListener("click", function () {
          showSlide(currentIdx - 1);
        });
      }
      if (nextBtn) {
        nextBtn.addEventListener("click", function () {
          showSlide(currentIdx + 1);
        });
      }

      dialog.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight") {
          showSlide(currentIdx + 1);
        } else if (e.key === "ArrowLeft") {
          showSlide(currentIdx - 1);
        }
      });
    }

    // 4. Article Reading Progress Bar
    var progressBar = document.getElementById("read-progress-bar");
    var articleBody = document.getElementById("article-content");
    if (progressBar && articleBody) {
      var updateProgress = function () {
        var rect = articleBody.getBoundingClientRect();
        var winH = window.innerHeight || document.documentElement.clientHeight;
        var total = rect.height - winH * 0.5;
        var scrolled = -rect.top + winH * 0.25;
        var ratio = total > 0 ? Math.max(0, Math.min(1, scrolled / total)) : 0;
        progressBar.style.transform = "scaleX(" + ratio + ")";
      };
      window.addEventListener("scroll", updateProgress, { passive: true });
      updateProgress();
    }

    // 5. Copy Article Link Button
    var copyBtns = Array.prototype.slice.call(document.querySelectorAll("[data-copy-url]"));
    copyBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        var orig = btn.textContent;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(window.location.href).catch(function () {});
        }
        btn.textContent = "Link Copied";
        window.setTimeout(function () {
          btn.textContent = orig;
        }, 2000);
      });
    });
  }

  global.EmpetixApp = global.EmpetixApp || {};
  global.EmpetixApp.initMedia = initMedia;
})(window);
