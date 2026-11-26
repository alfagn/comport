/**
 * Empetix Solutions — Public AI Customer Support Chatbot Widget.
 * Persists visitor chat token in localStorage and hydrates lazily on open.
 */
(function () {
  var root = document.getElementById('empetix-support-widget');
  if (!root) return;

  var STORAGE_KEY = 'empetix_support_chat_token';
  var sessionUrl = root.getAttribute('data-session-url');
  var messageUrl = root.getAttribute('data-message-url');
  var csrf = root.getAttribute('data-csrf') || '';
  var defaultGreeting = root.getAttribute('data-greeting') || '';

  var toggleBtn = root.querySelector('[data-chat-toggle]');
  var closeBtn = root.querySelector('[data-chat-close]');
  var resetBtn = root.querySelector('[data-chat-reset]');
  var bodyEl = root.querySelector('[data-chat-body]');
  var chipsEl = root.querySelector('[data-chat-chips]');
  var formEl = root.querySelector('[data-chat-form]');
  var inputEl = root.querySelector('[data-chat-input]');
  var sendBtn = root.querySelector('[data-chat-send]');
  var bannerEl = root.querySelector('[data-chat-handoff]');

  var state = {
    open: false,
    hydrated: false,
    busy: false,
    token: null
  };

  try {
    state.token = localStorage.getItem(STORAGE_KEY) || null;
  } catch (e) {}

  function saveToken(token) {
    state.token = token || null;
    try {
      if (token) localStorage.setItem(STORAGE_KEY, token);
      else localStorage.removeItem(STORAGE_KEY);
    } catch (e) {}
  }

  function escapeHtml(str) {
    var d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function scrollBottom() {
    if (bodyEl) bodyEl.scrollTop = bodyEl.scrollHeight;
  }

  function setBusy(busy) {
    state.busy = busy;
    if (sendBtn) sendBtn.disabled = busy;
    if (inputEl) inputEl.disabled = busy;
  }

  function showTyping() {
    hideTyping();
    var div = document.createElement('div');
    div.className = 'emp-chat__msg emp-chat__msg--bot';
    div.setAttribute('data-chat-typing', '1');
    div.innerHTML = '<div class="emp-chat__typing" aria-label="Assistant is typing"><span></span><span></span><span></span></div>';
    bodyEl.appendChild(div);
    scrollBottom();
  }

  function hideTyping() {
    var el = bodyEl && bodyEl.querySelector('[data-chat-typing]');
    if (el) el.remove();
  }

  function renderSession(session) {
    if (!session) return;
    if (session.token) saveToken(session.token);

    if (bannerEl) {
      bannerEl.hidden = !session.handed_off;
    }

    Array.prototype.forEach.call(bodyEl.querySelectorAll('[data-dynamic-msg]'), function (n) {
      n.remove();
    });

    var messages = Array.isArray(session.messages) ? session.messages : [];
    if (chipsEl) {
      chipsEl.hidden = messages.length > 0;
    }

    messages.forEach(function (m) {
      var wrap = document.createElement('div');
      wrap.className = 'emp-chat__msg ' + (m.role === 'user' ? 'emp-chat__msg--user' : 'emp-chat__msg--bot');
      wrap.setAttribute('data-dynamic-msg', '1');
      wrap.innerHTML = '<div class="emp-chat__bubble" dir="auto">' + (m.html || escapeHtml(m.content || '')) + '</div>';
      bodyEl.appendChild(wrap);
    });

    if (session.fallback_reply) {
      var fb = document.createElement('div');
      fb.className = 'emp-chat__msg emp-chat__msg--bot';
      fb.setAttribute('data-dynamic-msg', '1');
      fb.innerHTML = '<div class="emp-chat__bubble" dir="auto">' + (session.fallback_reply.html || escapeHtml(session.fallback_reply.content || '')) + '</div>';
      bodyEl.appendChild(fb);
    }

    scrollBottom();
  }

  function hydrateIfNeeded() {
    if (state.hydrated) return;
    state.hydrated = true;
    if (!state.token) return;

    var url = sessionUrl + '?token=' + encodeURIComponent(state.token);
    fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (data) {
        if (data && data.ok && data.session) {
          renderSession(data.session);
        }
      })
      .catch(function () {});
  }

  function toggleOpen(force) {
    var panelEl = root.querySelector('.emp-chat__panel');
    if (!panelEl) return;
    var next = typeof force === 'boolean' ? force : !state.open;
    state.open = next;
    root.classList.toggle('is-open', next);
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', next ? 'true' : 'false');
    panelEl.setAttribute('aria-hidden', next ? 'false' : 'true');

    if (next) {
      hydrateIfNeeded();
      setTimeout(function () { if (inputEl) inputEl.focus(); scrollBottom(); }, 100);
    }
  }

  function sendMessage(text) {
    text = (text || '').trim();
    if (!text || state.busy) return;

    setBusy(true);
    if (chipsEl) chipsEl.hidden = true;
    if (inputEl) {
      inputEl.value = '';
      inputEl.style.height = 'auto';
    }

    var userNode = document.createElement('div');
    userNode.className = 'emp-chat__msg emp-chat__msg--user';
    userNode.setAttribute('data-dynamic-msg', '1');
    userNode.innerHTML = '<div class="emp-chat__bubble" dir="auto">' + escapeHtml(text).replace(/\n/g, '<br>') + '</div>';
    bodyEl.appendChild(userNode);
    showTyping();

    fetch(messageUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        message: text,
        token: state.token,
        landing_page: window.location.pathname
      })
    })
      .then(function (res) { return res.json().then(function (b) { return { status: res.status, body: b }; }); })
      .then(function (result) {
        hideTyping();
        if (result.body && result.body.ok && result.body.session) {
          renderSession(result.body.session);
        } else {
          var errMsg = (result.body && result.body.message) || 'Sorry, something went wrong. Please try again in a moment.';
          var errEl = document.createElement('div');
          errEl.className = 'emp-chat__msg emp-chat__msg--bot';
          errEl.setAttribute('data-dynamic-msg', '1');
          errEl.innerHTML = '<div class="emp-chat__bubble" dir="auto">' + escapeHtml(errMsg) + '</div>';
          bodyEl.appendChild(errEl);
          scrollBottom();
        }
      })
      .catch(function () {
        hideTyping();
        var errEl = document.createElement('div');
        errEl.className = 'emp-chat__msg emp-chat__msg--bot';
        errEl.setAttribute('data-dynamic-msg', '1');
        errEl.innerHTML = '<div class="emp-chat__bubble" dir="auto">Connection interrupted. Please try again or use our Contact page.</div>';
        bodyEl.appendChild(errEl);
        scrollBottom();
      })
      .finally(function () {
        setBusy(false);
        if (inputEl) inputEl.focus();
      });
  }

  if (toggleBtn) toggleBtn.addEventListener('click', function () { toggleOpen(); });
  if (closeBtn) closeBtn.addEventListener('click', function () { toggleOpen(false); });

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      saveToken(null);
      if (bannerEl) bannerEl.hidden = true;
      Array.prototype.forEach.call(bodyEl.querySelectorAll('[data-dynamic-msg]'), function (n) { n.remove(); });
      if (chipsEl) chipsEl.hidden = false;
      var greetEl = root.querySelector('[data-chat-greeting]');
      if (greetEl && defaultGreeting) greetEl.textContent = defaultGreeting;
    });
  }

  if (formEl) {
    formEl.addEventListener('submit', function (e) {
      e.preventDefault();
      sendMessage(inputEl ? inputEl.value : '');
    });
  }

  if (inputEl) {
    inputEl.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage(inputEl.value);
      }
    });
    inputEl.addEventListener('input', function () {
      inputEl.style.height = 'auto';
      var next = Math.min(inputEl.scrollHeight, 108);
      inputEl.style.height = next + 'px';
      inputEl.classList.toggle('is-scrollable', inputEl.scrollHeight > 108);
    });
  }

  root.addEventListener('click', function (e) {
    var chip = e.target.closest('[data-chat-prompt]');
    if (chip) {
      e.preventDefault();
      sendMessage(chip.getAttribute('data-chat-prompt'));
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && state.open) {
      toggleOpen(false);
    }
  });
})();
