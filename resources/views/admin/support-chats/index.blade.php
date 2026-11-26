@extends('layouts.admin')

@section('title', 'Support Conversations')
@section('crumb', 'Support Chats · Live Runtime Monitor')

@section('actions')
  <span class="ai-status-pill" style="background:rgba(16,185,129,0.12);border-color:rgba(16,185,129,0.34);color:#10B981;">
    <span class="ai-status-pill__dot" style="background:#10B981;"></span>
    <span>Runtime Live Sync</span>
  </span>
  <a class="btn btn--primary btn--sm" href="{{ route('admin.support-chats.dispatch') }}" wire:navigate>
    <span>+ Send WhatsApp</span>
  </a>
@endsection

@section('content')
<div class="support-kpis" id="support-live-kpis" data-pulse-url="{{ route('admin.support-chats.live-pulse') }}">
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">Total Conversations</div>
    <div class="support-kpi__val" style="color:#fff;" data-kpi-total>{{ $stats['total'] }}</div>
  </div>
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">WhatsApp Threads</div>
    <div class="support-kpi__val" style="color:#25D366;" data-kpi-whatsapp>{{ $stats['whatsapp'] }}</div>
  </div>
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">Handed Off to Inbox</div>
    <div class="support-kpi__val" style="color:var(--accent);" data-kpi-handed-off>{{ $stats['handed_off'] }}</div>
  </div>
  <div class="support-kpi" style="background:#0E0C0A;border-color:rgba(255,255,255,0.08);color:#fff;">
    <div class="support-kpi__label" style="color:rgba(255,255,255,0.55);">Started Today</div>
    <div class="support-kpi__val" style="color:#fff;" data-kpi-today>{{ $stats['today'] }}</div>
  </div>
</div>

{{-- Real-Time Active Typing Banner (appears automatically when any Website or WhatsApp customer is typing) --}}
<div
  id="support-live-typing-banner"
  hidden
  style="margin-bottom:16px;padding:12px 18px;border-radius:16px;background:linear-gradient(135deg,#0B1324 0%,#101E36 100%);border:1px solid rgba(16,185,129,0.45);color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;box-shadow:0 10px 26px rgba(10,14,24,0.16);"
>
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;" data-live-banner-items></div>
</div>

<div class="card">
  <div class="table-wrap table-wrap--dt">
    {!! $datatable->table(['class' => 'table dt-table', 'style' => 'width:100%']) !!}
  </div>
</div>

<script>
(function () {
  var kpiRoot = document.getElementById('support-live-kpis');
  if (!kpiRoot || kpiRoot.__pulseBound) return;
  kpiRoot.__pulseBound = true;

  var pulseUrl = kpiRoot.getAttribute('data-pulse-url');
  var bannerEl = document.getElementById('support-live-typing-banner');
  var bannerItemsEl = bannerEl ? bannerEl.querySelector('[data-live-banner-items]') : null;
  var lastSignature = null;

  function escapeHtml(str) {
    var d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function pollPulse() {
    if (document.hidden || !pulseUrl) return;
    fetch(pulseUrl, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (data) {
        if (!data || !data.ok) return;

        if (data.stats) {
          var t = kpiRoot.querySelector('[data-kpi-total]');
          var w = kpiRoot.querySelector('[data-kpi-whatsapp]');
          var h = kpiRoot.querySelector('[data-kpi-handed-off]');
          var d = kpiRoot.querySelector('[data-kpi-today]');
          if (t) t.textContent = data.stats.total;
          if (w) w.textContent = data.stats.whatsapp;
          if (h) h.textContent = data.stats.handed_off;
          if (d) d.textContent = data.stats.today;
        }

        var active = Array.isArray(data.active_typing) ? data.active_typing : [];
        if (bannerEl && bannerItemsEl) {
          if (!active.length) {
            bannerEl.hidden = true;
            bannerItemsEl.innerHTML = '';
          } else {
            bannerEl.hidden = false;
            bannerItemsEl.innerHTML = active.map(function (item) {
              var ch = item.is_whatsapp ? 'WhatsApp' : 'Website';
              var stateText = item.customer_typing
                ? 'is typing live' + (item.customer_preview ? ' (“' + escapeHtml(item.customer_preview) + '”)' : '…')
                : 'AI Concierge is replying…';
              return (
                '<a href="' + escapeHtml(item.url) + '" style="display:inline-flex;align-items:center;gap:8px;color:#fff;text-decoration:none;font-size:0.84rem;font-weight:600;background:rgba(255,255,255,0.07);padding:6px 13px;border-radius:999px;border:1px solid rgba(16,185,129,0.4);">' +
                  '<span class="ai-status-pill__dot" style="background:#10B981;"></span>' +
                  '<span>[' + ch + '] <strong>' + escapeHtml(item.visitor_label) + '</strong> ' + stateText + '</span>' +
                  '<span style="color:#34D399;">Open Live &rarr;</span>' +
                '</a>'
              );
            }).join('');
          }
        }

        if (lastSignature !== null && data.signature && data.signature !== lastSignature) {
          if (window.jQuery && jQuery.fn.dataTable) {
            jQuery.fn.dataTable.tables({ api: true }).ajax.reload(null, false);
          }
        }
        lastSignature = data.signature || lastSignature;
      })
      .catch(function () {});
  }

  pollPulse();
  var timer = setInterval(pollPulse, 2000);
  document.addEventListener('livewire:navigating', function () { clearInterval(timer); }, { once: true });
})();
</script>
@endsection

@push('datatables')
  {!! $datatable->scripts() !!}
@endpush
