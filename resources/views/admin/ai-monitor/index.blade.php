@extends('layouts.admin')

@section('title', 'AI Quota & Model Health')
@section('page_header', 'hide')

@section('content')
@php
  $days = (int) ($report['days'] ?? 28);
  $kpis = $report['kpis'] ?? [];
  $slotQuotas = $report['slot_quotas'] ?? [];
  $models = $report['models'] ?? [];
  $errorBreakdown = $report['error_breakdown'] ?? [];
  $timeline = $report['timeline'] ?? [];
  $incidents = $report['recent_incidents'] ?? [];

  $maxBucketReq = max(1, collect($timeline)->max('requests') ?? 1);
  $maxBucketErr = max(1, collect($timeline)->max('errors') ?? 1);
@endphp

<style>
  .aim-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 22px;
  }

  .aim-range-bar {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px;
    border-radius: 999px;
    background: #141210;
    border: 1px solid rgba(255, 255, 255, 0.09);
  }

  .aim-range-pill {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    color: rgba(245, 241, 236, 0.65);
    text-decoration: none;
    transition: all 0.16s ease;
  }

  .aim-range-pill:hover {
    color: #fff;
  }

  .aim-range-pill.is-active {
    background: #FF4D1C;
    color: #fff;
    box-shadow: 0 4px 12px rgba(255, 77, 28, 0.35);
  }

  /* Google AI Studio-inspired Dark Obsidian KPI Grid */
  .aim-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
  }

  @media (max-width: 1200px) {
    .aim-kpi-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
  }

  @media (max-width: 720px) {
    .aim-kpi-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  .aim-kpi {
    padding: 18px 20px;
    border-radius: 18px;
    background: linear-gradient(165deg, #181513 0%, #0E0C0B 100%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #F5F1EC;
    box-shadow: 0 14px 32px rgba(10, 10, 12, 0.14);
  }

  .aim-kpi__label {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(245, 241, 236, 0.56);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
  }

  .aim-kpi__value {
    font-family: var(--font-display);
    font-size: 1.72rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    color: #FFFFFF;
  }

  .aim-kpi__sub {
    margin-top: 8px;
    font-size: 0.73rem;
    color: rgba(245, 241, 236, 0.48);
  }

  /* Dual Studio Charts */
  .aim-charts-grid {
    display: grid;
    grid-template-columns: 1.55fr 1fr;
    gap: 18px;
    margin-bottom: 24px;
  }

  @media (max-width: 1024px) {
    .aim-charts-grid {
      grid-template-columns: 1fr;
    }
  }

  .aim-dark-panel {
    padding: 22px 24px;
    border-radius: 20px;
    background: linear-gradient(165deg, #161412 0%, #0C0B0A 100%);
    border: 1px solid rgba(255, 255, 255, 0.09);
    color: #F5F1EC;
    box-shadow: 0 18px 40px rgba(10, 10, 12, 0.16);
  }

  .aim-dark-panel__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 16px;
  }

  .aim-dark-panel__title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #fff;
    margin: 0;
  }

  .aim-bars {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    height: 168px;
    padding-top: 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.09);
  }

  .aim-bar-col {
    flex: 1;
    min-width: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
    position: relative;
  }

  .aim-bar-fill {
    width: 100%;
    max-width: 22px;
    border-radius: 5px 5px 0 0;
    background: linear-gradient(180deg, #E5E7EB 0%, rgba(229, 231, 235, 0.45) 100%);
    transition: height 0.25s ease, background 0.18s ease;
    min-height: 2px;
  }

  .aim-bar-col:hover .aim-bar-fill {
    background: #25D366;
  }

  .aim-bar-fill--err {
    background: linear-gradient(180deg, #F87171 0%, #DC2626 100%);
  }

  .aim-slot-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(315px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .aim-slot-card {
    padding: 18px 20px;
    border-radius: 18px;
    background: var(--surface, #fff);
    border: 1px solid var(--border, rgba(15, 23, 42, 0.09));
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 14px;
  }

  .aim-meter {
    height: 7px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .aim-meter > span {
    display: block;
    height: 100%;
    border-radius: 999px;
    transition: width 0.25s ease;
  }

  .aim-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .aim-badge--healthy,
  .aim-badge--armed {
    background: rgba(37, 211, 102, 0.14);
    color: #15803d;
    border: 1px solid rgba(37, 211, 102, 0.35);
  }

  .aim-badge--recovered {
    background: rgba(59, 130, 246, 0.13);
    color: #1d4ed8;
    border: 1px solid rgba(59, 130, 246, 0.3);
  }

  .aim-badge--degraded,
  .aim-badge--warn {
    background: rgba(245, 158, 11, 0.15);
    color: #b45309;
    border: 1px solid rgba(245, 158, 11, 0.38);
  }

  .aim-badge--critical,
  .aim-badge--danger {
    background: rgba(239, 68, 68, 0.14);
    color: #b91c1c;
    border: 1px solid rgba(239, 68, 68, 0.36);
  }

  .aim-badge--unconfigured {
    background: rgba(100, 116, 139, 0.12);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.24);
  }

  .aim-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .aim-table th {
    text-align: left;
    font-size: 0.71rem;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--muted);
    padding: 11px 12px;
    border-bottom: 1px solid var(--border, rgba(0, 0, 0, 0.08));
  }

  .aim-table td {
    padding: 13px 12px;
    border-bottom: 1px solid var(--border, rgba(0, 0, 0, 0.06));
    vertical-align: top;
  }

  .aim-table tr:last-child td {
    border-bottom: 0;
  }
</style>

<div>
  {{-- ==================== HEADER & CONTROLS ==================== --}}
  <div class="aim-head">
    <div>
      <div style="font-size:0.73rem;font-weight:700;letter-spacing:0.11em;text-transform:uppercase;color:var(--brand, #FF4D1C);margin-bottom:4px;">AI Telemetry &middot; Quota &amp; Diagnostics</div>
      <h1 style="font-family:var(--font-display);font-size:clamp(1.5rem,2.2vw,1.95rem);font-weight:800;letter-spacing:-0.025em;margin:0 0 4px;">AI Quota, Token Usage &amp; Model Health</h1>
      <p style="margin:0;color:var(--muted);font-size:0.9rem;">Live consumption per isolated API key, rate-limit headroom (RPM / RPD), automatic failovers, and exact root-cause diagnosis for every model.</p>
    </div>

    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
      <div class="aim-range-bar" role="tablist" aria-label="Time window">
        <a href="{{ route('admin.ai-monitor.index', ['days' => 1]) }}" class="aim-range-pill {{ $days === 1 ? 'is-active' : '' }}" wire:navigate>24 Hours</a>
        <a href="{{ route('admin.ai-monitor.index', ['days' => 7]) }}" class="aim-range-pill {{ $days === 7 ? 'is-active' : '' }}" wire:navigate>7 Days</a>
        <a href="{{ route('admin.ai-monitor.index', ['days' => 28]) }}" class="aim-range-pill {{ $days === 28 ? 'is-active' : '' }}" wire:navigate>28 Days</a>
      </div>

      <form method="post" action="{{ route('admin.ai-monitor.probe') }}" style="margin:0;">
        @csrf
        <input type="hidden" name="provider" value="all">
        <button type="submit" class="btn btn--primary btn--sm">⚡ Probe All Armed Keys</button>
      </form>

      <a href="{{ route('admin.settings.edit', ['section' => 'chatbot']) }}" class="btn btn--ghost btn--sm" wire:navigate>🔑 Manage API Keys</a>
    </div>
  </div>

  {{-- ==================== TOP KPI STRIP (GOOGLE AI STUDIO STYLE) ==================== --}}
  <div class="aim-kpi-grid">
    <div class="aim-kpi">
      <div class="aim-kpi__label">Total Input Tokens</div>
      <div class="aim-kpi__value">{{ number_format((int) ($kpis['input_tokens'] ?? 0)) }}</div>
      <div class="aim-kpi__sub">Cached: {{ number_format((int) ($kpis['cached_tokens'] ?? 0)) }} tokens</div>
    </div>

    <div class="aim-kpi">
      <div class="aim-kpi__label">Total Output Tokens</div>
      <div class="aim-kpi__value">{{ number_format((int) ($kpis['output_tokens'] ?? 0)) }}</div>
      <div class="aim-kpi__sub">Reasoning: {{ number_format((int) ($kpis['reasoning_tokens'] ?? 0)) }} tokens</div>
    </div>

    <div class="aim-kpi">
      <div class="aim-kpi__label">Total API Requests</div>
      <div class="aim-kpi__value">{{ number_format((int) ($kpis['total_requests'] ?? 0)) }}</div>
      <div class="aim-kpi__sub">Avg Latency: {{ number_format((int) ($kpis['avg_latency_ms'] ?? 0)) }} ms</div>
    </div>

    <div class="aim-kpi">
      <div class="aim-kpi__label">
        <span>Success Rate</span>
        <span style="color:#25D366;">●</span>
      </div>
      <div class="aim-kpi__value" style="color:{{ ($kpis['success_rate'] ?? 100) >= 95 ? '#25D366' : '#FBBF24' }};">{{ $kpis['success_rate'] ?? 100 }}%</div>
      <div class="aim-kpi__sub">{{ number_format((int) ($kpis['failover_count'] ?? 0)) }} rescued via Auto-Failover</div>
    </div>

    <div class="aim-kpi">
      <div class="aim-kpi__label">Total API Errors</div>
      <div class="aim-kpi__value" style="color:{{ ($kpis['total_failures'] ?? 0) > 0 ? '#F87171' : '#FFFFFF' }};">{{ number_format((int) ($kpis['total_failures'] ?? 0)) }}</div>
      <div class="aim-kpi__sub">{{ (int) ($kpis['error_count'] ?? 0) }} terminal &middot; {{ (int) ($kpis['failover_count'] ?? 0) }} failed over</div>
    </div>
  </div>

  {{-- ==================== DUAL CHARTS (REQUESTS + ERRORS BREAKDOWN) ==================== --}}
  <div class="aim-charts-grid">
    {{-- Left Chart: Total API Requests & Success Rate --}}
    <div class="aim-dark-panel">
      <div class="aim-dark-panel__head">
        <div>
          <h2 class="aim-dark-panel__title">Total API Requests &amp; Success Rate</h2>
          <div style="font-size:0.75rem;color:rgba(245,241,236,0.5);margin-top:2px;">Window: {{ $days === 1 ? 'Last 24 Hours (Hourly)' : "Last {$days} Days (Daily)" }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:14px;font-size:0.74rem;color:rgba(245,241,236,0.72);">
          <span><span style="display:inline-block;width:9px;height:9px;border-radius:2px;background:#E5E7EB;margin-right:5px;"></span>API Requests</span>
          <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#25D366;margin-right:5px;"></span>{{ $kpis['success_rate'] ?? 100 }}% Success</span>
        </div>
      </div>

      <div class="aim-bars" role="img" aria-label="API Requests timeline chart">
        @foreach($timeline as $point)
          @php($pctH = $point['requests'] > 0 ? max(8, (int) round(($point['requests'] / $maxBucketReq) * 100)) : 2)
          <div class="aim-bar-col" title="{{ $point['label'] }}: {{ $point['requests'] }} requests ({{ $point['success_rate'] }}% success, {{ $point['errors'] }} errors)">
            <div class="aim-bar-fill {{ $point['errors'] > 0 && $point['success'] === 0 ? 'aim-bar-fill--err' : '' }}" style="height:{{ $pctH }}%;"></div>
          </div>
        @endforeach
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:8px;font-size:0.71rem;color:rgba(245,241,236,0.45);">
        <span>{{ $timeline[0]['label'] ?? '' }}</span>
        <span>{{ $timeline[(int) floor(count($timeline) / 2)]['label'] ?? '' }}</span>
        <span>{{ $timeline[count($timeline) - 1]['label'] ?? '' }}</span>
      </div>
    </div>

    {{-- Right Chart: Total API Errors by Root Cause --}}
    <div class="aim-dark-panel">
      <div class="aim-dark-panel__head">
        <div>
          <h2 class="aim-dark-panel__title">Total API Errors &amp; Root Causes</h2>
          <div style="font-size:0.75rem;color:rgba(245,241,236,0.5);margin-top:2px;">Categorized HTTP &amp; Quota errors</div>
        </div>
        @if(count($incidents) > 0)
          <form method="post" action="{{ route('admin.ai-monitor.clear-errors') }}" style="margin:0;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn--ghost btn--sm" style="color:rgba(255,255,255,0.72);border-color:rgba(255,255,255,0.16);font-size:0.72rem;padding:4px 10px;">Clear Log</button>
          </form>
        @endif
      </div>

      @if($errorBreakdown === [])
        <div style="height:175px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;border:1px dashed rgba(255,255,255,0.11);border-radius:14px;padding:16px;">
          <div style="width:38px;height:38px;border-radius:50%;background:rgba(37,211,102,0.16);color:#25D366;display:flex;align-items:center;justify-content:center;font-weight:800;margin-bottom:8px;">✓</div>
          <div style="font-weight:700;font-size:0.9rem;color:#fff;">Zero API Errors in Window</div>
          <div style="font-size:0.76rem;color:rgba(245,241,236,0.5);margin-top:3px;">No 503 Overloaded, 429 Quota, or 401 Key errors recorded.</div>
        </div>
      @else
        <div style="display:flex;flex-direction:column;gap:12px;">
          @foreach($errorBreakdown as $err)
            @php($errPct = min(100, max(8, (int) round(($err['count'] / max(1, (int) ($kpis['total_failures'] ?? 1))) * 100))))
            <div style="padding:10px 12px;border-radius:12px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
              <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                <span style="font-size:0.8rem;font-weight:700;color:{{ $err['severity'] === 'danger' ? '#FCA5A5' : '#FCD34D' }};">● {{ $err['badge'] }}</span>
                <span style="font-family:var(--font-display);font-weight:800;font-size:0.9rem;color:#fff;">{{ $err['count'] }}</span>
              </div>
              <div style="height:5px;border-radius:999px;background:rgba(255,255,255,0.09);overflow:hidden;margin-bottom:6px;">
                <span style="display:block;height:100%;width:{{ $errPct }}%;background:{{ $err['severity'] === 'danger' ? '#EF4444' : '#F59E0B' }};border-radius:999px;"></span>
              </div>
              <div style="font-size:0.75rem;color:rgba(245,241,236,0.76);direction:rtl;text-align:right;">{{ $err['title_ar'] }}</div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- ==================== ISOLATED KEY SLOTS & LIVE QUOTA HEADROOM ==================== --}}
  <div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="font-size:1.08rem;font-weight:800;margin:0;">Quota Headroom per Isolated Key Slot (RPM &amp; Daily RPD)</h2>
      <p style="margin:2px 0 0;font-size:0.82rem;color:var(--muted);">Real-time consumption for Customer Support, Dashboard Copilot, Secondary Gemini, and Emergency Providers.</p>
    </div>
  </div>

  <div class="aim-slot-grid">
    @foreach($slotQuotas as $slot)
      <div class="aim-slot-card">
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
            <span style="font-size:0.71rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted);">{{ $slot['role'] }}</span>
            <span class="aim-badge aim-badge--{{ $slot['health'] }}">
              @switch($slot['health'])
                @case('healthy') ● Healthy @break
                @case('recovered') ● Recovered @break
                @case('degraded') ⚡ Overload / Quota @break
                @case('critical') ✖ Key / Credit Error @break
                @default ○ Not Armed
              @endswitch
            </span>
          </div>

          <div style="font-weight:800;font-size:0.96rem;margin-bottom:2px;">{{ strtoupper($slot['provider']) }}</div>
          <div style="font-size:0.76rem;color:var(--muted);margin-bottom:12px;">
            Model: <code>{{ $slot['default_model'] }}</code>
            @if($slot['masked']) &middot; Key: <code>{{ $slot['masked'] }}</code> @endif
          </div>

          {{-- Daily RPD Quota Bar --}}
          <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:0.75rem;margin-bottom:4px;">
              <span style="font-weight:600;">Daily Quota (RPD)</span>
              <span><strong>{{ number_format($slot['rpd_used']) }}</strong> / {{ number_format($slot['rpd_limit']) }} req ({{ $slot['rpd_pct'] }}%)</span>
            </div>
            <div class="aim-meter">
              <span style="width:{{ max($slot['rpd_used'] > 0 ? 3 : 0, $slot['rpd_pct']) }}%;background:{{ $slot['rpd_pct'] >= 85 ? '#EF4444' : ($slot['rpd_pct'] >= 60 ? '#F59E0B' : '#25D366') }};"></span>
            </div>
          </div>

          {{-- Live Minute Burst RPM Bar --}}
          <div>
            <div style="display:flex;justify-content:space-between;font-size:0.75rem;margin-bottom:4px;">
              <span style="font-weight:600;">Live Burst (RPM — Last 60s)</span>
              <span><strong>{{ $slot['rpm_used'] }}</strong> / {{ $slot['rpm_limit'] }} rpm</span>
            </div>
            <div class="aim-meter">
              <span style="width:{{ max($slot['rpm_used'] > 0 ? 4 : 0, $slot['rpm_pct']) }}%;background:{{ $slot['rpm_pct'] >= 85 ? '#EF4444' : '#6366F1' }};"></span>
            </div>
          </div>

          @if($slot['latest_issue'])
            <div style="margin-top:12px;padding:9px 11px;border-radius:10px;background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.2);font-size:0.75rem;">
              <div style="font-weight:700;color:#b91c1c;margin-bottom:2px;">⚠ {{ $slot['latest_issue']['badge'] }} ({{ $slot['latest_issue']['ago'] }})</div>
              <div style="color:#7f1d1d;direction:rtl;text-align:right;">{{ $slot['latest_issue']['title_ar'] }}</div>
            </div>
          @endif
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding-top:10px;border-top:1px solid var(--border, rgba(0,0,0,0.06));font-size:0.75rem;color:var(--muted);">
          <span>Today: <strong>{{ number_format($slot['tokens_today']) }}</strong> tok &middot; {{ $slot['avg_latency_ms'] }}ms</span>
          @if($slot['ready'])
            <form method="post" action="{{ route('admin.ai-monitor.probe') }}" style="margin:0;">
              @csrf
              <input type="hidden" name="provider" value="{{ $slot['provider'] }}">
              <button type="submit" class="btn btn--ghost btn--sm" style="padding:4px 10px;font-size:0.73rem;">⚡ Test Key</button>
            </form>
          @else
            <a href="{{ route('admin.settings.edit', ['section' => 'chatbot']) }}" style="font-weight:700;color:var(--brand, #FF4D1C);text-decoration:none;" wire:navigate>+ Add Key</a>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  {{-- ==================== PER-MODEL DIAGNOSTICS & PROBLEMS TABLE ==================== --}}
  <div class="card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
      <div>
        <h2 class="card__title" style="margin:0;">Per-Model Health &amp; Diagnosed Problems (تشخيص مشاكل كل Model)</h2>
        <p class="card__sub" style="margin:2px 0 0;">Detailed token usage, latency, error codes (<code>503</code> / <code>429</code> / <code>401</code> / <code>402</code>), and actionable Arabic root-cause guidance per model.</p>
      </div>
    </div>

    <div style="overflow-x:auto;">
      <table class="aim-table">
        <thead>
          <tr>
            <th>Model &amp; Provider</th>
            <th>Status</th>
            <th>Requests / Success</th>
            <th>Tokens (In / Out)</th>
            <th>Latency</th>
            <th>Detected Errors</th>
            <th style="min-width:270px;">المشكلة والتشخيص (Problem &amp; Fix)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($models as $m)
            <tr>
              <td>
                <div style="font-weight:800;color:var(--ink, #0f172a);"><code>{{ $m['model'] }}</code></div>
                <div style="font-size:0.74rem;color:var(--muted);margin-top:2px;">{{ strtoupper($m['provider']) }} &middot; {{ $m['role'] }}</div>
              </td>
              <td>
                <span class="aim-badge aim-badge--{{ $m['status_state'] }}">{{ $m['status_label'] }}</span>
              </td>
              <td>
                <div style="font-weight:700;">{{ number_format($m['requests']) }} req</div>
                <div style="font-size:0.74rem;color:{{ $m['success_rate'] >= 95 ? '#15803d' : '#b45309' }};">{{ $m['success_rate'] }}% success</div>
              </td>
              <td>
                <div style="font-weight:700;">{{ number_format($m['total_tokens']) }}</div>
                <div style="font-size:0.73rem;color:var(--muted);">{{ number_format($m['input_tokens']) }} in / {{ number_format($m['output_tokens']) }} out</div>
              </td>
              <td>
                <span style="font-weight:600;">{{ $m['avg_latency_ms'] > 0 ? $m['avg_latency_ms'].' ms' : '—' }}</span>
              </td>
              <td>
                @if($m['error_pills'] === [])
                  <span style="color:#15803d;font-size:0.78rem;font-weight:600;">0 Errors</span>
                @else
                  <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($m['error_pills'] as $pill)
                      <span class="aim-badge aim-badge--{{ $pill['severity'] }}">{{ $pill['badge'] }} &times;{{ $pill['count'] }}</span>
                    @endforeach
                  </div>
                @endif
              </td>
              <td style="direction:rtl;text-align:right;">
                <div style="font-weight:700;font-size:0.82rem;color:{{ $m['error_pills'] !== [] ? '#b91c1c' : '#15803d' }};">
                  {{ $m['diagnosis_title'] }}
                </div>
                @if($m['diagnosis_fix'])
                  <div style="font-size:0.76rem;color:var(--muted);margin-top:3px;">💡 {{ $m['diagnosis_fix'] }}</div>
                @endif
                @if($m['latest_error_msg'])
                  <div style="direction:ltr;text-align:left;margin-top:6px;padding:6px 8px;border-radius:7px;background:rgba(15,23,42,0.04);font-family:monospace;font-size:0.71rem;color:#64748b;word-break:break-word;">
                    {{ \Illuminate\Support\Str::limit($m['latest_error_msg'], 180) }}
                    @if($m['latest_error_at']) <span style="opacity:0.75;">({{ $m['latest_error_at'] }})</span> @endif
                  </div>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- ==================== RECENT INCIDENTS & AUTO-FAILOVER LOG ==================== --}}
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
      <div>
        <h2 class="card__title" style="margin:0;">Recent Model Incidents &amp; Emergency Failover Log</h2>
        <p class="card__sub" style="margin:2px 0 0;">Every upstream <code>503</code>, <code>429</code>, <code>401</code>, <code>402</code>, or timeout event and whether Emergency Failover rescued the conversation.</p>
      </div>
    </div>

    @if($incidents === [])
      <div style="padding:28px 16px;text-align:center;color:var(--muted);font-size:0.88rem;">
        No upstream model errors or failovers recorded in this time window.
      </div>
    @else
      <div style="overflow-x:auto;">
        <table class="aim-table">
          <thead>
            <tr>
              <th>Time</th>
              <th>Channel</th>
              <th>Provider / Model</th>
              <th>Outcome</th>
              <th>Error Type</th>
              <th>Raw Upstream Message</th>
            </tr>
          </thead>
          <tbody>
            @foreach($incidents as $inc)
              <tr>
                <td style="white-space:nowrap;">
                  <div style="font-weight:600;">{{ $inc['time'] }}</div>
                  <div style="font-size:0.72rem;color:var(--muted);">{{ $inc['ago'] }}</div>
                </td>
                <td>
                  <span class="pill" style="text-transform:uppercase;font-size:0.7rem;">{{ $inc['channel'] }}</span>
                </td>
                <td>
                  <div style="font-weight:700;">{{ strtoupper($inc['provider']) }}</div>
                  <code style="font-size:0.74rem;color:var(--muted);">{{ $inc['model'] }}</code>
                </td>
                <td>
                  @if($inc['status'] === 'failover')
                    <span class="aim-badge aim-badge--warn">⚡ Rescued by Failover</span>
                  @else
                    <span class="aim-badge aim-badge--danger">✖ Request Failed</span>
                  @endif
                </td>
                <td>
                  <span class="aim-badge aim-badge--danger">{{ $inc['badge'] }}</span>
                </td>
                <td style="max-width:380px;">
                  <div style="font-size:0.79rem;direction:rtl;text-align:right;font-weight:600;margin-bottom:3px;">{{ $inc['title_ar'] }}</div>
                  <code style="font-size:0.72rem;color:#64748b;word-break:break-word;">{{ $inc['message'] }}</code>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
