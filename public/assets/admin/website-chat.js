/**
 * Empetix Website Live Chat Studio Controller.
 * Matches the WhatsApp Web 3-Pane System Design for Website Visitor <-> AI / Human chats:
 * - Zero-latency optimistic send (0ms bubble insertion with WhatsApp Clock SVG `🕒` -> blue `✓✓`)
 * - Strict client + server idempotency (`X-Idempotency-Key`)
 * - Unread-only green circular badges, bold unread visitor names, green timestamps & live visitor typing preview
 */
(function () {
  if (window.__empetixWebChatBound) return;
  window.__empetixWebChatBound = true;

  var EMOJIS = [
    '😀','😃','😄','😊','🙂','😉','😍','🤩','🤔','🤗','🙌','👏','👍','🤝',
    '🙏','❤️','🔥','✨','🎉','💯','✅','⚡','🚀','💼','📄','💬','👋','🇪🇬'
  ];

  function csrfToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function makeIdempotencyKey() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
      return 'wc_' + window.crypto.randomUUID();
    }
    return 'wc_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 12);
  }

  function nowTimeHHMM() {
    var d = new Date();
    var h = d.getHours();
    var m = d.getMinutes();
    return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
  }

  function todayDateKey() {
    var d = new Date();
    var m = d.getMonth() + 1;
    var day = d.getDate();
    return d.getFullYear() + '-' + (m < 10 ? '0' : '') + m + '-' + (day < 10 ? '0' : '') + day;
  }

  function escapeHtml(str) {
    var d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function ticksSvg(status) {
    if (status === 'sending') {
      return '<span class="wa-ticks is-sending" title="Sending…">' +
        '<svg width="12" height="12" viewBox="0 0 16 16" fill="none">' +
          '<circle cx="8" cy="8" r="6.4" stroke="currentColor" stroke-width="1.5"/>' +
          '<path d="M8 4.4V8.2L10.6 9.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '</svg>' +
      '</span>';
    }
    if (status === 'failed') {
      return '<span class="wa-ticks is-failed" title="Failed">⚠</span>';
    }
    return '<span class="wa-ticks is-read" title="Delivered live">' +
      '<svg width="16" height="11" viewBox="0 0 16 11" fill="none"><path d="M11.07.66 4.88 6.85 2.3 4.27.9 5.68l3.98 3.98L12.48 2.07 11.07.66Zm3.98 0L8.86 6.85l-.93-.93-1.41 1.41 2.34 2.33L16.46 2.07 15.05.66Z" fill="currentColor"/></svg>' +
    '</span>';
  }

  function avatarInnerHtml(contact) {
    if (contact && contact.avatar_url) {
      return '<img src="' + escapeHtml(contact.avatar_url) + '" alt="' + escapeHtml(contact.name || 'Visitor') + '" class="wa-avatar__img" loading="lazy"/>';
    }
    var initial = contact && contact.initial ? contact.initial : '';
    if (initial && initial !== 'V') {
      return '<span>' + escapeHtml(initial) + '</span>';
    }
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" opacity="0.92"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5Zm0 2c-3.34 0-10 1.68-10 5v2h20v-2c0-3.32-6.66-5-10-5Z"/></svg>';
  }

  function applyAvatar(el, contact) {
    if (!el || !contact) return;
    el.style.background = contact.avatar_url ? 'transparent' : (contact.avatar_color || '#4F46E5');
    el.innerHTML = avatarInnerHtml(contact);
  }

  function pinIconSvg() {
    return '<span class="wa-pin-mark" title="Pinned">' +
      '<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2Z"/></svg>' +
    '</span>';
  }

  function mountWebsiteChat(root) {
    if (!root || root.__wcMounted) return;
    root.__wcMounted = true;

    var urls = {
      poll: root.getAttribute('data-url-poll'),
      showTpl: root.getAttribute('data-url-show-tpl'),
      sendTpl: root.getAttribute('data-url-send-tpl'),
      typingTpl: root.getAttribute('data-url-typing-tpl'),
      visitorTpl: root.getAttribute('data-url-visitor-tpl'),
      deleteTpl: root.getAttribute('data-url-delete-tpl')
    };

    var contactsEl = root.querySelector('[data-wc-contacts]');
    var archiveHeadEl = root.querySelector('[data-wc-archive-head]');
    var filtersBarEl = root.querySelector('[data-wc-filters-bar]');
    var ctxMenuEl = root.querySelector('[data-wc-ctx-menu]');
    var searchEl = root.querySelector('[data-wc-search]');
    var unreadTotalEl = root.querySelector('[data-wc-unread-total]');
    var welcomeEl = root.querySelector('[data-wc-welcome]');
    var convEl = root.querySelector('[data-wc-conversation]');
    var streamEl = root.querySelector('[data-wc-stream]');
    var messagesEl = root.querySelector('[data-wc-messages]');

    var headAvatar = root.querySelector('[data-wc-head-avatar]');
    var headName = root.querySelector('[data-wc-head-name]');
    var headStatus = root.querySelector('[data-wc-head-status]');
    var headSub = root.querySelector('[data-wc-head-sub]');
    var toggleAiBtn = root.querySelector('[data-wc-toggle-ai]');
    var toggleAiLabel = root.querySelector('[data-wc-toggle-ai-label]');
    var toggleArchiveBtn = root.querySelector('[data-wc-toggle-archive]');
    var togglePinBtn = root.querySelector('[data-wc-toggle-pin]');

    var inchatSearchWrap = root.querySelector('[data-wc-inchat-search]');
    var inchatInput = root.querySelector('[data-wc-inchat-input]');
    var emojiPopover = root.querySelector('[data-wc-emoji-popover]');
    var formEl = root.querySelector('[data-wc-form]');
    var inputEl = root.querySelector('[data-wc-input]');

    var drawerEl = root.querySelector('[data-wc-drawer]');
    var drawerAvatar = root.querySelector('[data-wc-drawer-avatar]');
    var avatarInput = root.querySelector('[data-wc-avatar-input]');
    var drawerTitle = root.querySelector('[data-wc-drawer-title]');
    var drawerSub = root.querySelector('[data-wc-drawer-sub]');
    var drawerMsgCount = root.querySelector('[data-wc-drawer-msg-count]');
    var drawerPresence = root.querySelector('[data-wc-drawer-presence]');
    var briefWrap = root.querySelector('[data-wc-brief-wrap]');
    var briefLink = root.querySelector('[data-wc-brief-link]');
    var crmForm = root.querySelector('[data-wc-crm-form]');
    var crmName = root.querySelector('[data-wc-crm-name]');
    var crmEmail = root.querySelector('[data-wc-crm-email]');
    var crmPhone = root.querySelector('[data-wc-crm-phone]');
    var crmStatus = root.querySelector('[data-wc-crm-status]');
    var crmNotes = root.querySelector('[data-wc-crm-notes]');
    var summaryBox = root.querySelector('[data-wc-summary-box]');
    var summaryText = root.querySelector('[data-wc-handoff-summary]');

    var state = {
      contacts: [],
      activeId: null,
      activeChat: null,
      filter: 'all',
      prevFilter: 'all',
      archivedCount: 0,
      archivedUnread: 0,
      search: '',
      inchatFilter: '',
      draftKey: makeIdempotencyKey(),
      dispatchedKeys: {},
      optimisticByChat: {},
      msgSignature: '',
      lastTypingPingAt: 0
    };

    if (emojiPopover) {
      emojiPopover.innerHTML = EMOJIS.map(function (em) {
        return '<button type="button" data-wc-emoji="' + em + '">' + em + '</button>';
      }).join('');
    }

    var initScript = root.querySelector('[data-wc-initial]');
    if (initScript) {
      try {
        var initial = JSON.parse(initScript.textContent || '{}');
        state.contacts = initial.contacts || [];
        state.archivedCount = Number(initial.archived_count) || 0;
        state.archivedUnread = Number(initial.archived_unread) || 0;
        if (initial.active_chat && initial.active_chat.contact) {
          state.activeId = initial.active_chat.contact.id;
          state.activeChat = initial.active_chat;
        }
        updateUnreadCounters(initial.total_unread || 0);
        renderContacts();
        renderActiveChat(true);
      } catch (e) {}
    }

    function updateUnreadCounters(totalUnread) {
      totalUnread = Number(totalUnread) || 0;
      if (unreadTotalEl) {
        unreadTotalEl.hidden = totalUnread <= 0;
        unreadTotalEl.textContent = totalUnread;
      }
      Array.prototype.forEach.call(document.querySelectorAll('[data-sidebar-webchat-badge]'), function (b) {
        b.textContent = totalUnread;
        b.style.display = totalUnread > 0 ? '' : 'none';
      });
    }

    function syncArchiveUi() {
      var inArch = state.filter === 'archived';
      if (archiveHeadEl) archiveHeadEl.hidden = !inArch;
      if (filtersBarEl) filtersBarEl.hidden = inArch;
    }

    function renderContacts() {
      if (!contactsEl) return;
      syncArchiveUi();

      var topArchivedRow = '';
      if (state.filter !== 'archived' && !state.search && state.archivedCount > 0) {
        var archBadge = state.archivedUnread > 0
          ? '<span class="wa-unread-badge">' + state.archivedUnread + '</span>'
          : '<span class="wa-archived-row__count">' + state.archivedCount + '</span>';
        topArchivedRow = (
          '<div class="wa-archived-row" data-wc-enter-archive>' +
            '<span class="wa-archived-row__ico">' +
              '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>' +
            '</span>' +
            '<span class="wa-archived-row__label">Archived</span>' +
            archBadge +
          '</div>'
        );
      }

      if (!state.contacts.length) {
        contactsEl.innerHTML = topArchivedRow + '<div class="wa-contact-empty">' + (state.filter === 'archived' ? 'No archived chats.' : 'No website conversations match this filter yet.') + '</div>';
        return;
      }

      contactsEl.innerHTML = topArchivedRow + state.contacts.map(function (c) {
        var active = c.id === state.activeId ? ' is-active' : '';
        var hasUnread = Number(c.unread_count) > 0;
        var unreadCls = hasUnread ? ' has-unread' : '';
        var avatarStyle = c.avatar_url ? 'background:transparent;' : ('background:' + escapeHtml(c.avatar_color || '#4F46E5') + ';');

        var previewHtml = '';
        if (c.customer_typing) {
          previewHtml = '<span class="wa-typing-green">typing' + (c.customer_preview ? ': "' + escapeHtml(c.customer_preview) + '"' : '…') + '</span>';
        } else if (c.ai_typing) {
          previewHtml = '<span class="wa-typing-green">🤖 AI Concierge replying…</span>';
        } else {
          var ticks = c.last_direction === 'outbound' ? (ticksSvg(c.last_status || 'read') + ' ') : '';
          previewHtml = ticks + '<span>' + escapeHtml(c.preview || '') + '</span>';
        }

        var onlineDot = c.online
          ? '<span style="position:absolute;right:1px;bottom:1px;width:11px;height:11px;border-radius:50%;background:#25D366;border:2px solid var(--wa-sidebar-bg);" title="Online on website"></span>'
          : '';

        return (
          '<div class="wa-contact-row' + active + unreadCls + '" data-wc-select="' + c.id + '">' +
            '<div class="wa-avatar" style="' + avatarStyle + ';overflow:visible;">' +
              '<div style="width:100%;height:100%;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;">' + avatarInnerHtml(c) + '</div>' +
              onlineDot +
            '</div>' +
            '<div class="wa-contact-row__main">' +
              '<div class="wa-contact-row__top">' +
                '<span class="wa-contact-row__name" dir="auto">' + escapeHtml(c.name) + '</span>' +
                '<span class="wa-contact-row__time">' + escapeHtml(c.time_label || '') + '</span>' +
              '</div>' +
              '<div class="wa-contact-row__identity">' +
                '<span class="wa-contact-row__phone">' + escapeHtml(c.sub_identity || '') + '</span>' +
              '</div>' +
              '<div class="wa-contact-row__bottom">' +
                '<span class="wa-contact-row__preview" dir="auto">' + previewHtml + '</span>' +
                '<span class="wa-contact-row__badges">' +
                  (c.is_pinned ? pinIconSvg() : '') +
                  (hasUnread ? '<span class="wa-unread-badge">' + (c.unread_count > 99 ? '99+' : c.unread_count) + '</span>' : '') +
                  '<button type="button" class="wa-row-menu-btn" data-wc-row-menu="' + c.id + '" title="Chat options">' +
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>' +
                  '</button>' +
                '</span>' +
              '</div>' +
            '</div>' +
          '</div>'
        );
      }).join('');
    }

    function getMergedMessages(chatId, serverMsgs) {
      serverMsgs = serverMsgs || [];
      var optList = state.optimisticByChat[chatId] || [];
      if (!optList.length) return serverMsgs;

      var seenKeys = {};
      serverMsgs.forEach(function (m) {
        if (m.idempotency_key) seenKeys[m.idempotency_key] = true;
      });

      var remaining = optList.filter(function (opt) {
        return !(opt.idempotency_key && seenKeys[opt.idempotency_key]);
      });
      state.optimisticByChat[chatId] = remaining;

      return serverMsgs.concat(remaining);
    }

    function computeSignature(msgs, presence, contact) {
      var parts = (msgs || []).map(function (m) { return m.id + ':' + m.status; });
      parts.push('ct:' + (presence && presence.customer_typing ? (presence.customer_preview || '1') : '0'));
      parts.push('ai:' + (presence && presence.ai_typing ? '1' : '0'));
      parts.push('q:' + state.inchatFilter);
      parts.push('av:' + (contact && contact.avatar_url ? contact.avatar_url : ''));
      return parts.join('|');
    }

    function renderActiveChat(forceScroll) {
      if (!state.activeChat || !state.activeChat.contact) {
        if (welcomeEl) welcomeEl.hidden = false;
        if (convEl) convEl.hidden = true;
        if (drawerEl) drawerEl.hidden = true;
        root.classList.remove('has-drawer');
        return;
      }

      if (welcomeEl) welcomeEl.hidden = true;
      if (convEl) convEl.hidden = false;

      var c = state.activeChat.contact;
      var presence = state.activeChat.presence || {};

      applyAvatar(headAvatar, c);
      if (headName) headName.textContent = c.name;
      if (headStatus) headStatus.textContent = c.status_label || 'Open';

      if (headSub) {
        if (presence.customer_typing) {
          headSub.innerHTML = '<span class="wa-typing-green">Visitor is typing' + (presence.customer_preview ? ': "' + escapeHtml(presence.customer_preview) + '"' : '…') + '</span>';
        } else if (presence.ai_typing) {
          headSub.innerHTML = '<span class="wa-typing-green">🤖 Empetix AI is generating a reply…</span>';
        } else {
          var subBits = [];
          if (c.email) subBits.push(escapeHtml(c.email));
          if (c.phone) subBits.push(escapeHtml(c.phone));
          subBits.push('Page: ' + escapeHtml(c.landing_page || '/'));
          if (c.online) subBits.push('<span style="color:#25D366;font-weight:700;">● Online</span>');
          headSub.innerHTML = subBits.join(' &middot; ');
        }
      }

      if (toggleAiBtn && toggleAiLabel) {
        toggleAiBtn.classList.toggle('is-human', !!c.ai_paused);
        toggleAiLabel.textContent = c.ai_paused ? 'Human Mode' : 'AI Auto';
      }
      if (toggleArchiveBtn) {
        toggleArchiveBtn.classList.toggle('is-active', !!c.is_archived);
      }
      if (togglePinBtn) {
        togglePinBtn.classList.toggle('is-active', !!c.is_pinned);
      }

      // Sync Right Drawer
      applyAvatar(drawerAvatar, c);
      if (drawerTitle) drawerTitle.textContent = c.name;
      if (drawerSub) drawerSub.textContent = (c.landing_page || '/') + (c.ip_address ? ' · ' + c.ip_address : '');
      if (drawerMsgCount) drawerMsgCount.textContent = c.total_messages || 0;
      if (drawerPresence) drawerPresence.textContent = c.online ? 'Online' : 'Idle';

      if (briefWrap && briefLink) {
        if (c.contact_message_url) {
          briefWrap.hidden = false;
          briefLink.href = c.contact_message_url;
        } else {
          briefWrap.hidden = true;
        }
      }

      if (summaryBox && summaryText) {
        if (c.handoff_summary) {
          summaryBox.hidden = false;
          summaryText.textContent = c.handoff_summary;
        } else {
          summaryBox.hidden = true;
        }
      }

      if (crmName && document.activeElement !== crmName) crmName.value = c.visitor_name || '';
      if (crmEmail && document.activeElement !== crmEmail) crmEmail.value = c.email || '';
      if (crmPhone && document.activeElement !== crmPhone) crmPhone.value = c.phone || '';
      if (crmStatus && document.activeElement !== crmStatus) crmStatus.value = c.status || 'open';
      if (crmNotes && document.activeElement !== crmNotes) crmNotes.value = c.contact_notes || '';

      var allMsgs = getMergedMessages(c.id, state.activeChat.messages || []);
      var sig = computeSignature(allMsgs, presence, c);
      if (!forceScroll && sig === state.msgSignature) return;

      var wasNearBottom = !streamEl || (streamEl.scrollHeight - streamEl.scrollTop - streamEl.clientHeight < 140);
      state.msgSignature = sig;

      var qLower = state.inchatFilter.toLowerCase();
      var filtered = qLower
        ? allMsgs.filter(function (m) { return (m.body || '').toLowerCase().indexOf(qLower) !== -1; })
        : allMsgs;

      var lastDateKey = '';
      var html = (
        '<div class="wa-e2ee-banner">' +
          '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2Zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2Zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2Z"/></svg>' +
          '<span>Live Website Chat &middot; Replies stream instantaneously to the visitor widget with AES-256 note protection.</span>' +
        '</div>'
      );

      filtered.forEach(function (m) {
        if (m.date_key && m.date_key !== lastDateKey) {
          lastDateKey = m.date_key;
          html += '<div class="wa-date-divider">' + escapeHtml(m.date_label) + '</div>';
        }

        var isOut = m.direction === 'outbound';
        var rowCls = isOut ? 'wa-msg-row wa-msg-row--out' : 'wa-msg-row wa-msg-row--in';
        var toolsHtml = '';
        if (m.tools && m.tools.length) {
          toolsHtml = '<div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:4px;">' +
            m.tools.map(function (t) {
              return '<span style="font-size:0.66rem;font-weight:700;padding:1px 6px;border-radius:99px;background:rgba(0,168,132,0.15);color:var(--wa-green);">⚙ ' + escapeHtml(t) + '</span>';
            }).join('') +
          '</div>';
        }

        html += (
          '<div class="' + rowCls + '" data-wc-msg-body="' + escapeHtml(m.body || '') + '">' +
            '<div class="wa-bubble">' +
              (isOut && m.sender_name ? '<div class="wa-bubble__sender">' + escapeHtml(m.sender_name) + '</div>' : '') +
              toolsHtml +
              '<div class="wa-bubble__text" dir="auto">' + (m.html || escapeHtml(m.body)) + '</div>' +
              (m.error ? '<div class="wa-bubble__err">⚠ ' + escapeHtml(m.error) + '</div>' : '') +
              '<div class="wa-bubble__foot">' +
                '<span>' + escapeHtml(m.time || '') + '</span>' +
                (isOut ? ticksSvg(m.status) : '') +
              '</div>' +
            '</div>' +
          '</div>'
        );
      });

      if (presence.customer_typing) {
        html += (
          '<div class="wa-msg-row wa-msg-row--in">' +
            '<div class="wa-bubble" style="opacity:0.88;">' +
              '<div class="wa-typing-green" style="font-size:0.75rem;">✍️ Visitor typing…</div>' +
              (presence.customer_preview ? '<div dir="auto" style="font-size:0.84rem;color:var(--wa-bubble-sub);">' + escapeHtml(presence.customer_preview) + '</div>' : '') +
            '</div>' +
          '</div>'
        );
      }

      if (presence.ai_typing) {
        html += (
          '<div class="wa-msg-row wa-msg-row--out">' +
            '<div class="wa-bubble"><div class="wa-bubble__sender">Empetix AI</div><div style="font-size:0.82rem;opacity:0.8;">Generating reply…</div></div>' +
          '</div>'
        );
      }

      if (messagesEl) {
        messagesEl.innerHTML = filtered.length || presence.customer_typing || presence.ai_typing
          ? html
          : html + '<div class="wa-date-divider">Visitor opened the chat widget — waiting for first message</div>';
      }
      if (streamEl && (forceScroll || wasNearBottom)) {
        streamEl.scrollTop = streamEl.scrollHeight;
      }
    }

    function selectVisitor(id) {
      id = Number(id);
      if (!id) return;
      state.activeId = id;
      state.msgSignature = '';
      root.classList.add('is-mobile-chat');

      var cleared = 0;
      state.contacts.forEach(function (c) {
        if (c.id === id && c.unread_count > 0) {
          cleared += Number(c.unread_count) || 0;
          c.unread_count = 0;
        }
      });
      if (cleared > 0 && unreadTotalEl) {
        updateUnreadCounters(Math.max(0, (Number(unreadTotalEl.textContent) || 0) - cleared));
      }
      renderContacts();

      var url = urls.showTpl.replace('__ID__', encodeURIComponent(id));
      fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
          if (!data || !data.ok) return;
          updateUnreadCounters(data.total_unread || 0);
          state.activeChat = data.active_chat;
          renderActiveChat(true);
        });
    }

    function pollWorkspace() {
      if (document.hidden) return;
      var params = new URLSearchParams();
      if (state.activeId) params.set('active_id', state.activeId);
      if (state.filter) params.set('filter', state.filter);
      if (state.search) params.set('q', state.search);

      fetch(urls.poll + '?' + params.toString(), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
          if (!data || !data.ok) return;
          state.contacts = data.contacts || [];
          if (typeof data.archived_count !== 'undefined') state.archivedCount = Number(data.archived_count) || 0;
          if (typeof data.archived_unread !== 'undefined') state.archivedUnread = Number(data.archived_unread) || 0;
          updateUnreadCounters(data.total_unread || 0);
          renderContacts();

          if (data.active_chat && state.activeId && data.active_chat.contact && data.active_chat.contact.id === state.activeId) {
            state.activeChat = data.active_chat;
            renderActiveChat(false);
          }
        })
        .catch(function () {});
    }

    function sendLiveReply() {
      if (!state.activeId || !inputEl) return;
      var text = inputEl.value.trim();
      if (!text) return;

      var targetId = state.activeId;
      var idemKey = state.draftKey;
      if (state.dispatchedKeys[idemKey]) return;

      state.dispatchedKeys[idemKey] = true;
      state.draftKey = makeIdempotencyKey();

      inputEl.value = '';
      inputEl.style.height = 'auto';
      inputEl.focus();

      if (!state.optimisticByChat[targetId]) state.optimisticByChat[targetId] = [];
      state.optimisticByChat[targetId].push({
        id: 'opt_' + idemKey,
        idempotency_key: idemKey,
        direction: 'outbound',
        sender_type: 'admin',
        sender_name: 'You',
        body: text,
        html: escapeHtml(text).replace(/\n/g, '<br>'),
        status: 'sending',
        time: nowTimeHHMM(),
        date_key: todayDateKey(),
        date_label: 'Today'
      });

      state.contacts.forEach(function (c) {
        if (c.id === targetId) {
          c.preview = text;
          c.last_direction = 'outbound';
          c.last_status = 'sending';
          c.time_label = nowTimeHHMM();
        }
      });

      state.msgSignature = '';
      renderContacts();
      renderActiveChat(true);

      var url = urls.sendTpl.replace('__ID__', encodeURIComponent(targetId));
      fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken(),
          'X-Idempotency-Key': idemKey
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          message: text,
          idempotency_key: idemKey,
          pause_ai: true
        })
      })
        .then(function (r) { return r.json(); })
        .then(function (body) {
          if (!body || !body.ok) {
            (state.optimisticByChat[targetId] || []).forEach(function (m) {
              if (m.idempotency_key === idemKey) {
                m.status = 'failed';
                m.error = (body && body.message) || 'Failed to send';
              }
            });
            state.msgSignature = '';
            renderActiveChat(false);
            return;
          }
          if (targetId === state.activeId && body.active_chat) {
            state.activeChat = body.active_chat;
            state.msgSignature = '';
            renderActiveChat(false);
          }
          pollWorkspace();
        })
        .catch(function () {
          (state.optimisticByChat[targetId] || []).forEach(function (m) {
            if (m.idempotency_key === idemKey) {
              m.status = 'failed';
              m.error = 'Network error';
            }
          });
          state.msgSignature = '';
          renderActiveChat(false);
        });
    }

    function patchVisitorById(targetId, payload, toastMsg) {
      if (!targetId) return;
      var url = urls.visitorTpl.replace('__ID__', encodeURIComponent(targetId));
      fetch(url, {
        method: 'PATCH',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken()
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      })
        .then(function (r) { return r.json(); })
        .then(function (body) {
          if (body && body.ok) {
            if (typeof body.archived_count !== 'undefined') state.archivedCount = Number(body.archived_count) || 0;
            if (typeof body.archived_unread !== 'undefined') state.archivedUnread = Number(body.archived_unread) || 0;
            if (typeof body.total_unread !== 'undefined') updateUnreadCounters(body.total_unread);
            if (Number(targetId) === Number(state.activeId)) {
              state.activeChat = body.active_chat;
              state.msgSignature = '';
              renderActiveChat(false);
            }
            pollWorkspace();
            if (toastMsg && window.empetixToast) window.empetixToast(toastMsg, 'success');
          }
        });
    }

    function patchVisitorField(payload, toastMsg) {
      patchVisitorById(state.activeId, payload, toastMsg);
    }

    function closeCtxMenu() {
      if (ctxMenuEl) ctxMenuEl.hidden = true;
    }

    function positionCtxMenu(x, y) {
      if (!ctxMenuEl) return;
      ctxMenuEl.hidden = false;
      ctxMenuEl.style.left = '0px';
      ctxMenuEl.style.top = '0px';
      var rect = ctxMenuEl.getBoundingClientRect();
      ctxMenuEl.style.left = Math.max(10, Math.min(x, window.innerWidth - rect.width - 10)) + 'px';
      ctxMenuEl.style.top = Math.max(10, Math.min(y, window.innerHeight - rect.height - 10)) + 'px';
    }

    function openVisitorContextMenu(visitorId, x, y) {
      if (!ctxMenuEl) return;
      var c = state.contacts.find(function (item) { return Number(item.id) === Number(visitorId); })
        || (state.activeChat && state.activeChat.contact && Number(state.activeChat.contact.id) === Number(visitorId) ? state.activeChat.contact : null);
      if (!c) return;

      var isArch = !!c.is_archived;
      var isPinned = !!c.is_pinned;
      var hasUnread = Number(c.unread_count) > 0;
      var isPaused = !!c.ai_paused;

      ctxMenuEl.innerHTML = (
        '<button type="button" class="wa-ctx-item" data-wc-ctx-act="archive" data-wc-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>' +
          '<span>' + (isArch ? 'Unarchive chat' : 'Archive chat') + '</span>' +
        '</button>' +
        (!isArch ? (
          '<button type="button" class="wa-ctx-item" data-wc-ctx-act="pin" data-wc-ctx-id="' + c.id + '">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg>' +
            '<span>' + (isPinned ? 'Unpin chat' : 'Pin chat') + '</span>' +
          '</button>'
        ) : '') +
        '<button type="button" class="wa-ctx-item" data-wc-ctx-act="unread" data-wc-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' +
          '<span>' + (hasUnread ? 'Mark as read' : 'Mark as unread') + '</span>' +
        '</button>' +
        '<button type="button" class="wa-ctx-item" data-wc-ctx-act="ai" data-wc-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M9.5 15a3.5 3.5 0 0 0 5 0"/></svg>' +
          '<span>' + (isPaused ? 'Enable AI Auto-Reply' : 'Switch to Human Mode') + '</span>' +
        '</button>' +
        '<div class="wa-ctx-sep"></div>' +
        '<button type="button" class="wa-ctx-item wa-ctx-item--danger" data-wc-ctx-act="delete" data-wc-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
          '<span>Delete chat</span>' +
        '</button>'
      );

      positionCtxMenu(x, y);
    }

    if (avatarInput) {
      avatarInput.addEventListener('change', function () {
        if (!avatarInput.files || !avatarInput.files[0] || !state.activeId) return;
        var fd = new FormData();
        fd.append('avatar', avatarInput.files[0]);
        avatarInput.value = '';

        var url = urls.visitorTpl.replace('__ID__', encodeURIComponent(state.activeId));
        fetch(url, {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
          credentials: 'same-origin',
          body: fd
        })
          .then(function (r) { return r.json(); })
          .then(function (body) {
            if (body && body.ok) {
              state.activeChat = body.active_chat;
              state.msgSignature = '';
              renderActiveChat(false);
              pollWorkspace();
            }
          });
      });
    }

    if (searchEl) {
      var st = null;
      searchEl.addEventListener('input', function () {
        state.search = searchEl.value.trim();
        if (st) clearTimeout(st);
        st = setTimeout(pollWorkspace, 220);
      });
    }

    if (inchatInput) {
      inchatInput.addEventListener('input', function () {
        state.inchatFilter = inchatInput.value.trim();
        state.msgSignature = '';
        renderActiveChat(false);
      });
    }

    if (inputEl) {
      inputEl.addEventListener('input', function () {
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(inputEl.scrollHeight, 120) + 'px';

        // Broadcast admin typing heartbeat every 2.5s while typing
        var now = Date.now();
        if (state.activeId && inputEl.value.trim() && now - state.lastTypingPingAt > 2500) {
          state.lastTypingPingAt = now;
          fetch(urls.typingTpl.replace('__ID__', encodeURIComponent(state.activeId)), {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            credentials: 'same-origin',
            body: JSON.stringify({ is_typing: true })
          }).catch(function () {});
        }
      });
      inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendLiveReply();
        }
      });
    }

    if (formEl) {
      formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        sendLiveReply();
      });
    }

    if (crmForm) {
      crmForm.addEventListener('submit', function (e) {
        e.preventDefault();
        patchVisitorField({
          visitor_name: crmName ? crmName.value : '',
          visitor_email: crmEmail ? crmEmail.value : '',
          visitor_phone: crmPhone ? crmPhone.value : '',
          status: crmStatus ? crmStatus.value : 'open',
          contact_notes: crmNotes ? crmNotes.value : ''
        }, 'Visitor details saved.');
      });
    }

    root.addEventListener('contextmenu', function (e) {
      var vRow = e.target.closest('[data-wc-select]');
      if (vRow) {
        e.preventDefault();
        openVisitorContextMenu(vRow.getAttribute('data-wc-select'), e.clientX, e.clientY);
        return;
      }

      var mRow = e.target.closest('[data-wc-msg-body]');
      if (mRow && ctxMenuEl) {
        var mTxt = mRow.getAttribute('data-wc-msg-body') || '';
        if (!mTxt) return;
        e.preventDefault();
        ctxMenuEl.innerHTML = (
          '<button type="button" class="wa-ctx-item" data-wc-ctx-copy="' + escapeHtml(mTxt) + '">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>' +
            '<span>Copy text</span>' +
          '</button>'
        );
        positionCtxMenu(e.clientX, e.clientY);
      }
    });

    document.addEventListener('click', function (e) {
      if (ctxMenuEl && !ctxMenuEl.hidden && !e.target.closest('[data-wc-row-menu]')) {
        closeCtxMenu();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeCtxMenu();
    });

    root.addEventListener('click', function (e) {
      var rMenuBtn = e.target.closest('[data-wc-row-menu]');
      if (rMenuBtn) {
        e.stopPropagation();
        var rr = rMenuBtn.getBoundingClientRect();
        openVisitorContextMenu(rMenuBtn.getAttribute('data-wc-row-menu'), rr.left, rr.bottom + 4);
        return;
      }

      if (e.target.closest('[data-wc-enter-archive]')) {
        state.prevFilter = state.filter !== 'archived' ? state.filter : 'all';
        state.filter = 'archived';
        syncArchiveUi();
        pollWorkspace();
        return;
      }

      if (e.target.closest('[data-wc-exit-archive]')) {
        state.filter = state.prevFilter || 'all';
        syncArchiveUi();
        pollWorkspace();
        return;
      }

      var ctxBtn = e.target.closest('[data-wc-ctx-act]');
      if (ctxBtn) {
        var vid = Number(ctxBtn.getAttribute('data-wc-ctx-id'));
        var vAct = ctxBtn.getAttribute('data-wc-ctx-act');
        closeCtxMenu();
        var tc = state.contacts.find(function (x) { return Number(x.id) === vid; })
          || (state.activeChat && state.activeChat.contact && Number(state.activeChat.contact.id) === vid ? state.activeChat.contact : null);
        if (!tc) return;

        if (vAct === 'archive') {
          var nextArch = !tc.is_archived;
          if (nextArch && state.filter !== 'archived') {
            state.contacts = state.contacts.filter(function (x) { return Number(x.id) !== vid; });
            state.archivedCount = (state.archivedCount || 0) + 1;
            renderContacts();
          }
          patchVisitorById(vid, { is_archived: nextArch }, nextArch ? 'Chat archived.' : 'Chat unarchived.');
        } else if (vAct === 'pin') {
          patchVisitorById(vid, { is_pinned: !tc.is_pinned }, !tc.is_pinned ? 'Chat pinned.' : 'Chat unpinned.');
        } else if (vAct === 'unread') {
          var nextUn = !(Number(tc.unread_count) > 0);
          patchVisitorById(vid, { mark_unread: nextUn }, nextUn ? 'Marked as unread.' : 'Marked as read.');
        } else if (vAct === 'ai') {
          patchVisitorById(vid, { ai_paused: !tc.ai_paused }, !tc.ai_paused ? 'Switched to Human Mode.' : 'AI Auto-Reply enabled.');
        } else if (vAct === 'delete') {
          if (!confirm('Delete this website conversation permanently?')) return;
          fetch(urls.deleteTpl.replace('__ID__', encodeURIComponent(vid)), {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
            credentials: 'same-origin'
          }).then(function () {
            if (Number(state.activeId) === vid) {
              state.activeId = null;
              state.activeChat = null;
              renderActiveChat(true);
            }
            pollWorkspace();
          });
        }
        return;
      }

      var cpBtn = e.target.closest('[data-wc-ctx-copy]');
      if (cpBtn) {
        var cpTxt = cpBtn.getAttribute('data-wc-ctx-copy') || '';
        closeCtxMenu();
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(cpTxt).then(function () {
            if (window.empetixToast) window.empetixToast('Copied to clipboard.', 'success');
          });
        }
        return;
      }

      if (e.target.closest('[data-wc-toggle-archive]') && state.activeId) {
        var curArch = state.activeChat && state.activeChat.contact && state.activeChat.contact.is_archived;
        patchVisitorField({ is_archived: !curArch }, !curArch ? 'Conversation archived.' : 'Conversation unarchived.');
        return;
      }

      var row = e.target.closest('[data-wc-select]');
      if (row) {
        selectVisitor(row.getAttribute('data-wc-select'));
        return;
      }

      var fBtn = e.target.closest('[data-wc-filter]');
      if (fBtn) {
        state.filter = fBtn.getAttribute('data-wc-filter');
        Array.prototype.forEach.call(root.querySelectorAll('[data-wc-filter]'), function (b) {
          b.classList.toggle('is-active', b === fBtn);
        });
        pollWorkspace();
        return;
      }

      if (e.target.closest('[data-wc-back]')) {
        root.classList.remove('is-mobile-chat');
        return;
      }

      if (e.target.closest('[data-wc-toggle-ai]')) {
        var cur = state.activeChat && state.activeChat.contact && state.activeChat.contact.ai_paused;
        patchVisitorField({ ai_paused: !cur }, !cur ? 'Human Takeover active (AI paused).' : 'AI Auto-Reply enabled.');
        return;
      }

      if (e.target.closest('[data-wc-toggle-pin]')) {
        var pinned = state.activeChat && state.activeChat.contact && state.activeChat.contact.is_pinned;
        patchVisitorField({ is_pinned: !pinned }, !pinned ? 'Conversation pinned.' : 'Conversation unpinned.');
        return;
      }

      if (e.target.closest('[data-wc-toggle-drawer]')) {
        var open = drawerEl.hidden;
        drawerEl.hidden = !open;
        root.classList.toggle('has-drawer', open);
        return;
      }

      if (e.target.closest('[data-wc-close-drawer]')) {
        drawerEl.hidden = true;
        root.classList.remove('has-drawer');
        return;
      }

      if (e.target.closest('[data-wc-toggle-msg-search]')) {
        inchatSearchWrap.hidden = !inchatSearchWrap.hidden;
        if (!inchatSearchWrap.hidden && inchatInput) inchatInput.focus();
        return;
      }

      if (e.target.closest('[data-wc-close-msg-search]')) {
        inchatSearchWrap.hidden = true;
        state.inchatFilter = '';
        if (inchatInput) inchatInput.value = '';
        state.msgSignature = '';
        renderActiveChat(false);
        return;
      }

      if (e.target.closest('[data-wc-btn-emoji]')) {
        emojiPopover.hidden = !emojiPopover.hidden;
        return;
      }

      var emBtn = e.target.closest('[data-wc-emoji]');
      if (emBtn && inputEl) {
        inputEl.value += emBtn.getAttribute('data-wc-emoji');
        emojiPopover.hidden = true;
        inputEl.focus();
        return;
      }

      if (e.target.closest('[data-wc-delete-chat]') && state.activeId) {
        if (!confirm('Delete this website conversation permanently?')) return;
        var delUrl = urls.deleteTpl.replace('__ID__', encodeURIComponent(state.activeId));
        fetch(delUrl, {
          method: 'DELETE',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
          credentials: 'same-origin'
        }).then(function () {
          state.activeId = null;
          state.activeChat = null;
          renderActiveChat(true);
          pollWorkspace();
        });
      }
    });

    var pollTimer = setInterval(pollWorkspace, 1800);
    document.addEventListener('livewire:navigating', function () {
      clearInterval(pollTimer);
    }, { once: true });
  }

  function initAll() {
    var el = document.querySelector('[data-webchat-root]');
    if (el) mountWebsiteChat(el);
  }

  document.addEventListener('DOMContentLoaded', initAll);
  document.addEventListener('livewire:navigated', initAll);
})();
