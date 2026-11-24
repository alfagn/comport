@extends('layouts.admin')

@section('title', 'Sitemap & URL Inventory')

@section('content')
<div class="content-header">
  <div class="content-header__meta">
    <div class="content-header__badge">
      <img src="{{ asset('assets/images/3d/3d-target.png') }}" alt="" width="20" height="20" />
      <span>Search Engine Indexing</span>
    </div>
    <h1 class="content-header__title">XML Sitemap &amp; URL Inventory</h1>
    <p class="content-header__sub">
      Current CMS inventory for discovery documents. Search engines decide which pages to crawl and index.
    </p>
  </div>
  <div class="content-header__actions">
    <form action="{{ route('admin.sitemap.regenerate') }}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="btn btn--primary btn--sm" title="Refresh XML Sitemap and LLM cache">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        <span>Regenerate Sitemap</span>
      </button>
    </form>
    <form action="{{ route('admin.sitemap.ping') }}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="btn btn--ghost btn--sm" title="Notify search engines of updates">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"/></svg>
        <span>Submission Help</span>
      </button>
    </form>
    <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn--ghost btn--sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      <span>View XML</span>
    </a>
  </div>
</div>

{{-- Top Row: Stats Counters --}}
<div class="sitemap-stats-grid">
  
  <div class="stat-card stat-card--glass">
    <div class="stat-card__icon-wrap">
      <img src="{{ asset('assets/images/3d/3d-target.png') }}" alt="" width="36" height="36" />
    </div>
    <div class="stat-card__info">
      <div class="stat-card__val">{{ $stats['total'] }}</div>
      <div class="stat-card__label">Public URLs Listed</div>
    </div>
  </div>

  <div class="stat-card stat-card--glass">
    <div class="stat-card__icon-wrap">
      <img src="{{ asset('assets/images/3d/3d-folder.png') }}" alt="" width="36" height="36" />
    </div>
    <div class="stat-card__info">
      <div class="stat-card__val">{{ $stats['projects'] }}</div>
      <div class="stat-card__label">SaaS &amp; Client Projects</div>
    </div>
  </div>

  <div class="stat-card stat-card--glass">
    <div class="stat-card__icon-wrap">
      <img src="{{ asset('assets/images/3d/3d-megaphone.png') }}" alt="" width="36" height="36" />
    </div>
    <div class="stat-card__info">
      <div class="stat-card__val">{{ $stats['posts'] }}</div>
      <div class="stat-card__label">Engineering Posts</div>
    </div>
  </div>

  <div class="stat-card stat-card--glass">
    <div class="stat-card__icon-wrap">
      <img src="{{ asset('assets/images/3d/3d-briefcase.png') }}" alt="" width="36" height="36" />
    </div>
    <div class="stat-card__info">
      <div class="stat-card__val">{{ $stats['jobs'] }}</div>
      <div class="stat-card__label">Active Career Roles</div>
    </div>
  </div>

  <div class="stat-card stat-card--glass">
    <div class="stat-card__icon-wrap">
      <img src="{{ asset('assets/images/3d/3d-puzzle-partners.png') }}" alt="" width="36" height="36" />
    </div>
    <div class="stat-card__info">
      <div class="stat-card__val">{{ $stats['founders'] }}</div>
      <div class="stat-card__label">Leadership Profiles</div>
    </div>
  </div>

</div>

{{-- Status & Endpoint Inspection Bar --}}
<div class="sitemap-status-bar card card--glass">
  <div class="sitemap-status-bar__item">
    <span class="status-indicator status-indicator--live"></span>
    <div>
      <strong>Sitemap Status:</strong> Active &amp; Dynamic
      <span class="text-muted">({{ $lastGenerated }})</span>
    </div>
  </div>
  <div class="sitemap-status-bar__links">
    <a href="{{ url('/robots.txt') }}" target="_blank" class="sitemap-status-bar__link">
      <span>robots.txt</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    </a>
    <a href="{{ url('/llms.txt') }}" target="_blank" class="sitemap-status-bar__link">
      <span>llms.txt</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    </a>
    <a href="{{ url('/llms-full.txt') }}" target="_blank" class="sitemap-status-bar__link">
      <span>llms-full.txt</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    </a>
  </div>
</div>

{{-- URLs Inventory Table --}}
<div class="card card--glass sitemap-table-card">
  <div class="card__header">
    <div class="card__header-icon">
      <img src="{{ asset('assets/images/3d/3d-list.png') }}" alt="" width="28" height="28" />
    </div>
    <div>
      <h3 class="card__title">Complete URL Registry</h3>
      <p class="card__sub">Current public URL inventory. Listing here does not confirm crawling, indexing, or AI citations.</p>
    </div>
    <div class="sitemap-filter-wrap">
      <input type="text"
             id="sitemapSearchInput"
             class="form-control form-control--sm"
             placeholder="Filter URLs or titles..."
             oninput="empetixFilterSitemapTable(this.value)" />
    </div>
  </div>
  <div class="card__body p-0">
    <div class="table-responsive">
      <table class="table sitemap-table" id="sitemapTable">
        <thead>
          <tr>
            <th>URL &amp; Page Title</th>
            <th>Type</th>
            <th>Priority</th>
            <th>Frequency</th>
            <th>Last Modified</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($urls as $entry)
            <tr class="sitemap-row" data-search="{{ strtolower($entry['title'] . ' ' . $entry['url'] . ' ' . $entry['type']) }}">
              <td>
                <div class="sitemap-url-cell">
                  @if(!empty($entry['image']))
                    <img class="sitemap-url-cell__thumb" src="{{ $entry['image'] }}" alt="" width="36" height="36" />
                  @else
                    <div class="sitemap-url-cell__ico">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </div>
                  @endif
                  <div>
                    <a href="{{ $entry['url'] }}" target="_blank" class="sitemap-url-cell__title">
                      {{ $entry['title'] }}
                    </a>
                    <span class="sitemap-url-cell__link">{{ $entry['url'] }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge badge--pill badge--type-{{ strtolower(explode(' ', $entry['type'])[0]) }}">
                  {{ $entry['type'] }}
                </span>
              </td>
              <td>
                <div class="sitemap-priority-pill">
                  <span class="sitemap-priority-val">{{ $entry['priority'] }}</span>
                  <div class="sitemap-priority-bar">
                    <div class="sitemap-priority-bar__fill" style="width: {{ floatval($entry['priority']) * 100 }}%;"></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="sitemap-freq-badge">{{ $entry['changefreq'] }}</span>
              </td>
              <td>
                <span class="sitemap-date">
                  {{ $entry['lastmod'] ? \Illuminate\Support\Carbon::parse($entry['lastmod'])->format('M d, Y') : 'Not supplied' }}
                </span>
              </td>
              <td class="text-right">
                <a href="{{ $entry['url'] }}" target="_blank" class="btn btn--ghost btn--xs" title="Open live URL">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                  <span>Visit</span>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function empetixFilterSitemapTable(query) {
    var term = query.toLowerCase().trim();
    var rows = document.querySelectorAll('#sitemapTable tbody tr.sitemap-row');
    rows.forEach(function(row) {
      var hay = row.getAttribute('data-search') || '';
      row.style.display = hay.includes(term) ? '' : 'none';
    });
  }
</script>
@endsection
