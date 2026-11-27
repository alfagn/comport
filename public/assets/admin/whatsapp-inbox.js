/**
 * Empetix WhatsApp Business Inbox Studio Controller.
 * Authentic WhatsApp Web System Design:
 * - Zero-latency optimistic send (0ms bubble insertion with WhatsApp Clock SVG `🕒` -> `✓` -> `✓✓` -> blue `✓✓`)
 * - Strict client + server per-message idempotency (`X-Idempotency-Key` UUID)
 * - Unread-only WhatsApp green badges, bold unread names, green timestamps & numbers
 * - Contact profile photos, `@username` handles, and formatted phone numbers
 * - Browser Ogg/WebM Opus voice note recording, waveform player, media lightbox & reactions
 */
(function () {
  if (window.__empetixWaInboxBound) return;
  window.__empetixWaInboxBound = true;

  var EMOJIS = [
    '😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','😉','😍','🥰',
    '😘','😎','🤩','🥳','🤔','🤗','🙌','👏','👍','👎','🤝','🙏','❤️','🔥',
    '✨','🎉','💯','✅','⚡','🚀','💼','📄','📍','📞','💬','👋','🇪🇬','🇸🇦'
  ];

  var QUICK_REACTIONS = ['👍', '❤️', '😂', '😮', '😢', '🙏'];

  function csrfToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function makeIdempotencyKey() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
      return 'wa_' + window.crypto.randomUUID();
    }
    return 'wa_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 12);
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

  function formatClientTextHtml(text) {
    var escaped = escapeHtml(text || '');
    escaped = escaped.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    escaped = escaped.replace(/(^|\s)\*([^*\n]+)\*(?=\s|$)/g, '$1<strong>$2</strong>');
    escaped = escaped.replace(/(https?:\/\/[^\s<]+)/gi, '<a href="$1" target="_blank" rel="noopener noreferrer" style="color:var(--wa-green);font-weight:600;text-decoration:underline;">$1</a>');
    return escaped.replace(/\n/g, '<br>');
  }

  function formatDuration(sec) {
    sec = Math.max(0, Math.round(Number(sec) || 0));
    var m = Math.floor(sec / 60);
    var s = sec % 60;
    return m + ':' + (s < 10 ? '0' : '') + s;
  }

  /**
   * Authentic WhatsApp delivery status icons:
   * - `sending`: WhatsApp Clock SVG (`🕒`)
   * - `sent`: Single grey check (`✓`)
   * - `delivered`: Double grey check (`✓✓`)
   * - `read`: Double blue `#53BDEB` check (`✓✓`)
   * - `failed`: Red warning (`⚠`)
   */
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
      return '<span class="wa-ticks is-failed" title="Delivery failed">⚠</span>';
    }
    if (status === 'delivered' || status === 'read') {
      var cls = status === 'read' ? 'wa-ticks is-read' : 'wa-ticks';
      return '<span class="' + cls + '" title="' + escapeHtml(status) + '">' +
        '<svg width="16" height="11" viewBox="0 0 16 11" fill="none"><path d="M11.07.66 4.88 6.85 2.3 4.27.9 5.68l3.98 3.98L12.48 2.07 11.07.66Zm3.98 0L8.86 6.85l-.93-.93-1.41 1.41 2.34 2.33L16.46 2.07 15.05.66Z" fill="currentColor"/></svg>' +
      '</span>';
    }
    return '<span class="wa-ticks" title="Sent">' +
      '<svg width="12" height="11" viewBox="0 0 12 11" fill="none"><path d="M10.3.66 4.1 6.85 1.53 4.27.12 5.68 4.1 9.66 11.71 2.07 10.3.66Z" fill="currentColor"/></svg>' +
    '</span>';
  }

  /**
   * Authentic WhatsApp Avatar: real profile photo when available, or WhatsApp silhouette + monogram.
   */
  function avatarInnerHtml(contact) {
    if (contact && contact.avatar_url) {
      return '<img src="' + escapeHtml(contact.avatar_url) + '" alt="' + escapeHtml(contact.name || 'Contact') + '" class="wa-avatar__img" loading="lazy"/>';
    }
    var initial = contact && contact.initial ? contact.initial : '';
    if (initial && initial !== '+' && !/^\d$/.test(initial)) {
      return '<span class="wa-avatar__initial">' + escapeHtml(initial) + '</span>';
    }
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" opacity="0.92"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5Zm0 2c-3.34 0-10 1.68-10 5v2h20v-2c0-3.32-6.66-5-10-5Z"/></svg>';
  }

  function applyAvatarToElement(el, contact) {
    if (!el || !contact) return;
    if (contact.avatar_url) {
      el.style.background = 'transparent';
      el.classList.add('has-photo');
    } else {
      el.style.background = contact.avatar_color || '#6A7175';
      el.classList.remove('has-photo');
    }
    el.innerHTML = avatarInnerHtml(contact);
  }

  function pinIconSvg() {
    return '<span class="wa-pin-mark" title="Pinned">' +
      '<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2Z"/></svg>' +
    '</span>';
  }

  function mountWhatsappInbox(root) {
    if (!root || root.__waMounted) return;
    root.__waMounted = true;

    var urls = {
      poll: root.getAttribute('data-url-poll'),
      start: root.getAttribute('data-url-start'),
      showTpl: root.getAttribute('data-url-show-tpl'),
      sendTpl: root.getAttribute('data-url-send-tpl'),
      contactTpl: root.getAttribute('data-url-contact-tpl'),
      deleteTpl: root.getAttribute('data-url-delete-tpl'),
      reactTpl: root.getAttribute('data-url-react-tpl'),
      delMsgTpl: root.getAttribute('data-url-del-msg-tpl'),
      notifications: root.getAttribute('data-url-notifications')
    };

    var contactsEl = root.querySelector('[data-wa-contacts]');
    var archiveHeadEl = root.querySelector('[data-wa-archive-head]');
    var filtersBarEl = root.querySelector('[data-wa-filters-bar]');
    var ctxMenuEl = root.querySelector('[data-wa-ctx-menu]');
    var searchEl = root.querySelector('[data-wa-search]');
    var unreadTotalEl = root.querySelector('[data-wa-unread-total]');
    var welcomeEl = root.querySelector('[data-wa-welcome]');
    var convEl = root.querySelector('[data-wa-conversation]');
    var streamEl = root.querySelector('[data-wa-stream]');
    var messagesEl = root.querySelector('[data-wa-messages]');

    var headAvatar = root.querySelector('[data-wa-head-avatar]');
    var headName = root.querySelector('[data-wa-head-name]');
    var headUsername = root.querySelector('[data-wa-head-username]');
    var headSub = root.querySelector('[data-wa-head-sub]');
    var toggleAiBtn = root.querySelector('[data-wa-toggle-ai]');
    var toggleAiLabel = root.querySelector('[data-wa-toggle-ai-label]');
    var toggleArchiveBtn = root.querySelector('[data-wa-toggle-archive]');
    var togglePinBtn = root.querySelector('[data-wa-toggle-pin]');

    var inchatSearchWrap = root.querySelector('[data-wa-inchat-search]');
    var inchatInput = root.querySelector('[data-wa-inchat-input]');

    var replyBar = root.querySelector('[data-wa-reply-bar]');
    var replySender = root.querySelector('[data-wa-reply-sender]');
    var replyPreview = root.querySelector('[data-wa-reply-preview]');
    var stagedEl = root.querySelector('[data-wa-staged-files]');
    var emojiPopover = root.querySelector('[data-wa-emoji-popover]');
    var attachMenu = root.querySelector('[data-wa-attach-menu]');
    var fileMediaInput = root.querySelector('[data-wa-file-media]');
    var fileDocInput = root.querySelector('[data-wa-file-doc]');

    var formEl = root.querySelector('[data-wa-form]');
    var inputEl = root.querySelector('[data-wa-input]');
    var micBtn = root.querySelector('[data-wa-btn-mic]');
    var sendBtn = root.querySelector('[data-wa-btn-send]');

    var recBar = root.querySelector('[data-wa-recorder-bar]');
    var recTimerEl = root.querySelector('[data-wa-rec-timer]');
    var recBarsEl = root.querySelector('[data-wa-rec-bars]');

    var drawerEl = root.querySelector('[data-wa-drawer]');
    var drawerAvatar = root.querySelector('[data-wa-drawer-avatar]');
    var avatarInput = root.querySelector('[data-wa-avatar-input]');
    var drawerTitle = root.querySelector('[data-wa-drawer-title]');
    var drawerUsername = root.querySelector('[data-wa-drawer-username]');
    var drawerPhone = root.querySelector('[data-wa-drawer-phone]');
    var drawerMsgCount = root.querySelector('[data-wa-drawer-msg-count]');
    var drawerWindow = root.querySelector('[data-wa-drawer-window]');
    var crmForm = root.querySelector('[data-wa-crm-form]');
    var crmName = root.querySelector('[data-wa-crm-name]');
    var crmUsername = root.querySelector('[data-wa-crm-username]');
    var crmEmail = root.querySelector('[data-wa-crm-email]');
    var crmStatus = root.querySelector('[data-wa-crm-status]');
    var crmNotes = root.querySelector('[data-wa-crm-notes]');
    var sharedMediaEl = root.querySelector('[data-wa-shared-media]');

    var lightboxEl = root.querySelector('[data-wa-lightbox]');
    var lightboxImg = root.querySelector('[data-wa-lightbox-img]');
    var lightboxCap = root.querySelector('[data-wa-lightbox-caption]');
    var lightboxDl = root.querySelector('[data-wa-lightbox-download]');

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
      replyTo: null,
      stagedFiles: [],
      draftKey: makeIdempotencyKey(),
      dispatchedKeys: {},
      optimisticByChat: {},
      inFlightCount: 0,
      msgSignature: '',
      rec: {
        active: false,
        recorder: null,
        stream: null,
        chunks: [],
        startedAt: 0,
        timerId: null,
        audioCtx: null,
        rafId: null
      }
    };

    if (emojiPopover) {
      emojiPopover.innerHTML = EMOJIS.map(function (em) {
        return '<button type="button" data-wa-emoji="' + em + '">' + em + '</button>';
      }).join('');
    }

    var initScript = root.querySelector('[data-wa-initial]');
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
      Array.prototype.forEach.call(document.querySelectorAll('[data-sidebar-wa-badge]'), function (b) {
        b.textContent = totalUnread;
        b.style.display = totalUnread > 0 ? '' : 'none';
      });
    }

    function syncComposerButtons() {
      var hasContent = ((inputEl && inputEl.value.trim().length > 0) || state.stagedFiles.length > 0);
      if (micBtn) micBtn.hidden = hasContent;
      if (sendBtn) sendBtn.hidden = !hasContent;
    }

    /**
     * Render Left Contacts Rail with 100% Authentic WhatsApp Web System Design:
     * - Avatar photo (or silhouette/monogram)
     * - Bold name + green timestamp + green phone & green circular unread badge ONLY when `unread_count > 0`
     * - Zero numeric badge when `unread_count === 0`
     * - Outbound status clock/ticks (`🕒` / `✓` / `✓✓` / blue `✓✓`) in preview line
     */
    function syncArchiveModeUi() {
      var inArch = state.filter === 'archived';
      if (archiveHeadEl) archiveHeadEl.hidden = !inArch;
      if (filtersBarEl) filtersBarEl.hidden = inArch;
    }

    function renderContacts() {
      if (!contactsEl) return;
      syncArchiveModeUi();

      var topArchivedRow = '';
      if (state.filter !== 'archived' && !state.search && state.archivedCount > 0) {
        var archBadge = state.archivedUnread > 0
          ? '<span class="wa-unread-badge">' + state.archivedUnread + '</span>'
          : '<span class="wa-archived-row__count">' + state.archivedCount + '</span>';
        topArchivedRow = (
          '<div class="wa-archived-row" data-wa-enter-archive>' +
            '<span class="wa-archived-row__ico">' +
              '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>' +
            '</span>' +
            '<span class="wa-archived-row__label">Archived</span>' +
            archBadge +
          '</div>'
        );
      }

      if (!state.contacts.length) {
        var emptyMsg = state.filter === 'archived'
          ? 'No archived conversations.'
          : 'No WhatsApp conversations match this view yet. Click <strong>+</strong> above to start a chat.';
        contactsEl.innerHTML = topArchivedRow + '<div class="wa-contact-empty">' + emptyMsg + '</div>';
        return;
      }

      contactsEl.innerHTML = topArchivedRow + state.contacts.map(function (c) {
        var active = c.id === state.activeId ? ' is-active' : '';
        var hasUnread = Number(c.unread_count) > 0;
        var unreadCls = hasUnread ? ' has-unread' : '';

        var avatarStyle = c.avatar_url ? 'background:transparent;' : ('background:' + escapeHtml(c.avatar_color || '#6A7175') + ';');
        var avatarCls = c.avatar_url ? 'wa-avatar has-photo' : 'wa-avatar';

        var showPhoneLine = c.phone && c.name && c.name.replace(/\s+/g, '') !== c.phone.replace(/\s+/g, '');
        var handleHtml = c.username ? '<span class="wa-contact-row__handle">' + escapeHtml(c.username) + '</span>' : '';
        var subIdentityHtml = showPhoneLine
          ? '<div class="wa-contact-row__identity">' +
              '<span class="wa-contact-row__phone" dir="ltr">' + escapeHtml(c.phone) + '</span>' +
              handleHtml +
            '</div>'
          : (handleHtml ? '<div class="wa-contact-row__identity">' + handleHtml + '</div>' : '');

        var previewTicks = '';
        if (!c.ai_typing && c.last_direction === 'outbound') {
          previewTicks = ticksSvg(c.last_status || 'sent') + ' ';
        }

        var previewContent = c.ai_typing
          ? '<span class="wa-typing-green">typing…</span>'
          : (previewTicks + '<span>' + escapeHtml(c.preview || '') + '</span>');

        return (
          '<div class="wa-contact-row' + active + unreadCls + '" data-wa-select-contact="' + c.id + '">' +
            '<div class="' + avatarCls + '" style="' + avatarStyle + '">' + avatarInnerHtml(c) + '</div>' +
            '<div class="wa-contact-row__main">' +
              '<div class="wa-contact-row__top">' +
                '<span class="wa-contact-row__name" dir="auto">' + escapeHtml(c.name) + '</span>' +
                '<span class="wa-contact-row__time">' + escapeHtml(c.time_label || '') + '</span>' +
              '</div>' +
              subIdentityHtml +
              '<div class="wa-contact-row__bottom">' +
                '<span class="wa-contact-row__preview" dir="auto">' + previewContent + '</span>' +
                '<span class="wa-contact-row__badges">' +
                  (c.is_pinned ? pinIconSvg() : '') +
                  (hasUnread ? '<span class="wa-unread-badge">' + (c.unread_count > 99 ? '99+' : c.unread_count) + '</span>' : '') +
                  '<button type="button" class="wa-row-menu-btn" data-wa-row-menu="' + c.id + '" title="Chat options" aria-label="Chat options">' +
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>' +
                  '</button>' +
                '</span>' +
              '</div>' +
            '</div>' +
          '</div>'
        );
      }).join('');
    }

    /**
     * Merge server messages with any still-pending local 0ms optimistic messages,
     * deduplicating seamlessly by `idempotency_key` so there is never a jump or double render.
     */
    function getMergedMessages(chatId, serverMsgs) {
      serverMsgs = serverMsgs || [];
      var optList = state.optimisticByChat[chatId] || [];
      if (!optList.length) return serverMsgs;

      var seenKeys = {};
      serverMsgs.forEach(function (m) {
        if (m.idempotency_key) {
          seenKeys[m.idempotency_key] = true;
        }
      });

      var remainingOpt = optList.filter(function (opt) {
        if (opt.idempotency_key && seenKeys[opt.idempotency_key]) {
          if (opt._blobUrl) {
            try { URL.revokeObjectURL(opt._blobUrl); } catch (e) {}
          }
          return false;
        }
        return true;
      });

      state.optimisticByChat[chatId] = remainingOpt;

      return serverMsgs.concat(remainingOpt);
    }

    function computeMessagesSignature(msgs, presence, contact) {
      var parts = (msgs || []).map(function (m) {
        return m.id + ':' + m.status + ':' + (m.reaction_emoji || '') + ':' + (m.error || '');
      });
      parts.push('ai:' + (presence && presence.ai_typing ? '1' : '0'));
      parts.push('q:' + state.inchatFilter);
      parts.push('av:' + (contact && contact.avatar_url ? contact.avatar_url : ''));
      return parts.join('|');
    }

    function renderMediaInner(m) {
      if (m.type === 'image' || m.type === 'sticker') {
        if (!m.media_url) return '';
        return '<img src="' + escapeHtml(m.media_url) + '" alt="Photo" class="wa-media-img" data-wa-zoom="' + escapeHtml(m.media_url) + '" data-wa-dl="' + escapeHtml(m.download_url || m.media_url) + '" data-wa-cap="' + escapeHtml(m.body || m.media_filename || 'WhatsApp Photo') + '" loading="lazy"/>';
      }

      if (m.type === 'video' && m.media_url) {
        return '<video src="' + escapeHtml(m.media_url) + '" controls preload="metadata" class="wa-media-video"></video>';
      }

      if ((m.type === 'voice' || m.type === 'audio') && m.media_url) {
        var bars = '';
        var heights = [35,60,45,80,55,90,40,70,85,50,65,95,55,40,75,60,85,45,70,50,65,40];
        for (var i = 0; i < heights.length; i++) {
          bars += '<span style="height:' + heights[i] + '%"></span>';
        }
        return (
          '<div class="wa-voice-player" data-wa-voice-player>' +
            '<audio src="' + escapeHtml(m.media_url) + '" preload="metadata"></audio>' +
            '<button type="button" class="wa-voice-play" data-wa-voice-toggle aria-label="Play voice message">' +
              '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"/></svg>' +
            '</button>' +
            '<div class="wa-voice-bars" data-wa-voice-scrub>' + bars + '</div>' +
            '<span class="wa-voice-dur" data-wa-voice-dur style="font-size:0.72rem;color:var(--wa-bubble-sub);min-width:30px;">' + (m.media_duration ? formatDuration(m.media_duration) : '0:00') + '</span>' +
            '<button type="button" class="wa-voice-speed" data-wa-voice-speed>1x</button>' +
          '</div>'
        );
      }

      if (m.type === 'document') {
        var fname = m.media_filename || 'Document';
        var ext = (fname.split('.').pop() || 'FILE').slice(0, 4);
        var href = m.download_url || m.media_url || '#';
        return (
          '<a href="' + escapeHtml(href) + '" target="_blank" rel="noopener" class="wa-doc-card">' +
            '<span class="wa-doc-card__ext">' + escapeHtml(ext) + '</span>' +
            '<span class="wa-doc-card__info">' +
              '<span class="wa-doc-card__name">' + escapeHtml(fname) + '</span>' +
              '<span class="wa-doc-card__size">' + escapeHtml(m.media_size_human || 'Download file') + '</span>' +
            '</span>' +
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
          '</a>'
        );
      }

      if (m.type === 'location' && m.location) {
        var lat = m.location.latitude || 0;
        var lng = m.location.longitude || 0;
        var mapUrl = 'https://www.google.com/maps?q=' + encodeURIComponent(lat + ',' + lng);
        return (
          '<a href="' + mapUrl + '" target="_blank" rel="noopener" class="wa-loc-card">' +
            '<span style="font-size:1.4rem;">📍</span>' +
            '<span>' +
              '<strong style="display:block;font-size:0.83rem;">' + escapeHtml(m.location.name || 'Shared Location') + '</strong>' +
              '<span style="font-size:0.73rem;color:var(--wa-bubble-sub);">' + escapeHtml(m.location.address || (lat + ', ' + lng)) + ' &middot; Open Map &rarr;</span>' +
            '</span>' +
          '</a>'
        );
      }

      return '';
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

      applyAvatarToElement(headAvatar, c);
      if (headName) headName.textContent = c.name;
      if (headUsername) {
        if (c.username) {
          headUsername.hidden = false;
          headUsername.textContent = c.username;
        } else {
          headUsername.hidden = true;
        }
      }

      if (headSub) {
        if (presence.ai_typing) {
          headSub.innerHTML = '<span class="wa-typing-green">typing…</span>';
        } else {
          var subParts = [];
          if (c.phone) subParts.push('<span dir="ltr">' + escapeHtml(c.phone) + '</span>');
          subParts.push(c.within_24h ? '<span style="color:var(--wa-green);font-weight:600;">● 24h Window Open</span>' : 'Template Mode (&gt;24h)');
          headSub.innerHTML = subParts.join(' &middot; ');
        }
      }

      if (toggleAiBtn && toggleAiLabel) {
        toggleAiBtn.classList.toggle('is-human', !!c.ai_paused);
        toggleAiLabel.textContent = c.ai_paused ? 'Human Mode' : 'AI Auto';
      }
      if (toggleArchiveBtn) {
        toggleArchiveBtn.classList.toggle('is-active', !!c.is_archived);
        toggleArchiveBtn.title = c.is_archived ? 'Unarchive conversation' : 'Archive conversation';
      }
      if (togglePinBtn) {
        togglePinBtn.classList.toggle('is-active', !!c.is_pinned);
      }

      // Sync Right Contact Info Drawer
      applyAvatarToElement(drawerAvatar, c);
      if (drawerTitle) drawerTitle.textContent = c.name;
      if (drawerUsername) {
        if (c.username) {
          drawerUsername.hidden = false;
          drawerUsername.textContent = c.username;
        } else {
          drawerUsername.hidden = true;
        }
      }
      if (drawerPhone) drawerPhone.textContent = c.phone || '';
      if (drawerMsgCount) drawerMsgCount.textContent = c.total_messages || 0;
      if (drawerWindow) drawerWindow.textContent = c.within_24h ? 'Active' : 'Closed';
      if (crmName && document.activeElement !== crmName) crmName.value = c.visitor_name || '';
      if (crmUsername && document.activeElement !== crmUsername) crmUsername.value = c.username || '';
      if (crmEmail && document.activeElement !== crmEmail) crmEmail.value = c.email || '';
      if (crmStatus && document.activeElement !== crmStatus) crmStatus.value = c.status || 'open';
      if (crmNotes && document.activeElement !== crmNotes) crmNotes.value = c.contact_notes || '';

      if (sharedMediaEl) {
        var mediaItems = state.activeChat.shared_media || [];
        if (!mediaItems.length) {
          sharedMediaEl.innerHTML = '<p class="wa-muted-note">No photos or files shared yet.</p>';
        } else {
          sharedMediaEl.innerHTML = mediaItems.map(function (m) {
            if (m.type === 'image') {
              return '<a href="' + escapeHtml(m.media_url) + '" target="_blank" class="wa-shared-thumb"><img src="' + escapeHtml(m.media_url) + '" alt=""/></a>';
            }
            var label = m.type === 'voice' ? '🎤 Voice' : ('📄 ' + (m.media_filename || m.type).slice(0, 8));
            return '<a href="' + escapeHtml(m.download_url || m.media_url) + '" target="_blank" class="wa-shared-thumb">' + escapeHtml(label) + '</a>';
          }).join('');
        }
      }

      var allMsgs = getMergedMessages(c.id, state.activeChat.messages || []);
      var sig = computeMessagesSignature(allMsgs, presence, c);
      if (!forceScroll && sig === state.msgSignature) {
        return;
      }
      var wasNearBottom = !streamEl || (streamEl.scrollHeight - streamEl.scrollTop - streamEl.clientHeight < 140);
      state.msgSignature = sig;

      var qLower = state.inchatFilter.toLowerCase();
      var filtered = qLower
        ? allMsgs.filter(function (m) { return (m.body || '').toLowerCase().indexOf(qLower) !== -1 || (m.media_filename || '').toLowerCase().indexOf(qLower) !== -1; })
        : allMsgs;

      var lastDateKey = '';
      var html = (
        '<div class="wa-e2ee-banner">' +
          '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2Zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2Zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2Z"/></svg>' +
          '<span>Messages and media are end-to-end TLS protected &amp; AES-256 encrypted at rest.</span>' +
        '</div>'
      );

      filtered.forEach(function (m) {
        if (m.date_key && m.date_key !== lastDateKey) {
          lastDateKey = m.date_key;
          html += '<div class="wa-date-divider">' + escapeHtml(m.date_label) + '</div>';
        }

        var isOut = m.direction === 'outbound';
        var isOpt = String(m.id || '').indexOf('opt_') === 0;
        var rowCls = (isOut ? 'wa-msg-row wa-msg-row--out' : 'wa-msg-row wa-msg-row--in') + (m.status === 'sending' ? ' is-sending' : '');

        var quotedHtml = '';
        if (m.quoted) {
          quotedHtml = (
            '<div class="wa-quoted" data-wa-jump-wamid="' + escapeHtml(m.quoted.wamid || '') + '">' +
              '<strong>' + escapeHtml(m.quoted.sender || 'Message') + '</strong>' +
              '<span>' + escapeHtml(m.quoted.preview || '') + '</span>' +
            '</div>'
          );
        }

        var hoverBar = '';
        if (!isOpt) {
          var reactionBtns = QUICK_REACTIONS.map(function (em) {
            return '<button type="button" data-wa-react="' + m.id + '" data-wa-emoji-val="' + em + '" title="React ' + em + '">' + em + '</button>';
          }).join('');

          hoverBar = (
            '<div class="wa-msg-actions">' +
              reactionBtns +
              (m.wamid ? '<button type="button" data-wa-reply-btn="' + escapeHtml(m.wamid) + '" data-wa-reply-who="' + escapeHtml(isOut ? 'You' : c.name) + '" data-wa-reply-txt="' + escapeHtml((m.body || m.type || 'Message').slice(0, 90)) + '" title="Reply">↩</button>' : '') +
            '</div>'
          );
        }

        html += (
          '<div class="' + rowCls + '" data-wa-msg-id="' + escapeHtml(m.id) + '" data-wa-wamid="' + escapeHtml(m.wamid || '') + '">' +
            hoverBar +
            '<div class="wa-bubble">' +
              (isOut && m.sender_name ? '<div class="wa-bubble__sender">' + escapeHtml(m.sender_name) + '</div>' : '') +
              quotedHtml +
              renderMediaInner(m) +
              (m.html ? '<div class="wa-bubble__text" dir="auto">' + m.html + '</div>' : '') +
              (m.error ? '<div class="wa-bubble__err">⚠ ' + escapeHtml(m.error) + '</div>' : '') +
              '<div class="wa-bubble__foot">' +
                '<span>' + escapeHtml(m.time || '') + '</span>' +
                (isOut ? ticksSvg(m.status) : '') +
              '</div>' +
            '</div>' +
            (m.reaction_emoji ? '<span class="wa-reaction-pill" data-wa-react="' + m.id + '" data-wa-emoji-val="" title="Click to remove reaction">' + escapeHtml(m.reaction_emoji) + '</span>' : '') +
          '</div>'
        );
      });

      if (presence.ai_typing) {
        html += (
          '<div class="wa-msg-row wa-msg-row--out">' +
            '<div class="wa-bubble"><div class="wa-bubble__sender">Empetix AI</div><div style="font-size:0.82rem;opacity:0.8;">Generating reply…</div></div>' +
          '</div>'
        );
      }

      if (messagesEl) {
        messagesEl.innerHTML = filtered.length || presence.ai_typing
          ? html
          : html + '<div class="wa-date-divider">No messages in this conversation yet</div>';
      }
      if (streamEl && (forceScroll || wasNearBottom)) {
        streamEl.scrollTop = streamEl.scrollHeight;
      }
    }

    function selectContact(id) {
      id = Number(id);
      if (!id) return;
      state.activeId = id;
      state.msgSignature = '';
      clearReplyTo();
      root.classList.add('is-mobile-chat');

      // Optimistically clear unread badge on selected contact immediately
      var clearedCount = 0;
      state.contacts.forEach(function (c) {
        if (c.id === id && c.unread_count > 0) {
          clearedCount += Number(c.unread_count) || 0;
          c.unread_count = 0;
        }
      });
      if (clearedCount > 0 && unreadTotalEl) {
        var nextTotal = Math.max(0, (Number(unreadTotalEl.textContent) || 0) - clearedCount);
        updateUnreadCounters(nextTotal);
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
      if (document.hidden || state.rec.active) return;

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

    function setReplyTo(wamid, sender, preview) {
      state.replyTo = { wamid: wamid, sender: sender, preview: preview };
      if (replyBar) replyBar.hidden = false;
      if (replySender) replySender.textContent = sender;
      if (replyPreview) replyPreview.textContent = preview;
      if (inputEl) inputEl.focus();
    }

    function clearReplyTo() {
      state.replyTo = null;
      if (replyBar) replyBar.hidden = true;
    }

    function addStagedFiles(fileList) {
      if (!fileList || !fileList.length) return;
      Array.prototype.forEach.call(fileList, function (f) {
        if (state.stagedFiles.length >= 5) return;
        state.stagedFiles.push(f);
      });
      renderStagedFiles();
      syncComposerButtons();
    }

    function renderStagedFiles() {
      if (!stagedEl) return;
      if (!state.stagedFiles.length) {
        stagedEl.hidden = true;
        stagedEl.innerHTML = '';
        return;
      }
      stagedEl.hidden = false;
      stagedEl.innerHTML = state.stagedFiles.map(function (f, i) {
        return (
          '<span class="wa-staged-chip">' +
            '<span>📎 ' + escapeHtml(f.name) + '</span>' +
            '<button type="button" data-wa-rm-staged="' + i + '">&times;</button>' +
          '</span>'
        );
      }).join('');
    }

    function pushOptimisticBubble(chatId, optMsg, sidebarPreview) {
      if (!state.optimisticByChat[chatId]) {
        state.optimisticByChat[chatId] = [];
      }
      state.optimisticByChat[chatId].push(optMsg);

      // Move active contact to top (respecting pinned order) & show 🕒 status immediately
      state.contacts.forEach(function (c) {
        if (c.id === chatId) {
          c.preview = sidebarPreview;
          c.last_direction = 'outbound';
          c.last_status = 'sending';
          c.time_label = optMsg.time;
        }
      });

      state.msgSignature = '';
      renderContacts();
      renderActiveChat(true);
    }

    function markOptimisticFailed(chatId, idemKey, errMsg) {
      var list = state.optimisticByChat[chatId] || [];
      list.forEach(function (m) {
        if (m.idempotency_key === idemKey) {
          m.status = 'failed';
          m.error = errMsg || 'Failed to send';
        }
      });
      state.msgSignature = '';
      renderActiveChat(false);
    }

    /**
     * Zero-Latency WhatsApp Send Pipeline:
     * 1. Enforces per-draft `idempotency_key` so rapid double-clicks in the same millisecond NEVER duplicate
     * 2. Clears composer in 0ms (never blocks typing/sending consecutive messages!)
     * 3. Renders message immediately with WhatsApp Clock SVG (`🕒`)
     * 4. Reconciles seamlessly with server `sent` (`✓`) -> `delivered` (`✓✓`) -> `read` (blue `✓✓`)
     */
    function sendComposerPayload(extraFormData, optimisticMeta) {
      if (!state.activeId) return;
      var targetChatId = state.activeId;
      var idemKey = extraFormData ? makeIdempotencyKey() : state.draftKey;

      // Client-side idempotency guard: drop rapid double-clicks on the exact same draft
      if (state.dispatchedKeys[idemKey]) {
        return;
      }

      var fd = extraFormData || new FormData();
      var quotedSnapshot = state.replyTo ? {
        wamid: state.replyTo.wamid,
        sender: state.replyTo.sender,
        preview: state.replyTo.preview
      } : null;

      var optType = (optimisticMeta && optimisticMeta.type) || 'text';
      var optBody = (optimisticMeta && optimisticMeta.body) || '';
      var optHtml = '';
      var optBlobUrl = (optimisticMeta && optimisticMeta.blobUrl) || null;
      var optDuration = (optimisticMeta && optimisticMeta.duration) || null;
      var optFilename = (optimisticMeta && optimisticMeta.filename) || null;
      var optLocation = (optimisticMeta && optimisticMeta.location) || null;
      var sidebarPreview = 'Message';

      if (!extraFormData) {
        var text = inputEl ? inputEl.value.trim() : '';
        var filesSnapshot = state.stagedFiles.slice();
        if (!text && !filesSnapshot.length) return;

        // Rotate draft key & clear composer IMMEDIATELY (0ms non-blocking UX!)
        state.dispatchedKeys[idemKey] = true;
        state.draftKey = makeIdempotencyKey();

        if (inputEl) {
          inputEl.value = '';
          inputEl.style.height = 'auto';
          inputEl.focus();
        }
        state.stagedFiles = [];
        renderStagedFiles();
        clearReplyTo();
        syncComposerButtons();

        if (text) fd.append('message', text);
        filesSnapshot.forEach(function (f) {
          fd.append('attachments[]', f);
        });

        if (filesSnapshot.length > 0) {
          var firstFile = filesSnapshot[0];
          var fMime = (firstFile.type || '').toLowerCase();
          optFilename = firstFile.name;
          if (fMime.indexOf('image/') === 0) {
            optType = 'image';
            optBlobUrl = URL.createObjectURL(firstFile);
            sidebarPreview = text ? ('📷 ' + text) : '📷 Photo';
          } else if (fMime.indexOf('video/') === 0) {
            optType = 'video';
            optBlobUrl = URL.createObjectURL(firstFile);
            sidebarPreview = text ? ('🎥 ' + text) : '🎥 Video';
          } else {
            optType = 'document';
            sidebarPreview = '📄 ' + firstFile.name;
          }
          optBody = text;
          optHtml = text ? formatClientTextHtml(text) : '';
        } else {
          optType = 'text';
          optBody = text;
          optHtml = formatClientTextHtml(text);
          sidebarPreview = text;
        }
      } else {
        state.dispatchedKeys[idemKey] = true;
        clearReplyTo();
        if (optType === 'voice') {
          sidebarPreview = '🎤 Voice message';
        } else if (optType === 'location') {
          sidebarPreview = '📍 ' + ((optLocation && optLocation.name) || 'Shared location');
        } else if (optType === 'template') {
          optHtml = '<span style="display:inline-flex;align-items:center;gap:6px;font-size:0.82rem;font-weight:600;color:var(--wa-green);">📋 Template sent: <strong>' + escapeHtml(optBody) + '</strong></span>';
          sidebarPreview = '📋 Template: ' + optBody;
        }
      }

      fd.append('idempotency_key', idemKey);
      if (quotedSnapshot && quotedSnapshot.wamid) {
        fd.append('reply_to_wamid', quotedSnapshot.wamid);
      }

      // Render 0ms optimistic bubble with WhatsApp Clock SVG (`🕒`)
      pushOptimisticBubble(targetChatId, {
        id: 'opt_' + idemKey,
        idempotency_key: idemKey,
        wamid: null,
        quoted: quotedSnapshot,
        direction: 'outbound',
        sender_type: 'admin',
        sender_name: null,
        type: optType,
        body: optBody,
        html: optHtml,
        media_url: optBlobUrl,
        download_url: optBlobUrl,
        media_filename: optFilename,
        media_duration: optDuration,
        location: optLocation,
        status: 'sending',
        time: nowTimeHHMM(),
        date_key: todayDateKey(),
        date_label: 'Today',
        _blobUrl: optBlobUrl
      }, sidebarPreview);

      state.inFlightCount++;
      var url = urls.sendTpl.replace('__ID__', encodeURIComponent(targetChatId));

      fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken(),
          'X-Idempotency-Key': idemKey
        },
        credentials: 'same-origin',
        body: fd
      })
        .then(function (r) { return r.json(); })
        .then(function (body) {
          if (!body || !body.ok) {
            var errMsg = (body && body.message) || 'Failed to send WhatsApp message.';
            markOptimisticFailed(targetChatId, idemKey, errMsg);
            if (window.empetixToast) window.empetixToast(errMsg, 'error');
            return;
          }

          if (targetChatId === state.activeId && body.active_chat) {
            state.activeChat = body.active_chat;
            state.msgSignature = '';
            renderActiveChat(false);
          }

          // Schedule fast follow-up polls so post-response Meta `sent` (`✓`) & `delivered` (`✓✓`) ticks appear immediately
          setTimeout(pollWorkspace, 700);
          setTimeout(pollWorkspace, 1900);
        })
        .catch(function () {
          markOptimisticFailed(targetChatId, idemKey, 'Network error while sending');
          if (window.empetixToast) window.empetixToast('Network error while sending WhatsApp message.', 'error');
        })
        .finally(function () {
          state.inFlightCount = Math.max(0, state.inFlightCount - 1);
        });
    }

    // ---------------- Voice Note Recording ----------------
    /**
     * Prefer native Ogg Opus (Firefox) -> WebM Opus (Chrome/Edge/Brave, remuxed to RFC 7845 OggS on server)
     * -> MP4 AAC last (Safari). Never pick Chrome's Opus-in-MP4 ahead of WebM Opus!
     */
    function pickSupportedAudioMime() {
      if (typeof MediaRecorder === 'undefined') return '';
      var candidates = [
        'audio/ogg;codecs=opus',
        'audio/webm;codecs=opus',
        'audio/webm',
        'audio/mp4;codecs=mp4a.40.2',
        'audio/mp4'
      ];
      for (var i = 0; i < candidates.length; i++) {
        if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(candidates[i])) {
          return candidates[i];
        }
      }
      return '';
    }

    function startVoiceRecording() {
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (window.empetixToast) window.empetixToast('Microphone access is not supported in this browser.', 'error');
        return;
      }

      navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function (stream) {
          var mime = pickSupportedAudioMime();
          var recorder = mime ? new MediaRecorder(stream, { mimeType: mime }) : new MediaRecorder(stream);

          state.rec.active = true;
          state.rec.stream = stream;
          state.rec.recorder = recorder;
          state.rec.chunks = [];
          state.rec.startedAt = Date.now();

          recorder.ondataavailable = function (e) {
            if (e.data && e.data.size > 0) state.rec.chunks.push(e.data);
          };

          recorder.start(200);

          if (formEl) formEl.hidden = true;
          if (recBar) recBar.hidden = false;
          if (recTimerEl) recTimerEl.textContent = '0:00';

          state.rec.timerId = setInterval(function () {
            var elapsed = Math.floor((Date.now() - state.rec.startedAt) / 1000);
            if (recTimerEl) recTimerEl.textContent = formatDuration(elapsed);
          }, 500);

          try {
            var AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx && recBarsEl) {
              var ctx = new AudioCtx();
              var src = ctx.createMediaStreamSource(stream);
              var analyser = ctx.createAnalyser();
              analyser.fftSize = 64;
              src.connect(analyser);
              state.rec.audioCtx = ctx;
              var dataArr = new Uint8Array(analyser.frequencyBinCount);
              var spans = recBarsEl.querySelectorAll('span');

              function drawWave() {
                if (!state.rec.active) return;
                analyser.getByteFrequencyData(dataArr);
                for (var i = 0; i < spans.length; i++) {
                  var val = dataArr[i % dataArr.length] || 0;
                  var h = Math.max(4, Math.round((val / 255) * 22));
                  spans[i].style.height = h + 'px';
                }
                state.rec.rafId = requestAnimationFrame(drawWave);
              }
              drawWave();
            }
          } catch (e) {}
        })
        .catch(function () {
          if (window.empetixToast) window.empetixToast('Please allow microphone access to record voice notes.', 'error');
        });
    }

    function stopVoiceRecording(shouldSend) {
      if (!state.rec.active) return;
      var recorder = state.rec.recorder;
      var durationSec = Math.max(1, Math.round((Date.now() - state.rec.startedAt) / 1000));

      state.rec.active = false;
      if (state.rec.timerId) clearInterval(state.rec.timerId);
      if (state.rec.rafId) cancelAnimationFrame(state.rec.rafId);
      if (state.rec.audioCtx) {
        try { state.rec.audioCtx.close(); } catch (e) {}
      }

      if (recBar) recBar.hidden = true;
      if (formEl) formEl.hidden = false;

      if (!recorder) return;

      recorder.onstop = function () {
        if (state.rec.stream) {
          state.rec.stream.getTracks().forEach(function (t) { t.stop(); });
        }
        if (!shouldSend || !state.rec.chunks.length) return;

        var mime = recorder.mimeType || 'audio/webm;codecs=opus';
        var ext = mime.indexOf('ogg') !== -1 ? 'ogg' : (mime.indexOf('mp4') !== -1 ? 'm4a' : 'webm');
        var blob = new Blob(state.rec.chunks, { type: mime });
        var localBlobUrl = URL.createObjectURL(blob);
        var file = new File([blob], 'voice-note.' + ext, { type: mime });

        var fd = new FormData();
        fd.append('mode', 'voice');
        fd.append('voice_note', file);
        fd.append('duration', String(durationSec));

        sendComposerPayload(fd, {
          type: 'voice',
          blobUrl: localBlobUrl,
          duration: durationSec
        });
      };

      try { recorder.stop(); } catch (e) {}
    }

    function patchContactById(targetId, payload, toastMsg) {
      if (!targetId) return;
      var url = urls.contactTpl.replace('__ID__', encodeURIComponent(targetId));
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

    function patchContactField(payload, toastMsg) {
      patchContactById(state.activeId, payload, toastMsg);
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
      var maxX = window.innerWidth - rect.width - 10;
      var maxY = window.innerHeight - rect.height - 10;
      ctxMenuEl.style.left = Math.max(10, Math.min(x, maxX)) + 'px';
      ctxMenuEl.style.top = Math.max(10, Math.min(y, maxY)) + 'px';
    }

    function openContactContextMenu(contactId, x, y) {
      if (!ctxMenuEl) return;
      var c = state.contacts.find(function (item) { return Number(item.id) === Number(contactId); });
      if (!c && state.activeChat && state.activeChat.contact && Number(state.activeChat.contact.id) === Number(contactId)) {
        c = state.activeChat.contact;
      }
      if (!c) return;

      var isArch = !!c.is_archived;
      var isPinned = !!c.is_pinned;
      var hasUnread = Number(c.unread_count) > 0;
      var isPaused = !!c.ai_paused;

      ctxMenuEl.innerHTML = (
        '<button type="button" class="wa-ctx-item" data-wa-ctx-act="archive" data-wa-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>' +
          '<span>' + (isArch ? 'Unarchive chat' : 'Archive chat') + '</span>' +
        '</button>' +
        (!isArch ? (
          '<button type="button" class="wa-ctx-item" data-wa-ctx-act="pin" data-wa-ctx-id="' + c.id + '">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg>' +
            '<span>' + (isPinned ? 'Unpin chat' : 'Pin chat') + '</span>' +
          '</button>'
        ) : '') +
        '<button type="button" class="wa-ctx-item" data-wa-ctx-act="unread" data-wa-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' +
          '<span>' + (hasUnread ? 'Mark as read' : 'Mark as unread') + '</span>' +
        '</button>' +
        '<button type="button" class="wa-ctx-item" data-wa-ctx-act="ai" data-wa-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M9.5 15a3.5 3.5 0 0 0 5 0"/></svg>' +
          '<span>' + (isPaused ? 'Enable AI Auto-Reply' : 'Switch to Human Mode') + '</span>' +
        '</button>' +
        '<div class="wa-ctx-sep"></div>' +
        '<button type="button" class="wa-ctx-item wa-ctx-item--danger" data-wa-ctx-act="delete" data-wa-ctx-id="' + c.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
          '<span>Delete chat</span>' +
        '</button>'
      );

      positionCtxMenu(x, y);
    }

    function openMessageContextMenu(msgId, x, y) {
      if (!ctxMenuEl || !state.activeChat) return;
      if (String(msgId || '').indexOf('opt_') === 0) return;

      var msgs = state.activeChat.messages || [];
      var m = msgs.find(function (item) { return String(item.id) === String(msgId); });
      if (!m) return;

      var c = state.activeChat.contact || {};
      var isOut = m.direction === 'outbound';
      var reactStrip = '<div class="wa-ctx-reactions">' +
        QUICK_REACTIONS.map(function (em) {
          return '<button type="button" data-wa-react="' + m.id + '" data-wa-emoji-val="' + em + '">' + em + '</button>';
        }).join('') +
      '</div>';

      ctxMenuEl.innerHTML = (
        reactStrip +
        (m.wamid ? (
          '<button type="button" class="wa-ctx-item" data-wa-reply-btn="' + escapeHtml(m.wamid) + '" data-wa-reply-who="' + escapeHtml(isOut ? 'You' : (c.name || 'Customer')) + '" data-wa-reply-txt="' + escapeHtml((m.body || m.type || 'Message').slice(0, 90)) + '">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>' +
            '<span>Reply</span>' +
          '</button>'
        ) : '') +
        (m.body ? (
          '<button type="button" class="wa-ctx-item" data-wa-ctx-copy="' + escapeHtml(m.body) + '">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>' +
            '<span>Copy text</span>' +
          '</button>'
        ) : '') +
        (m.download_url || m.media_url ? (
          '<a href="' + escapeHtml(m.download_url || m.media_url) + '" target="_blank" rel="noopener" class="wa-ctx-item">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
            '<span>Download media</span>' +
          '</a>'
        ) : '') +
        '<div class="wa-ctx-sep"></div>' +
        '<button type="button" class="wa-ctx-item wa-ctx-item--danger" data-wa-ctx-del-msg="' + m.id + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
          '<span>Delete message</span>' +
        '</button>'
      );

      positionCtxMenu(x, y);
    }

    // Contact Avatar Upload in Right Drawer
    if (avatarInput) {
      avatarInput.addEventListener('change', function () {
        if (!avatarInput.files || !avatarInput.files[0] || !state.activeId) return;
        var fd = new FormData();
        fd.append('avatar', avatarInput.files[0]);
        avatarInput.value = '';

        var url = urls.contactTpl.replace('__ID__', encodeURIComponent(state.activeId));
        fetch(url, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken()
          },
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
              if (window.empetixToast) window.empetixToast('Contact photo updated.', 'success');
            }
          });
      });
    }

    // ---------------- Event Listeners ----------------
    if (searchEl) {
      var searchTimer = null;
      searchEl.addEventListener('input', function () {
        state.search = searchEl.value.trim();
        if (searchTimer) clearTimeout(searchTimer);
        searchTimer = setTimeout(pollWorkspace, 240);
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
        syncComposerButtons();
      });
      inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendComposerPayload();
        }
      });
      inputEl.addEventListener('paste', function (e) {
        if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length) {
          addStagedFiles(e.clipboardData.files);
        }
      });
    }

    if (formEl) {
      formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        sendComposerPayload();
      });
    }

    if (fileMediaInput) {
      fileMediaInput.addEventListener('change', function () {
        addStagedFiles(fileMediaInput.files);
        fileMediaInput.value = '';
      });
    }
    if (fileDocInput) {
      fileDocInput.addEventListener('change', function () {
        addStagedFiles(fileDocInput.files);
        fileDocInput.value = '';
      });
    }

    if (micBtn) micBtn.addEventListener('click', startVoiceRecording);
    var recCancelBtn = root.querySelector('[data-wa-rec-cancel]');
    var recSendBtn = root.querySelector('[data-wa-rec-send]');
    if (recCancelBtn) recCancelBtn.addEventListener('click', function () { stopVoiceRecording(false); });
    if (recSendBtn) recSendBtn.addEventListener('click', function () { stopVoiceRecording(true); });

    if (crmForm) {
      crmForm.addEventListener('submit', function (e) {
        e.preventDefault();
        patchContactField({
          visitor_name: crmName ? crmName.value : '',
          visitor_username: crmUsername ? crmUsername.value : '',
          visitor_email: crmEmail ? crmEmail.value : '',
          status: crmStatus ? crmStatus.value : 'open',
          contact_notes: crmNotes ? crmNotes.value : ''
        }, 'Contact details saved.');
      });
    }

    // Start New Chat Modal Form
    var startModeEl = root.querySelector('[data-wa-start-mode]');
    var startContentWrap = root.querySelector('[data-wa-start-content-wrap]');
    var startContentLabel = root.querySelector('[data-wa-start-content-label]');
    var startLangWrap = root.querySelector('[data-wa-start-lang-wrap]');
    if (startModeEl) {
      startModeEl.addEventListener('change', function () {
        var m = startModeEl.value;
        if (startContentWrap) startContentWrap.hidden = m === 'contact_only';
        if (startLangWrap) startLangWrap.hidden = m !== 'template';
        if (startContentLabel) startContentLabel.textContent = m === 'template' ? 'Template Name' : 'Message Text';
      });
    }

    var startForm = root.querySelector('[data-wa-start-form]');
    if (startForm) {
      startForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(startForm);
        fetch(urls.start, {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
          credentials: 'same-origin',
          body: fd
        })
          .then(function (r) { return r.json(); })
          .then(function (body) {
            if (!body || !body.ok) {
              if (window.empetixToast) window.empetixToast((body && body.message) || 'Could not start chat.', 'error');
              return;
            }
            root.querySelector('[data-wa-modal="new-chat"]').hidden = true;
            state.activeId = body.contact.id;
            state.activeChat = body.active_chat;
            state.msgSignature = '';
            renderActiveChat(true);
            pollWorkspace();
          });
      });
    }

    // Notification Settings Form
    var notifForm = root.querySelector('[data-wa-notif-form]');
    if (notifForm) {
      notifForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var enabledCb = notifForm.querySelector('[name="enabled"]');
        var emailIn = notifForm.querySelector('[name="email"]');
        var delayIn = notifForm.querySelector('[name="delay_seconds"]');

        fetch(urls.notifications, {
          method: 'PUT',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken()
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            enabled: !!(enabledCb && enabledCb.checked),
            email: emailIn ? emailIn.value : '',
            delay_seconds: delayIn ? Number(delayIn.value) : 90
          })
        })
          .then(function (r) { return r.json(); })
          .then(function (body) {
            if (body && body.ok) {
              root.querySelector('[data-wa-modal="notifications"]').hidden = true;
              if (window.empetixToast) window.empetixToast(body.message || 'Saved.', 'success');
            }
          });
      });
    }

    // Share Location Form
    var locForm = root.querySelector('[data-wa-location-form]');
    if (locForm) {
      locForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(locForm);
        fd.append('mode', 'location');
        var lat = parseFloat(locForm.querySelector('[name="latitude"]').value) || 0;
        var lng = parseFloat(locForm.querySelector('[name="longitude"]').value) || 0;
        var locName = locForm.querySelector('[name="location_name"]').value || '';
        var locAddr = locForm.querySelector('[name="location_address"]').value || '';
        root.querySelector('[data-wa-modal="location"]').hidden = true;
        sendComposerPayload(fd, {
          type: 'location',
          location: { latitude: lat, longitude: lng, name: locName, address: locAddr }
        });
      });
    }

    var geoBtn = root.querySelector('[data-wa-geo-detect]');
    if (geoBtn && navigator.geolocation) {
      geoBtn.addEventListener('click', function () {
        navigator.geolocation.getCurrentPosition(function (pos) {
          locForm.querySelector('[name="latitude"]').value = pos.coords.latitude.toFixed(6);
          locForm.querySelector('[name="longitude"]').value = pos.coords.longitude.toFixed(6);
        });
      });
    }

    // Send Template Modal Form
    var tplForm = root.querySelector('[data-wa-template-form]');
    if (tplForm) {
      tplForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(tplForm);
        fd.append('mode', 'template');
        var tplName = tplForm.querySelector('[name="template_name"]').value || 'hello_world';
        var tplLang = tplForm.querySelector('[name="template_language"]').value || 'en_US';
        root.querySelector('[data-wa-modal="template"]').hidden = true;
        sendComposerPayload(fd, {
          type: 'template',
          body: tplName + ' (' + tplLang + ')'
        });
      });
    }

    // Right-Click (`contextmenu`) on Contact Rows & Message Bubbles
    root.addEventListener('contextmenu', function (e) {
      var contactRow = e.target.closest('[data-wa-select-contact]');
      if (contactRow) {
        e.preventDefault();
        openContactContextMenu(contactRow.getAttribute('data-wa-select-contact'), e.clientX, e.clientY);
        return;
      }

      var msgRow = e.target.closest('[data-wa-msg-id]');
      if (msgRow) {
        e.preventDefault();
        openMessageContextMenu(msgRow.getAttribute('data-wa-msg-id'), e.clientX, e.clientY);
      }
    });

    document.addEventListener('click', function (e) {
      if (ctxMenuEl && !ctxMenuEl.hidden && !e.target.closest('[data-wa-row-menu]')) {
        closeCtxMenu();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeCtxMenu();
    });

    // Delegated Clicks across Workspace
    root.addEventListener('click', function (e) {
      var rowMenuBtn = e.target.closest('[data-wa-row-menu]');
      if (rowMenuBtn) {
        e.stopPropagation();
        var r = rowMenuBtn.getBoundingClientRect();
        openContactContextMenu(rowMenuBtn.getAttribute('data-wa-row-menu'), r.left, r.bottom + 4);
        return;
      }

      if (e.target.closest('[data-wa-enter-archive]')) {
        state.prevFilter = state.filter !== 'archived' ? state.filter : 'all';
        state.filter = 'archived';
        syncArchiveModeUi();
        pollWorkspace();
        return;
      }

      if (e.target.closest('[data-wa-exit-archive]')) {
        state.filter = state.prevFilter || 'all';
        syncArchiveModeUi();
        pollWorkspace();
        return;
      }

      var ctxActBtn = e.target.closest('[data-wa-ctx-act]');
      if (ctxActBtn) {
        var cid = Number(ctxActBtn.getAttribute('data-wa-ctx-id'));
        var act = ctxActBtn.getAttribute('data-wa-ctx-act');
        closeCtxMenu();
        var targetC = state.contacts.find(function (x) { return Number(x.id) === cid; })
          || (state.activeChat && state.activeChat.contact && Number(state.activeChat.contact.id) === cid ? state.activeChat.contact : null);
        if (!targetC) return;

        if (act === 'archive') {
          var nextArch = !targetC.is_archived;
          if (nextArch && state.filter !== 'archived') {
            state.contacts = state.contacts.filter(function (x) { return Number(x.id) !== cid; });
            state.archivedCount = (state.archivedCount || 0) + 1;
            renderContacts();
          }
          patchContactById(cid, { is_archived: nextArch }, nextArch ? 'Chat archived.' : 'Chat unarchived.');
        } else if (act === 'pin') {
          patchContactById(cid, { is_pinned: !targetC.is_pinned }, !targetC.is_pinned ? 'Chat pinned.' : 'Chat unpinned.');
        } else if (act === 'unread') {
          var nextUnread = !(Number(targetC.unread_count) > 0);
          patchContactById(cid, { mark_unread: nextUnread }, nextUnread ? 'Marked as unread.' : 'Marked as read.');
        } else if (act === 'ai') {
          patchContactById(cid, { ai_paused: !targetC.ai_paused }, !targetC.ai_paused ? 'Switched to Human Mode.' : 'AI Auto-Reply enabled.');
        } else if (act === 'delete') {
          if (!confirm('Delete this WhatsApp conversation and all stored media files?')) return;
          fetch(urls.deleteTpl.replace('__ID__', encodeURIComponent(cid)), {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
            credentials: 'same-origin'
          }).then(function () {
            if (Number(state.activeId) === cid) {
              state.activeId = null;
              state.activeChat = null;
              renderActiveChat(true);
            }
            pollWorkspace();
          });
        }
        return;
      }

      var copyBtn = e.target.closest('[data-wa-ctx-copy]');
      if (copyBtn) {
        var txt = copyBtn.getAttribute('data-wa-ctx-copy') || '';
        closeCtxMenu();
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(txt).then(function () {
            if (window.empetixToast) window.empetixToast('Message copied to clipboard.', 'success');
          });
        }
        return;
      }

      var delMsgBtn = e.target.closest('[data-wa-ctx-del-msg]');
      if (delMsgBtn && state.activeId && urls.delMsgTpl) {
        var mid = delMsgBtn.getAttribute('data-wa-ctx-del-msg');
        closeCtxMenu();
        if (!confirm('Delete this message from the dashboard?')) return;
        var delMsgUrl = urls.delMsgTpl
          .replace('__ID__', encodeURIComponent(state.activeId))
          .replace('__MSG__', encodeURIComponent(mid));
        fetch(delMsgUrl, {
          method: 'DELETE',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
          credentials: 'same-origin'
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
        return;
      }

      if (e.target.closest('[data-wa-toggle-archive]') && state.activeId) {
        var curArch = state.activeChat && state.activeChat.contact && state.activeChat.contact.is_archived;
        patchContactField({ is_archived: !curArch }, !curArch ? 'Conversation archived.' : 'Conversation unarchived.');
        return;
      }

      var contactRow = e.target.closest('[data-wa-select-contact]');
      if (contactRow) {
        selectContact(contactRow.getAttribute('data-wa-select-contact'));
        return;
      }

      var filterBtn = e.target.closest('[data-wa-filter]');
      if (filterBtn) {
        state.filter = filterBtn.getAttribute('data-wa-filter');
        Array.prototype.forEach.call(root.querySelectorAll('[data-wa-filter]'), function (b) {
          b.classList.toggle('is-active', b === filterBtn);
        });
        pollWorkspace();
        return;
      }

      if (e.target.closest('[data-wa-back]')) {
        root.classList.remove('is-mobile-chat');
        return;
      }

      if (e.target.closest('[data-wa-toggle-ai]')) {
        var curPaused = state.activeChat && state.activeChat.contact && state.activeChat.contact.ai_paused;
        patchContactField({ ai_paused: !curPaused }, !curPaused ? 'Human Takeover active (AI paused).' : 'AI Auto-Reply enabled.');
        return;
      }

      if (e.target.closest('[data-wa-toggle-pin]')) {
        var curPinned = state.activeChat && state.activeChat.contact && state.activeChat.contact.is_pinned;
        patchContactField({ is_pinned: !curPinned }, !curPinned ? 'Conversation pinned.' : 'Conversation unpinned.');
        return;
      }

      if (e.target.closest('[data-wa-toggle-drawer]')) {
        var willOpen = drawerEl.hidden;
        drawerEl.hidden = !willOpen;
        root.classList.toggle('has-drawer', willOpen);
        return;
      }

      if (e.target.closest('[data-wa-close-drawer]')) {
        drawerEl.hidden = true;
        root.classList.remove('has-drawer');
        return;
      }

      if (e.target.closest('[data-wa-toggle-msg-search]')) {
        inchatSearchWrap.hidden = !inchatSearchWrap.hidden;
        if (!inchatSearchWrap.hidden && inchatInput) inchatInput.focus();
        return;
      }

      if (e.target.closest('[data-wa-close-msg-search]')) {
        inchatSearchWrap.hidden = true;
        state.inchatFilter = '';
        if (inchatInput) inchatInput.value = '';
        state.msgSignature = '';
        renderActiveChat(false);
        return;
      }

      if (e.target.closest('[data-wa-btn-emoji]')) {
        emojiPopover.hidden = !emojiPopover.hidden;
        attachMenu.hidden = true;
        return;
      }

      var emojiPick = e.target.closest('[data-wa-emoji]');
      if (emojiPick && inputEl) {
        inputEl.value += emojiPick.getAttribute('data-wa-emoji');
        emojiPopover.hidden = true;
        syncComposerButtons();
        inputEl.focus();
        return;
      }

      if (e.target.closest('[data-wa-btn-attach]')) {
        attachMenu.hidden = !attachMenu.hidden;
        emojiPopover.hidden = true;
        return;
      }

      var pickItem = e.target.closest('[data-wa-pick]');
      if (pickItem) {
        attachMenu.hidden = true;
        if (pickItem.getAttribute('data-wa-pick') === 'media' && fileMediaInput) fileMediaInput.click();
        else if (fileDocInput) fileDocInput.click();
        return;
      }

      var rmStaged = e.target.closest('[data-wa-rm-staged]');
      if (rmStaged) {
        state.stagedFiles.splice(Number(rmStaged.getAttribute('data-wa-rm-staged')), 1);
        renderStagedFiles();
        syncComposerButtons();
        return;
      }

      var replyTrigger = e.target.closest('[data-wa-reply-btn]');
      if (replyTrigger) {
        setReplyTo(
          replyTrigger.getAttribute('data-wa-reply-btn'),
          replyTrigger.getAttribute('data-wa-reply-who'),
          replyTrigger.getAttribute('data-wa-reply-txt')
        );
        return;
      }

      if (e.target.closest('[data-wa-reply-cancel]')) {
        clearReplyTo();
        return;
      }

      var reactBtn = e.target.closest('[data-wa-react]');
      if (reactBtn && state.activeId) {
        var msgId = reactBtn.getAttribute('data-wa-react');
        var emojiVal = reactBtn.getAttribute('data-wa-emoji-val') || '';
        var reactUrl = urls.reactTpl
          .replace('__ID__', encodeURIComponent(state.activeId))
          .replace('__MSG__', encodeURIComponent(msgId));
        fetch(reactUrl, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken()
          },
          credentials: 'same-origin',
          body: JSON.stringify({ emoji: emojiVal })
        }).then(function () { pollWorkspace(); });
        return;
      }

      var jumpQuote = e.target.closest('[data-wa-jump-wamid]');
      if (jumpQuote) {
        var targetWamid = jumpQuote.getAttribute('data-wa-jump-wamid');
        var targetEl = targetWamid && messagesEl.querySelector('[data-wa-wamid="' + targetWamid + '"]');
        if (targetEl) targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      // Voice Player Controls
      var voiceToggle = e.target.closest('[data-wa-voice-toggle]');
      if (voiceToggle) {
        var player = voiceToggle.closest('[data-wa-voice-player]');
        var audio = player && player.querySelector('audio');
        if (!audio) return;
        if (audio.paused) {
          audio.play();
          voiceToggle.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
          audio.ontimeupdate = function () {
            var dur = audio.duration || 1;
            var pct = audio.currentTime / dur;
            var bars = player.querySelectorAll('[data-wa-voice-scrub] span');
            var durLabel = player.querySelector('[data-wa-voice-dur]');
            if (durLabel) durLabel.textContent = formatDuration(audio.currentTime);
            for (var i = 0; i < bars.length; i++) {
              bars[i].classList.toggle('is-played', (i / bars.length) <= pct);
            }
          };
          audio.onended = function () {
            voiceToggle.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"/></svg>';
          };
        } else {
          audio.pause();
          voiceToggle.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"/></svg>';
        }
        return;
      }

      var speedBtn = e.target.closest('[data-wa-voice-speed]');
      if (speedBtn) {
        var vPlayer = speedBtn.closest('[data-wa-voice-player]');
        var vAudio = vPlayer && vPlayer.querySelector('audio');
        if (!vAudio) return;
        var nextRate = vAudio.playbackRate === 1 ? 1.5 : (vAudio.playbackRate === 1.5 ? 2 : 1);
        vAudio.playbackRate = nextRate;
        speedBtn.textContent = nextRate + 'x';
        return;
      }

      // Photo Lightbox
      var zoomImg = e.target.closest('[data-wa-zoom]');
      if (zoomImg && lightboxEl) {
        lightboxImg.src = zoomImg.getAttribute('data-wa-zoom');
        if (lightboxCap) lightboxCap.textContent = zoomImg.getAttribute('data-wa-cap') || 'Photo';
        if (lightboxDl) lightboxDl.href = zoomImg.getAttribute('data-wa-dl') || zoomImg.getAttribute('data-wa-zoom');
        lightboxEl.hidden = false;
        return;
      }

      if (e.target.closest('[data-wa-lightbox-close]') || e.target === lightboxEl) {
        lightboxEl.hidden = true;
        return;
      }

      // Modals
      var openModalBtn = e.target.closest('[data-wa-open-modal]');
      if (openModalBtn) {
        attachMenu.hidden = true;
        var modalName = openModalBtn.getAttribute('data-wa-open-modal');
        var modalEl = root.querySelector('[data-wa-modal="' + modalName + '"]');
        if (modalEl) modalEl.hidden = false;
        return;
      }

      if (e.target.closest('[data-wa-close-modal]') || e.target.hasAttribute('data-wa-modal')) {
        Array.prototype.forEach.call(root.querySelectorAll('[data-wa-modal]'), function (m) { m.hidden = true; });
        return;
      }

      if (e.target.closest('[data-wa-delete-chat]') && state.activeId) {
        if (!confirm('Delete this WhatsApp conversation and all stored media files?')) return;
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

    root.addEventListener('dragover', function (e) { e.preventDefault(); });
    root.addEventListener('drop', function (e) {
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length && state.activeId) {
        e.preventDefault();
        addStagedFiles(e.dataTransfer.files);
      }
    });

    var pollTimer = setInterval(pollWorkspace, 1800);
    document.addEventListener('livewire:navigating', function () {
      clearInterval(pollTimer);
      stopVoiceRecording(false);
    }, { once: true });
  }

  function initAll() {
    var el = document.querySelector('[data-whatsapp-root]');
    if (el) mountWhatsappInbox(el);
  }

  document.addEventListener('DOMContentLoaded', initAll);
  document.addEventListener('livewire:navigated', initAll);
})();
