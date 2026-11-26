/**
 * Empetix Command — Dashboard AI Copilot client controller.
 * Drives both the full /admin/assistant workspace and the global slide-over drawer.
 */
(function () {
  if (window.__empetixCopilotBound) return;
  window.__empetixCopilotBound = true;

  function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
  }

  function renderToolBadges(tools) {
    if (!Array.isArray(tools) || !tools.length) return '';
    return '<div class="ai-msg__tools">' + tools.map(function (t) {
      var cls = 'ai-tool-exec';
      var prefix = '✓ ';
      if (t.pending) { cls += ' is-pending'; prefix = '⏳ '; }
      else if (t.denied || t.failed) { cls += ' is-denied'; prefix = '✕ '; }
      return '<span class="' + cls + '">' + prefix + escapeHtml(t.name) + '</span>';
    }).join('') + '</div>';
  }

  function renderApprovalParams(args) {
    if (!args || typeof args !== 'object') return '';
    var keys = Object.keys(args);
    if (!keys.length) return '';
    return '<div class="ai-approval-box__params">' + keys.map(function (k) {
      var v = args[k];
      var formatted = typeof v === 'object' ? JSON.stringify(v) : String(v);
      if (formatted.length > 180) formatted = formatted.slice(0, 180) + '…';
      return (
        '<div class="ai-approval-box__param-row">' +
          '<span class="ai-approval-box__param-key">' + escapeHtml(k) + '</span>' +
          '<span class="ai-approval-box__param-val" dir="auto">' + escapeHtml(formatted) + '</span>' +
        '</div>'
      );
    }).join('') + '</div>';
  }

  function renderApprovals(conversationId, approvals) {
    if (!Array.isArray(approvals) || !approvals.length) return '';
    return approvals.map(function (item) {
      return (
        '<div class="ai-approval-box" data-approval-card data-conversation-id="' + escapeHtml(conversationId) + '" data-call-id="' + escapeHtml(item.id) + '">' +
          '<div class="ai-approval-box__head">' +
            '<span class="ai-approval-box__kicker">Confirmation Needed</span>' +
            '<h4 class="ai-approval-box__title">' + escapeHtml(item.tool_label || item.tool) + '</h4>' +
          '</div>' +
          '<p class="ai-approval-box__reason" dir="auto">' + escapeHtml(item.reason) + '</p>' +
          renderApprovalParams(item.arguments) +
          '<div class="ai-approval-box__actions">' +
            '<button type="button" class="btn btn--primary btn--sm" data-approval-decision="approve">Approve &amp; Execute</button>' +
            '<button type="button" class="btn btn--ghost btn--sm" data-approval-decision="reject">Decline</button>' +
          '</div>' +
        '</div>'
      );
    }).join('');
  }

  function mountController(root) {
    if (!root || root.__copilotMounted) return;
    root.__copilotMounted = true;

    var endpoints = {
      message: root.getAttribute('data-url-message'),
      conversations: root.getAttribute('data-url-conversations'),
      showTemplate: root.getAttribute('data-url-show-template'),
      approveTemplate: root.getAttribute('data-url-approve-template'),
      deleteTemplate: root.getAttribute('data-url-delete-template')
    };

    var botIconUrl = root.getAttribute('data-bot-icon') || '/assets/images/3d/3d-rocket.png';
    var userInitial = root.getAttribute('data-user-initial') || 'A';

    var messagesEl = root.querySelector('[data-ai-messages]');
    var emptyEl = root.querySelector('[data-ai-empty]');
    var formEl = root.querySelector('[data-ai-form]');
    var inputEl = root.querySelector('[data-ai-input]');
    var sendBtn = root.querySelector('[data-ai-send]');
    var newBtns = root.querySelectorAll('[data-ai-new]');
    var convListEl = root.querySelector('[data-ai-conv-list]');
    var titleEl = root.querySelector('[data-ai-active-title]');

    var state = {
      conversationId: root.getAttribute('data-initial-conversation-id') || null,
      busy: false
    };

    function syncTextareaHeight() {
      if (!inputEl) return;
      inputEl.style.height = 'auto';
      var next = Math.min(inputEl.scrollHeight, 140);
      inputEl.style.height = next + 'px';
      inputEl.classList.toggle('is-scrollable', inputEl.scrollHeight > 140);
    }

    var initialJson = root.querySelector('[data-ai-initial-payload]');
    if (initialJson) {
      try {
        var parsed = JSON.parse(initialJson.textContent || 'null');
        if (parsed && parsed.id) {
          renderConversation(parsed);
        }
      } catch (e) {}
    }

    function scrollBottom() {
      if (messagesEl) {
        messagesEl.scrollTop = messagesEl.scrollHeight;
      }
    }

    function setBusy(busy) {
      state.busy = busy;
      if (sendBtn) sendBtn.disabled = busy;
      if (inputEl) inputEl.disabled = busy;
    }

    function botAvatarHtml() {
      return (
        '<div class="ai-avatar ai-avatar--bot" aria-hidden="true">' +
          '<svg width="16" height="16" viewBox="0 0 24 24" fill="none">' +
            '<path d="M12 2.5L14.35 8.65L20.5 11L14.35 13.35L12 19.5L9.65 13.35L3.5 11L9.65 8.65L12 2.5Z" fill="#FF5E36" stroke="#FFA082" stroke-width="1.2" stroke-linejoin="round"/>' +
            '<path d="M19 3L19.75 5.25L22 6L19.75 6.75L19 9L18.25 6.75L16 6L18.25 5.25L19 3Z" fill="#FFFFFF"/>' +
          '</svg>' +
        '</div>'
      );
    }

    function userAvatarHtml() {
      return '<div class="ai-avatar ai-avatar--user">' + escapeHtml(userInitial) + '</div>';
    }

    function showTyping() {
      removeTyping();
      if (emptyEl) emptyEl.hidden = true;
      var wrap = document.createElement('div');
      wrap.className = 'ai-row ai-row--assistant';
      wrap.setAttribute('data-ai-typing', '1');
      wrap.innerHTML = botAvatarHtml() + '<div class="ai-msg"><div class="ai-typing" aria-label="Copilot is thinking"><span></span><span></span><span></span></div></div>';
      messagesEl.appendChild(wrap);
      scrollBottom();
    }

    function removeTyping() {
      var existing = messagesEl && messagesEl.querySelector('[data-ai-typing]');
      if (existing) existing.remove();
    }

    function cleanTitle(rawTitle) {
      if (!rawTitle || rawTitle === 'Beginning a new conversation') {
        return 'Empetix Command Copilot';
      }
      return rawTitle;
    }

    function renderConversation(conv) {
      state.conversationId = conv ? conv.id : null;
      if (titleEl) {
        titleEl.textContent = cleanTitle(conv && conv.title);
      }

      Array.prototype.forEach.call(messagesEl.querySelectorAll('.ai-row, [data-approval-card]'), function (el) {
        el.remove();
      });

      var msgs = (conv && Array.isArray(conv.messages)) ? conv.messages : [];
      var approvals = (conv && Array.isArray(conv.pending_approvals)) ? conv.pending_approvals : [];

      if (!msgs.length && !approvals.length) {
        if (emptyEl) emptyEl.hidden = false;
        return;
      }

      if (emptyEl) emptyEl.hidden = true;

      var html = msgs.map(function (m) {
        var isUser = m.role === 'user';
        var rowClass = isUser ? 'ai-row--user' : 'ai-row--assistant';
        var avatar = isUser ? userAvatarHtml() : botAvatarHtml();
        var author = isUser ? 'You' : 'Empetix Copilot';
        var bodyHtml = m.html || escapeHtml(m.content || '');
        if (!bodyHtml && m.status === 'paused') {
          bodyHtml = '<em>Action prepared — awaiting your confirmation below.</em>';
        }

        return (
          '<div class="ai-row ' + rowClass + '">' +
            avatar +
            '<div class="ai-msg">' +
              '<div class="ai-msg__meta">' +
                '<span class="ai-msg__author">' + author + '</span>' +
                (m.created_human ? '<span>&middot; ' + escapeHtml(m.created_human) + '</span>' : '') +
              '</div>' +
              renderToolBadges(m.tools) +
              (bodyHtml ? '<div class="ai-msg__bubble" dir="auto">' + bodyHtml + '</div>' : '') +
            '</div>' +
          '</div>'
        );
      }).join('');

      if (approvals.length && conv && conv.id) {
        html += renderApprovals(conv.id, approvals);
      }

      messagesEl.insertAdjacentHTML('beforeend', html);
      highlightActiveConv();
      scrollBottom();
    }

    function chatSvgIcon() {
      return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    }

    function renderConversationsList(items) {
      if (!convListEl || !Array.isArray(items)) return;
      if (!items.length) {
        convListEl.innerHTML = '<li class="ai-conv-empty">No saved sessions yet. Start a task on the right!</li>';
        return;
      }

      convListEl.innerHTML = items.map(function (c) {
        var active = c.id === state.conversationId ? ' is-active' : '';
        var displayTitle = (!c.title || c.title === 'Beginning a new conversation') ? 'Recent Session' : c.title;
        return (
          '<li class="ai-conv-item' + active + '" data-conv-select="' + escapeHtml(c.id) + '">' +
            '<span class="ai-conv-item__icon" aria-hidden="true">' + chatSvgIcon() + '</span>' +
            '<div class="ai-conv-item__main">' +
              '<div class="ai-conv-item__title" dir="auto">' + escapeHtml(displayTitle) + '</div>' +
              '<div class="ai-conv-item__meta">' + escapeHtml(c.updated_human || '') + '</div>' +
            '</div>' +
            '<button type="button" class="ai-conv-item__del" data-conv-delete="' + escapeHtml(c.id) + '" title="Delete session" aria-label="Delete session">&times;</button>' +
          '</li>'
        );
      }).join('');
    }

    function highlightActiveConv() {
      if (!convListEl) return;
      Array.prototype.forEach.call(convListEl.querySelectorAll('[data-conv-select]'), function (li) {
        li.classList.toggle('is-active', li.getAttribute('data-conv-select') === state.conversationId);
      });
    }

    function appendOptimisticUserBubble(text) {
      if (emptyEl) emptyEl.hidden = true;
      var div = document.createElement('div');
      div.className = 'ai-row ai-row--user';
      div.innerHTML =
        userAvatarHtml() +
        '<div class="ai-msg">' +
          '<div class="ai-msg__meta"><span class="ai-msg__author">You</span></div>' +
          '<div class="ai-msg__bubble" dir="auto">' + escapeHtml(text).replace(/\n/g, '<br>') + '</div>' +
        '</div>';
      messagesEl.appendChild(div);
      scrollBottom();
    }

    function sendPrompt(text) {
      text = (text || '').trim();
      if (!text || state.busy) return;

      setBusy(true);
      if (inputEl) {
        inputEl.value = '';
        syncTextareaHeight();
      }

      appendOptimisticUserBubble(text);
      showTyping();

      fetch(endpoints.message, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken()
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          prompt: text,
          conversation_id: state.conversationId
        })
      })
        .then(function (res) { return res.json().then(function (body) { return { status: res.status, body: body }; }); })
        .then(function (result) {
          removeTyping();
          if (!result.body || !result.body.ok) {
            var errMsg = (result.body && result.body.message) ? result.body.message : 'Could not complete AI request.';
            if (window.empetixToast) window.empetixToast(errMsg, 'error');
            var errDiv = document.createElement('div');
            errDiv.className = 'ai-row ai-row--assistant';
            errDiv.innerHTML = botAvatarHtml() + '<div class="ai-msg"><div class="ai-msg__bubble" style="border-color:var(--danger,#f87171);">' + escapeHtml(errMsg) + '</div></div>';
            messagesEl.appendChild(errDiv);
            scrollBottom();
            return;
          }

          renderConversation(result.body.conversation);
          if (result.body.conversations) {
            renderConversationsList(result.body.conversations);
          }
        })
        .catch(function () {
          removeTyping();
          if (window.empetixToast) window.empetixToast('Network error while contacting Copilot.', 'error');
        })
        .finally(function () {
          setBusy(false);
          if (inputEl) inputEl.focus();
        });
    }

    function submitApprovalDecision(convId, callId, approved, cardEl) {
      if (state.busy) return;
      setBusy(true);
      if (cardEl) {
        Array.prototype.forEach.call(cardEl.querySelectorAll('button'), function (b) { b.disabled = true; });
      }
      showTyping();

      var url = endpoints.approveTemplate.replace('__CONV__', encodeURIComponent(convId));
      var decisions = {};
      decisions[callId] = approved;

      fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken()
        },
        credentials: 'same-origin',
        body: JSON.stringify({ decisions: decisions })
      })
        .then(function (res) { return res.json(); })
        .then(function (body) {
          removeTyping();
          if (!body || !body.ok) {
            if (window.empetixToast) window.empetixToast((body && body.message) || 'Approval action failed.', 'error');
            return;
          }
          renderConversation(body.conversation);
          if (window.empetixToast) {
            window.empetixToast(approved ? 'Action approved and executed.' : 'Action declined.', 'success');
          }
        })
        .catch(function () {
          removeTyping();
          if (window.empetixToast) window.empetixToast('Network error while resolving approval.', 'error');
        })
        .finally(function () {
          setBusy(false);
        });
    }

    function loadConversationById(id) {
      if (!id || state.busy) return;
      setBusy(true);
      var url = endpoints.showTemplate.replace('__CONV__', encodeURIComponent(id));
      fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (res) { return res.json(); })
        .then(function (body) {
          if (body && body.ok && body.conversation) {
            renderConversation(body.conversation);
          }
        })
        .finally(function () { setBusy(false); });
    }

    function deleteConversationById(id) {
      if (!id || state.busy) return;
      var url = endpoints.deleteTemplate.replace('__CONV__', encodeURIComponent(id));
      fetch(url, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken()
        },
        credentials: 'same-origin'
      })
        .then(function (res) { return res.json(); })
        .then(function () {
          var item = convListEl && convListEl.querySelector('[data-conv-select="' + id + '"]');
          if (item) item.remove();
          if (convListEl && !convListEl.querySelector('[data-conv-select]')) {
            convListEl.innerHTML = '<li class="ai-conv-empty">No saved sessions yet. Start a task on the right!</li>';
          }
          if (state.conversationId === id) {
            renderConversation(null);
          }
          if (window.empetixToast) window.empetixToast('Session deleted.', 'success');
        });
    }

    if (formEl) {
      formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        sendPrompt(inputEl ? inputEl.value : '');
      });
    }

    if (inputEl) {
      inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendPrompt(inputEl.value);
        }
      });
      inputEl.addEventListener('input', syncTextareaHeight);
    }

    Array.prototype.forEach.call(newBtns, function (btn) {
      btn.addEventListener('click', function () {
        renderConversation(null);
        highlightActiveConv();
        if (inputEl) inputEl.focus();
      });
    });

    root.addEventListener('click', function (e) {
      var starter = e.target.closest('[data-ai-prompt]');
      if (starter) {
        e.preventDefault();
        sendPrompt(starter.getAttribute('data-ai-prompt'));
        return;
      }

      var decisionBtn = e.target.closest('[data-approval-decision]');
      if (decisionBtn) {
        e.preventDefault();
        var card = decisionBtn.closest('[data-approval-card]');
        if (!card) return;
        submitApprovalDecision(
          card.getAttribute('data-conversation-id'),
          card.getAttribute('data-call-id'),
          decisionBtn.getAttribute('data-approval-decision') === 'approve',
          card
        );
        return;
      }

      var delBtn = e.target.closest('[data-conv-delete]');
      if (delBtn) {
        e.preventDefault();
        e.stopPropagation();
        deleteConversationById(delBtn.getAttribute('data-conv-delete'));
        return;
      }

      var selectLi = e.target.closest('[data-conv-select]');
      if (selectLi) {
        e.preventDefault();
        loadConversationById(selectLi.getAttribute('data-conv-select'));
      }
    });
  }

  function initAll() {
    Array.prototype.forEach.call(document.querySelectorAll('[data-copilot-root]'), mountController);
  }

  window.empetixOpenCopilotDrawer = function () {
    var drawer = document.getElementById('copilot-drawer');
    var backdrop = document.getElementById('copilot-drawer-backdrop');
    if (!drawer || !backdrop) return;
    drawer.classList.add('is-open');
    backdrop.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    var input = drawer.querySelector('[data-ai-input]');
    if (input) setTimeout(function () { input.focus(); }, 120);
  };

  window.empetixCloseCopilotDrawer = function () {
    var drawer = document.getElementById('copilot-drawer');
    var backdrop = document.getElementById('copilot-drawer-backdrop');
    if (!drawer || !backdrop) return;
    drawer.classList.remove('is-open');
    backdrop.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
  };

  document.addEventListener('keydown', function (e) {
    if (e.altKey && e.key.toLowerCase() === 'a') {
      var drawer = document.getElementById('copilot-drawer');
      if (drawer) {
        e.preventDefault();
        if (drawer.classList.contains('is-open')) window.empetixCloseCopilotDrawer();
        else window.empetixOpenCopilotDrawer();
      }
    }
    if (e.key === 'Escape') {
      window.empetixCloseCopilotDrawer();
    }
  });

  document.addEventListener('DOMContentLoaded', initAll);
  document.addEventListener('livewire:navigated', initAll);
})();
