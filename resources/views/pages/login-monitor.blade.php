{{-- Login Monitoring Tracker UI (Frontend-only enhancement) --}}
<style>
  /* Compact scaling for login-monitor (visually reduced density) */
  .compact-scale {
    transform-origin: top center;
    transform: scale(0.92);
    max-width: 100%;
  }
  .compact-scale .search-container { padding: 0.9rem; }
  .compact-scale .stat-card { padding: 0.75rem; }
  .compact-scale .stat-icon { width: 36px; height: 36px; }
  .compact-scale .tab-nav .btn { padding: 0.45rem 0.7rem; font-size: 0.92rem; }
  .compact-scale .timeline-item { padding: 0.6rem 0.8rem; }
  .compact-scale .attempt-meta { font-size: 0.78rem; }
  .compact-scale .ua-muted { font-size: 0.72rem; }
  .compact-scale .btn { padding: 0.375rem 0.6rem; font-size: 0.9rem; }
  .compact-scale table th, .compact-scale table td { padding: 0.45rem 0.6rem; font-size: 0.88rem; }
  .compact-scale .back-btn { padding: 0.45rem 0.9rem !important; min-width: 98px !important; }

  /* Further compact and readable timeline text reductions */
  .compact-scale .timeline-item .fw-semibold { font-size: 0.95rem; }
  .compact-scale .timeline-item .status-badge { font-size: 0.68rem; padding: 0.25rem 0.45rem; }

  /* Table row compactness */
  .compact-scale #attempts-table th, .compact-scale #attempts-table td { font-size: 0.82rem; padding: 0.35rem 0.5rem; }
  .compact-scale #attempts-table td.text-nowrap { max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  /* Action button: make smaller and clearer */
  .compact-scale .view-details { padding: 0.22rem 0.45rem; font-size: 0.78rem; border-radius: 6px; }
  .compact-scale .view-details i { margin-right: 0.35rem; }
  .compact-scale .btn-outline-primary.view-details { border-width: 1px; }
  .compact-scale .delete-attempt { padding: 0.22rem 0.45rem; font-size: 0.78rem; border-radius: 6px; border-width: 1px; }
  .compact-scale .delete-attempt i { margin-right: 0.35rem; }

  /* Small-screen adjustments for compact view */
  @media (max-width: 576px) {
    .compact-scale .timeline-item .fw-semibold { font-size: 0.98rem; }
    .compact-scale #attempts-table th, .compact-scale #attempts-table td { font-size: 0.9rem; padding: 0.4rem 0.5rem; }
  }

  .enhanced-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
  .tab-separator { border: none; height: 2px; background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%); margin: 0.5rem 2rem 0.75rem 2rem; border-radius: 2px; }
  .back-btn { background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; border: none !important; border-radius: 10px !important; padding: 0.75rem 1.5rem !important; font-weight: 600 !important; transition: all 0.3s ease !important; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3) !important; color: white !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; min-width: 120px !important; }
  .back-btn:hover { transform: translateY(-2px) !important; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4) !important; color: white !important; text-decoration: none !important; }
  .search-container { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 1.25rem; }

  /* Summary / dashboard cards — enhanced visuals */
  .stat-card {
    background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 10px 26px rgba(15,23,42,0.06);
    border-left: 4px solid rgba(79,70,229,0.18); /* stronger accent */
    transition: transform 0.18s ease, box-shadow 0.18s ease;
    overflow: hidden;
  }
  .stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 22px 46px rgba(15,23,42,0.10);
  }
  .stat-card .text-muted.small { color: #52575b; opacity: 0.98; }
  .stat-card .h5 { font-weight: 800; margin-bottom: 0.12rem; color: #0b1220; }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 8px 22px rgba(79,70,229,0.14);
    font-size: 1.05rem;
    flex-shrink: 0;
  }
  /* Make icon gradients slightly more prominent when present */
  .stat-icon.bg-gradient-primary { box-shadow: 0 8px 22px rgba(79,70,229,0.12); }
  .stat-icon.bg-gradient-success { box-shadow: 0 8px 22px rgba(16,185,129,0.12); }
  .stat-icon.bg-gradient-danger { box-shadow: 0 8px 22px rgba(239,68,68,0.12); }
  .bg-gradient-primary { background: linear-gradient(135deg, #3730a3, #4f46e5); }
  .bg-gradient-success { background: linear-gradient(135deg, #059669, #10b981); }
  .bg-gradient-danger { background: linear-gradient(135deg, #dc2626, #ef4444); }

  /* Tabs */
  .tab-nav .btn { border-radius: 10px; font-weight: 600; }
  .tab-nav .btn.active { background: #0d6efd; color: #fff; box-shadow: 0 6px 20px rgba(13,110,253,0.35); }

  /* stronger active tab contrast */
  .tab-nav .btn.active { background: linear-gradient(135deg, #0b5ed7, #0d6efd); color: #fff; }

  /* Timeline */
  .timeline { position: relative; margin: 0; padding: 0 0 0 28px; list-style: none; }
  .timeline::before { content: ""; position: absolute; top: 0; left: 10px; width: 2px; height: 100%; background: #e9ecef; }
  .timeline-item { position: relative; margin-bottom: 18px; background: #fff; border-radius: 12px; padding: 0.85rem 1rem 0.85rem 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
  .timeline-item .marker { position: absolute; left: -6px; top: 14px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .marker-success { background: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.18); }
  .marker-failed { background: #ef4444; box-shadow: 0 2px 8px rgba(239,68,68,0.18); }
  .attempt-meta { color: #6b7280; font-size: 0.9rem; }
  .ua-muted { color: #9ca3af; font-size: 0.85rem; }
  .status-badge { font-size: 0.75rem; padding: 0.35rem 0.55rem; border-radius: 999px; font-weight: 700; letter-spacing: .2px; }
  .status-success { background: rgba(16,185,129,0.14); color: #065f46; border: 1px solid rgba(16,185,129,0.28); }
  .status-failed { background: rgba(239,68,68,0.14); color: #7f1d1d; border: 1px solid rgba(239,68,68,0.28); }

  /* Utilities */
  .truncate-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
  .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

  /* SweetAlert button container styling */
  .swal-button-container {
    gap: 1rem !important;
    display: flex !important;
    justify-content: center !important;
    margin-top: 1.5rem !important;
  }
  
  .swal-button-container .btn-sm {
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem !important;
  }
</style>

@php
  // Build base collection from paginator or array
  $collection = isset($loginAttempts) ? (method_exists($loginAttempts, 'getCollection') ? $loginAttempts->getCollection() : collect($loginAttempts)) : collect();

  // Deduplicate attempts by a stable fingerprint to avoid double-success display
  $collection = $collection->unique(function ($item) {
      return strtolower(
          ($item->status ?? '-') . '|' .
          ($item->email ?? '-') . '|' .
          ($item->ip_address ?? '-') . '|' .
          ($item->user_agent ?? '-') . '|' .
          optional($item->created_at)->format('Y-m-d H:i:s')
      );
  })->values();

  // Page-level counts (based on deduped items)
  $pageSuccess = $collection->where('status', 'success')->count();  
  $pageFailed  = $collection->where('status', 'failed')->count();

  // Keep the global total from paginator if available
  $totalCount  = isset($loginAttempts) && method_exists($loginAttempts, 'total') ? $loginAttempts->total() : $collection->count();
@endphp

<div class="compact-scale">
<div class="row page-full-width">
  <div class="col-12">
    <div class="enhanced-card">
      <div class="card-body p-0">
        <div class="p-4 pb-0">
          <h5 class="mb-2">
            <i class="fas fa-user-shield me-2"></i> Login Attempts Monitoring
          </h5>
          <p class="text-muted mb-3">Visual tracker of recent login activity with filters and detailed view.</p>
        </div>

        <hr class="tab-separator">

        <div class="p-4 pt-0">
          {{-- Summary Stats --}}
          <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Total Attempts</div>
                  <div class="h5 mb-0">{{ number_format($totalCount) }}</div>
                </div>
                <div class="stat-icon bg-gradient-primary"><i class="fas fa-list"></i></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Page Success</div>
                  <div class="h5 mb-0 text-success">{{ number_format($pageSuccess) }}</div>
                </div>
                <div class="stat-icon bg-gradient-success"><i class="fas fa-check"></i></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Page Failed</div>
                  <div class="h5 mb-0 text-danger">{{ number_format($pageFailed) }}</div>
                </div>
                <div class="stat-icon bg-gradient-danger"><i class="fas fa-times"></i></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Showing</div>
                  <div class="h5 mb-0">{{ $collection->count() }} / {{ number_format($totalCount) }}</div>
                </div>
                <div class="stat-icon bg-gradient-primary"><i class="fas fa-eye"></i></div>
              </div>
            </div>
          </div>

          {{-- Filters --}}
          <div class="search-container">
            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary mb-1"><i class="fas fa-search me-2"></i>Search</label>
                <input id="lm-search" type="text" class="form-control" placeholder="Search by user, email, IP, location or agent...">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold text-primary mb-1">Status</label>
                <select id="lm-status" class="form-select">
                  <option value="">All</option>
                  <option value="success">Success</option>
                  <option value="failed">Failed</option>
                </select>
              </div>
              <div class="col-md-3 text-end">
                <a href="{{ route('dashboard', ['page' => 'login-monitor']) }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Refresh</a>
              </div>
            </div>
          </div>

          {{-- Tabs --}}
          <div class="d-flex gap-2 tab-nav mb-3">
            <button type="button" class="btn btn-light active" data-tab="timeline"><i class="fas fa-stream me-1"></i> Timeline</button>
            <button type="button" class="btn btn-light" data-tab="table"><i class="fas fa-table me-1"></i> Table</button>
          </div>

          {{-- Timeline View --}}
          <div id="tab-timeline">
            <ul class="timeline" id="timeline-list">
              @forelse($collection as $attempt)
                @php
                  $isSuccess = ($attempt->status === 'success');
                  $name = $attempt->user->full_name ?? null;
                  $email = $attempt->email ?? '-';
                  $displayName = $name ?: $email;
                  $ua = $attempt->user_agent ?? '-';
                  $ip = $attempt->ip_address ?? '-';
                  $when = $attempt->created_at?->diffForHumans() ?? '-';
                  $rawText = strtolower(trim(($name ? ($name.' ') : '') . ($email.' ') . ($ip.' ') . ($ua)));
                @endphp
                <li class="timeline-item lm-item" data-status="{{ $attempt->status }}" data-text="{{ $rawText }}">
                  <span class="marker {{ $isSuccess ? 'marker-success' : 'marker-failed' }}"></span>
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="me-2">
                      <div class="fw-semibold truncate-1">
                        <i class="fas {{ $isSuccess ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-1"></i>
                        {{ $displayName }}
                        <span class="status-badge {{ $isSuccess ? 'status-success' : 'status-failed' }} ms-2 text-uppercase">{{ $attempt->status }}</span>
                      </div>
                      <div class="attempt-meta mt-1">
                        <i class="fas fa-envelope me-1"></i> {{ $email }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-network-wired me-1"></i> {{ $ip }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-map-marker-alt me-1"></i> {{ $attempt->location ?? 'Unknown' }}
                        <span class="mx-2">|</span>
                        <i class="far fa-clock me-1"></i> {{ $when }}
                      </div>
                      <div class="ua-muted truncate-2 mt-1"><i class="fas fa-desktop me-1"></i>{{ \Illuminate\Support\Str::limit($ua, 160) }}</div>
                    </div>
                    <div class="align-self-start d-flex gap-2">
                      <button class="btn btn-sm btn-outline-primary view-details" data-id="{{ $attempt->id ?? $loop->index }}" data-email="{{ $email }}" data-ip="{{ $ip }}" data-location="{{ $attempt->location ?? 'Unknown' }}" data-latitude="{{ $attempt->latitude ?? '' }}" data-longitude="{{ $attempt->longitude ?? '' }}" data-usertype="{{ $attempt->user->usertype ?? 'N/A' }}" data-ua="{{ $ua }}">
                        <i class="fas fa-eye"></i> View
                      </button>
                      <button class="btn btn-sm btn-outline-danger delete-attempt" data-id="{{ $attempt->id }}" data-email="{{ $email }}">
                        <i class="fas fa-trash"></i> Delete
                      </button>
                    </div>
                  </div>
                </li>
              @empty
                <li class="timeline-item">
                  <span class="marker marker-success"></span>
                  <div class="text-muted">No login attempts recorded yet.</div>
                </li>
              @endforelse
            </ul>

            @if(isset($loginAttempts) && method_exists($loginAttempts, 'hasPages') && $loginAttempts->hasPages())
              <nav class="mt-3" aria-label="Timeline pagination">
                <ul class="pagination justify-content-end mb-0">
                  <li class="page-item {{ $loginAttempts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $loginAttempts->previousPageUrl() }}" rel="prev">
                      <i class="fas fa-chevron-left me-1"></i> Previous
                    </a>
                  </li>
                  <li class="page-item {{ $loginAttempts->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $loginAttempts->nextPageUrl() }}" rel="next">
                      Next <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            @endif
          </div>

          {{-- Table View --}}
          <div id="tab-table" class="d-none">
            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="attempts-table">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Location</th>
                    <th>User Agent</th>
                    <th>Result</th>
                    <th>When</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(($loginAttempts ?? []) as $attempt)
                    @php
                      $name = $attempt->user->full_name ?? '-';
                      $email = $attempt->email ?? '-';
                      $ua = $attempt->user_agent ?? '-';
                      $location = $attempt->location ?? '-';
                      $ipAddress = $attempt->ip_address ?? '-';
                    @endphp
                    <tr class="lm-item" data-status="{{ $attempt->status }}" data-text="{{ strtolower(trim(($name.' ') . ($email.' ') . ($ipAddress.' ') . ($ua) . ' ' . ($location))) }}">
                      <td>{{ ($loginAttempts->firstItem() ?? 1) + $loop->index }}</td>
                      <td class="text-nowrap" title="{{ $name }}">{{ $name }}</td>
                      <td class="text-nowrap" title="{{ $email }}">{{ \Illuminate\Support\Str::limit($email, 60) }}</td>
                      <td>{{ $ipAddress }}</td>
                      <td class="text-nowrap" title="{{ $location }}">{{ \Illuminate\Support\Str::limit($location, 60) }}</td>
                      <td class="text-nowrap" title="{{ $ua }}">{{ \Illuminate\Support\Str::limit($ua, 80) }}</td>
                      <td>
                        @if($attempt->status === 'success')
                          <span class="badge bg-success">Success</span>
                        @else
                          <span class="badge bg-danger">Failed</span>
                        @endif
                      </td>
                      <td>{{ $attempt->created_at?->diffForHumans() }}</td>
                      <td>
                        <div class="d-flex gap-2">
                          <button class="btn btn-sm btn-outline-primary view-details" data-id="{{ $attempt->id ?? $loop->index }}" data-email="{{ $attempt->email ?? '-' }}" data-ip="{{ $attempt->ip_address ?? '-' }}" data-location="{{ $attempt->location ?? 'Unknown' }}" data-latitude="{{ $attempt->latitude ?? '' }}" data-longitude="{{ $attempt->longitude ?? '' }}" data-usertype="{{ $attempt->user->usertype ?? 'N/A' }}" data-ua="{{ $ua }}">
                            <i class="fas fa-eye"></i> View
                          </button>
                          <button class="btn btn-sm btn-outline-danger delete-attempt" data-id="{{ $attempt->id }}" data-email="{{ $attempt->email ?? '-' }}">
                            <i class="fas fa-trash"></i> Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted">No login attempts recorded yet.</td>
                    </tr>
                  @endforelse
                </tbody>
                @if(isset($loginAttempts) && method_exists($loginAttempts, 'hasPages') && $loginAttempts->hasPages())
                <tfoot>
                  <tr>
                    <td colspan="9">
                      <nav aria-label="Table pagination">
                        <ul class="pagination justify-content-end mb-0">
                          <li class="page-item {{ $loginAttempts->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $loginAttempts->previousPageUrl() }}" rel="prev">
                              <i class="fas fa-chevron-left me-1"></i> Previous
                            </a>
                          </li>
                          <li class="page-item {{ $loginAttempts->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $loginAttempts->nextPageUrl() }}" rel="next">
                              Next <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                          </li>
                        </ul>
                      </nav>
                    </td>
                  </tr>
                </tfoot>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="text-start mt-2 pb-3 px-4">
        <a href="{{ route('dashboard', ['page' => 'add-students']) }}" class="btn back-btn">
          <i class="fas fa-arrow-left me-2"></i>
          Back to Students Management
        </a>
      </div>
    </div>
  </div>
</div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Login Attempt Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-5">
            <h6>Attempt Information</h6>
            <table class="table table-sm">
              <tr>
                <th>Email:</th>
                <td id="modal-email">-</td>
              </tr>
              <tr>
                <th>IP Address:</th>
                <td id="modal-ip">-</td>
              </tr>
              <tr>
                <th>Location:</th>
                <td id="modal-location">-</td>
              </tr>
              <tr>
                <th>User Type:</th>
                <td id="modal-usertype">-</td>
              </tr>
              <tr>
                <th>User Agent:</th>
                <td id="modal-ua">-</td>
              </tr>
            </table>
          </div>
          <div class="col-md-7">
            <h6>Geolocation Mapping</h6>
            <div id="map" style="height: 400px; width: 100%; border-radius: 8px; overflow: hidden; position: relative; background: #f8f9fa;"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Map Styling -->
<style>
  /* Ensure map container has proper dimensions */
  #map {
    min-height: 400px !important;
    height: 400px !important;
    width: 100% !important;
    position: relative !important;
    display: block !important;
    background-color: #f8f9fa !important;
    border-radius: 8px !important;
    overflow: hidden !important;
  }
  
  /* Fix for Leaflet in modals - ensure proper display */
  #detailsModal #map {
    min-height: 320px !important;
    max-height: 100% !important;
    height: 100% !important;
    width: 100% !important;
    display: block !important;
  }

  /* Leaflet container must have dimensions */
  #map .leaflet-container {
    height: 100% !important;
    width: 100% !important;
    display: block !important;
  }

  /* Fix for Mapbox in modals */
  #map .mapboxgl-map {
    height: 100% !important;
    width: 100% !important;
  }
  
  /* Ensure map is visible */
  #map, #map * {
    box-sizing: border-box;
  }
  
  /* Loading state */
  #map:empty::before {
    content: "Loading map...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6c757d;
    font-size: 14px;
    z-index: 1;
  }

  /* Compact modal text & controls for details view */
  #detailsModal .modal-content {
    font-size: 0.90rem; /* slightly reduced overall */
  }
  #detailsModal .modal-title {
    font-size: 1.0rem;
    font-weight: 700;
  }
  #detailsModal .modal-body table th {
    font-size: 0.82rem;
    width: 32%;
    vertical-align: top;
    padding: 0.35rem 0.5rem;
  }
  #detailsModal .modal-body table td {
    font-size: 0.88rem;
    padding: 0.35rem 0.5rem;
  }
  #detailsModal .modal-body h6 { font-size: 0.95rem; }
  #detailsModal .modal-footer .btn { padding: 0.35rem 0.6rem; font-size: 0.86rem; }

  /* Ensure modal body layout is correct */
  #detailsModal .modal-body {
    min-height: 350px;
    display: flex;
    align-items: stretch;
  }

  #detailsModal .modal-body .row {
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 1.5rem;
    align-items: start;
  }

  #detailsModal .modal-body .row .col-md-5 {
    display: flex;
    flex-direction: column;
    min-height: fit-content;
  }

  #detailsModal .modal-body .row .col-md-7 {
    display: flex;
    flex-direction: column;
    min-height: 100%;
  }

  #detailsModal .modal-body .row .col-md-7 #map {
    flex: 1;
    min-height: 320px;
    height: 100%;
    width: 100%;
  }

  @media (max-width: 992px) {
    #detailsModal .modal-body .row {
      grid-template-columns: 1fr;
      min-height: auto;
    }

    #detailsModal .modal-body .row .col-md-7 #map {
      min-height: 300px;
      height: 300px;
    }
  }
</style>

<!-- Enhanced Mapping with Multiple Providers -->
@if(config('services.google_maps.api_key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}"></script>
@elseif(config('services.mapbox.access_token'))
<!-- Mapbox GL JS (High-quality satellite imagery) -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
@else
<!-- Multiple Free Mapping Providers with Satellite Support -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Esri Leaflet for ArcGIS satellite imagery -->
<script src="https://unpkg.com/esri-leaflet@3.0.10/dist/esri-leaflet.js"></script>
@endif
<script>
  (function(){
    const $ = (s,ctx=document)=>ctx.querySelector(s);
    const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));

    let map;
    let marker;

    function destroyExistingMap() {
      console.log('Destroying existing map instance');
      if (map) {
        try {
          if (typeof map.remove === 'function') {
            console.log('Calling map.remove()');
            map.remove();
          } else if (typeof map.destroy === 'function') {
            console.log('Calling map.destroy()');
            map.destroy();
          } else if (typeof map.unmount === 'function') {
            console.log('Calling map.unmount()');
            map.unmount();
          }
        } catch (error) {
          console.warn('Error tearing down previous map instance:', error);
        }
      }

      const mapContainer = $('#map');
      if (mapContainer) {
        console.log('Clearing map container HTML');
        // Remove all children including Leaflet-generated elements
        while (mapContainer.firstChild) {
          mapContainer.removeChild(mapContainer.firstChild);
        }
        // Reset inline styles
        mapContainer.style.height = '';
        mapContainer.style.width = '';
      }

      map = null;
      marker = null;
    }

    function renderMapMessage(messageHtml) {
      destroyExistingMap();
      const mapContainer = $('#map');
      if (mapContainer) {
        mapContainer.innerHTML = messageHtml;
      }
    }

    function normalizeCoordinate(coord) {
      if (coord === undefined || coord === null) {
        return '';
      }
      return String(coord).trim();
    }

    function dispatchMapEvent(name, detail = {}) {
      document.dispatchEvent(new CustomEvent(name, { detail }));
    }

    function initMap(lat, lng, meta = {}) {
      console.log('Initializing map with coordinates:', lat, lng, meta);

      const normalizedLat = normalizeCoordinate(lat);
      const normalizedLng = normalizeCoordinate(lng);

      let latitude, longitude, useDefaultLocation = false;

      // Check if coordinates are missing or invalid
      if (
        !normalizedLat ||
        !normalizedLng ||
        normalizedLat.toLowerCase() === 'null' ||
        normalizedLng.toLowerCase() === 'null' ||
        normalizedLat.toLowerCase() === 'undefined' ||
        normalizedLng.toLowerCase() === 'undefined'
      ) {
        console.log('No coordinates provided, using default world view');
        latitude = 20;
        longitude = 0;
        useDefaultLocation = true;
      } else {
        latitude = parseFloat(normalizedLat);
        longitude = parseFloat(normalizedLng);

        if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
          console.log('Invalid coordinates, using default world view');
          latitude = 20;
          longitude = 0;
          useDefaultLocation = true;
        }
      }

      destroyExistingMap();

      console.log('Using coordinates:', latitude, longitude, 'Default location:', useDefaultLocation);

      const container = document.getElementById('map');
      if (!container) {
        console.warn('Map container not found.');
        dispatchMapEvent('loginMonitor:mapUnavailable', { reason: 'missing-container', meta });
        return;
      }

      let providerUsed = 'none';

      if (typeof google !== 'undefined' && google.maps) {
        providerUsed = 'google';
        const location = { lat: latitude, lng: longitude };
        const zoomLevel = useDefaultLocation ? 2 : 12;
        map = new google.maps.Map(container, {
          zoom: zoomLevel,
          center: location,
          mapTypeControl: true,
          streetViewControl: true,
          fullscreenControl: true
        });
        if (!useDefaultLocation) {
          marker = new google.maps.Marker({
            position: location,
            map: map,
            title: 'Login Location'
          });
        }
      } else if (typeof mapboxgl !== 'undefined') {
        providerUsed = 'mapbox';
        mapboxgl.accessToken = '{{ config("services.mapbox.access_token") }}';
        const zoomLevel = useDefaultLocation ? 2 : 12;
        map = new mapboxgl.Map({
          container: 'map',
          style: 'mapbox://styles/mapbox/satellite-streets-v12',
          center: [longitude, latitude],
          zoom: zoomLevel
        });
        map.addControl(new mapboxgl.NavigationControl());
        map.on('load', function() {
          const layerList = document.createElement('div');
          layerList.className = 'mapboxgl-ctrl mapboxgl-ctrl-group';
          layerList.style.position = 'absolute';
          layerList.style.top = '10px';
          layerList.style.right = '10px';
          layerList.style.background = 'white';
          layerList.style.borderRadius = '4px';
          layerList.style.padding = '5px';
          layerList.innerHTML = `
            <select onchange="map.setStyle('mapbox://styles/mapbox/' + this.value)" style="border:none;background:transparent;">
              <option value="satellite-streets-v12" selected>Satellite</option>
              <option value="streets-v12">Streets</option>
              <option value="outdoors-v12">Outdoors</option>
              <option value="light-v11">Light</option>
              <option value="dark-v11">Dark</option>
            </select>
          `;
          const existingCtrl = container.querySelector('.mapboxgl-ctrl.mapboxgl-ctrl-group');
          if (existingCtrl) {
            existingCtrl.remove();
          }
          container.appendChild(layerList);
        });
        if (!useDefaultLocation) {
          marker = new mapboxgl.Marker()
            .setLngLat([longitude, latitude])
            .setPopup(new mapboxgl.Popup().setHTML(`
              <div class="text-center">
                <strong>Login Location</strong><br>
                <small class="text-muted">Lat: ${latitude.toFixed(6)}<br>Lng: ${longitude.toFixed(6)}</small>
              </div>
            `))
            .addTo(map);
        }
      } else if (typeof L !== 'undefined') {
        providerUsed = 'leaflet';
        console.log('Initializing Leaflet map');
        console.log('Map container info:', {
          element: container,
          computed: window.getComputedStyle(container),
          height: container.offsetHeight,
          width: container.offsetWidth,
          display: window.getComputedStyle(container).display,
          visibility: window.getComputedStyle(container).visibility
        });
        
        try {
          // Ensure container has proper dimensions
          if (container.offsetHeight === 0 || container.offsetWidth === 0) {
            console.warn('Map container has zero dimensions, applying fallback styles');
            container.style.height = '320px';
            container.style.width = '100%';
            container.style.display = 'block';
          }
          
          // Force reflow to ensure CSS is applied
          void container.offsetHeight;
          
          const zoomLevel = useDefaultLocation ? 2 : 12;
          map = L.map('map', {
            center: [latitude, longitude],
            zoom: zoomLevel,
            zoomControl: true,
            attributionControl: true,
            preferCanvas: true
          });

          console.log('Leaflet map object created, adding tiles');

          const baseLayers = {
            "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
              maxZoom: 19
            }),
            "Satellite (Esri)": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
              attribution: '© <a href="https://www.esri.com/">Esri</a>, DigitalGlobe, GeoEye, Earthstar Geographics, CNES/Airbus DS, USDA, USGS, AeroGRID, IGN, and the GIS User Community',
              maxZoom: 18
            }),
            "Terrain": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
              attribution: '© <a href="https://opentopomap.org/">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
              maxZoom: 17
            }),
            "CartoDB Positron": L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
              attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
              maxZoom: 19
            }),
            "Stamen Toner": L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}{r}.png', {
              attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> — Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
              maxZoom: 18
            })
          };

          baseLayers["Satellite (Esri)"].addTo(map);
          L.control.layers(baseLayers).addTo(map);

          if (!useDefaultLocation) {
            marker = L.marker([latitude, longitude])
              .addTo(map)
              .bindPopup(`
                <div class="text-center">
                  <strong>Login Location</strong><br>
                  <small class="text-muted">Lat: ${latitude.toFixed(6)}<br>Lng: ${longitude.toFixed(6)}</small>
                </div>
              `)
              .openPopup();
          }

          // Force map to recalculate size
          setTimeout(() => {
            if (map && typeof map.invalidateSize === 'function') {
              map.invalidateSize(true);
              console.log('invalidateSize called');
            }
          }, 100);

          const locationStatus = useDefaultLocation ? 'with default world view' : 'successfully';
          console.log('Leaflet map initialized ' + locationStatus + ' at', [latitude, longitude]);
        } catch (error) {
          console.error('Error initializing Leaflet map:', error);
          console.error('Error stack:', error.stack);
          renderMapMessage('<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Error loading map</p></div>');
          dispatchMapEvent('loginMonitor:mapUnavailable', { reason: 'leaflet-error', meta, error: error.message });
          return;
        }
      } else {
        renderMapMessage('<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted mb-0"><i class="fas fa-exclamation-triangle me-2"></i>No mapping service available</p></div>');
        dispatchMapEvent('loginMonitor:mapUnavailable', { reason: 'no-provider', meta });
        return;
      }

      dispatchMapEvent('loginMonitor:mapReady', {
        provider: providerUsed,
        latitude,
        longitude,
        meta,
        useDefaultLocation
      });
    }

    const search = $('#lm-search');
    const status = $('#lm-status');
    const items = $$('.lm-item'); // both timeline li and table tr have this

    function applyFilter(){
      const q = (search.value || '').toLowerCase().trim();
      const st = (status.value || '').toLowerCase();
      items.forEach(el=>{
        const txt = (el.getAttribute('data-text')||'').toLowerCase();
        const s = (el.getAttribute('data-status')||'').toLowerCase();
        const matchQ = !q || txt.includes(q);
        const matchS = !st || s === st;
        el.style.display = (matchQ && matchS) ? '' : 'none';
      });
    }

    if(search) search.addEventListener('input', applyFilter);
    if(status) status.addEventListener('change', applyFilter);

    // Tabs toggle
    const tabButtons = $$('.tab-nav .btn');
    tabButtons.forEach(btn=>{
      btn.addEventListener('click', ()=>{
        tabButtons.forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const tab = btn.getAttribute('data-tab');
        $('#tab-timeline').classList.toggle('d-none', tab !== 'timeline');
        $('#tab-table').classList.toggle('d-none', tab !== 'table');
      });
    });

    // View details modal
    const viewButtons = $$('.view-details');
    viewButtons.forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const email = btn.getAttribute('data-email');
        const ip = btn.getAttribute('data-ip');
        const location = btn.getAttribute('data-location');
        const usertype = btn.getAttribute('data-usertype');
        const ua = btn.getAttribute('data-ua');
        const lat = btn.getAttribute('data-latitude');
        const lng = btn.getAttribute('data-longitude');

        $('#modal-email').textContent = email;
        $('#modal-ip').textContent = ip;
        $('#modal-location').textContent = location;
        $('#modal-usertype').textContent = usertype;
        $('#modal-ua').textContent = ua;

        // Show modal first, then initialize map when modal is fully shown
        const modal = new bootstrap.Modal($('#detailsModal'));
        
        // Store coordinates for later use
        $('#detailsModal').setAttribute('data-lat', lat);
        $('#detailsModal').setAttribute('data-lng', lng);
        
        modal.show();
      });
    });

    // Handle modal events for proper map initialization
    const detailsModal = $('#detailsModal');
    if (detailsModal) {
      detailsModal.addEventListener('shown.bs.modal', function() {
        const lat = this.getAttribute('data-lat');
        const lng = this.getAttribute('data-lng');
        
        console.log('Modal shown event - coordinates:', lat, lng);
        
        // Always initialize map, even without coordinates (will use default world view)
        requestAnimationFrame(() => {
          setTimeout(() => {
            const mapContainer = $('#map');
            if (mapContainer) {
              console.log('Map container before init:', {
                offsetHeight: mapContainer.offsetHeight,
                offsetWidth: mapContainer.offsetWidth,
                display: window.getComputedStyle(mapContainer).display,
                visibility: window.getComputedStyle(mapContainer).visibility
              });
            }
            
            console.log('Initializing map from modal event');
            initMap(lat, lng);
            
            setTimeout(() => {
              if (mapContainer) {
                console.log('Map container after init:', {
                  offsetHeight: mapContainer.offsetHeight,
                  offsetWidth: mapContainer.offsetWidth
                });
              }
              
              if (map && typeof map.invalidateSize === 'function') {
                console.log('Calling invalidateSize for Leaflet');
                map.invalidateSize(true);
              }
              
              if (map && typeof map.resize === 'function') {
                console.log('Calling resize for Mapbox');
                map.resize();
              }
              
              if (map && typeof google !== 'undefined' && google.maps) {
                console.log('Triggering resize for Google Maps');
                google.maps.event.trigger(map, 'resize');
              }
            }, 300);
          }, 50);
        });
      });
      
      // Clean up map when modal is hidden
      detailsModal.addEventListener('hidden.bs.modal', function() {
        console.log('Modal hidden - cleaning up map');
        if (map) {
          // Clean up map instance
          if (typeof map.remove === 'function') {
            map.remove(); // Leaflet cleanup
          } else if (typeof map.destroy === 'function') {
            map.destroy(); // Other map libraries
          }
          map = null;
          marker = null;
        }
        
        // Clear map container
        const mapContainer = $('#map');
        if (mapContainer) {
          mapContainer.innerHTML = '';
        }
      });
    }

    // Delete attempt button handler
    const deleteButtons = $$('.delete-attempt');
    deleteButtons.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        const email = this.getAttribute('data-email');
        
        // SweetAlert confirmation dialog
        Swal.fire({
          title: 'Delete Login Attempt?',
          html: `<p class="text-muted">Are you sure you want to delete the login attempt for <strong>${email}</strong>?</p><p class="small text-danger">This action cannot be undone.</p>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: '<i class="fas fa-trash me-2"></i> Yes, Delete',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          allowOutsideClick: false,
          buttonsStyling: false,
          customClass: {
            confirmButton: 'btn btn-sm btn-danger px-3',
            cancelButton: 'btn btn-sm btn-secondary px-3',
            actions: 'swal-button-container'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
              title: 'Deleting...',
              html: '<p class="text-muted">Please wait while the login attempt is being deleted.</p>',
              icon: 'info',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });

            // Send delete request
            fetch(`/dashboard/login-monitor/${id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Success notification
                Swal.fire({
                  title: 'Deleted!',
                  html: `<p class="mb-0">${data.message}</p>`,
                  icon: 'success',
                  confirmButtonColor: '#28a745',
                  confirmButtonText: 'OK',
                  buttonsStyling: true
                }).then(() => {
                  // Remove the row from the view
                  const itemRow = btn.closest('.lm-item') || btn.closest('tr');
                  if (itemRow) {
                    itemRow.style.transition = 'opacity 0.3s ease';
                    itemRow.style.opacity = '0';
                    setTimeout(() => {
                      itemRow.remove();
                      // Refresh page if no items left
                      const remainingItems = $$('.lm-item').length;
                      if (remainingItems === 0) {
                        setTimeout(() => {
                          window.location.reload();
                        }, 500);
                      }
                    }, 300);
                  }
                });
              } else {
                Swal.fire({
                  title: 'Error!',
                  html: `<p class="text-danger mb-0">${data.message || 'Failed to delete login attempt.'}</p>`,
                  icon: 'error',
                  confirmButtonColor: '#dc3545'
                });
              }
            })
            .catch(error => {
              console.error('Delete error:', error);
              Swal.fire({
                title: 'Error!',
                html: '<p class="text-danger mb-0">An error occurred while deleting. Please try again.</p>',
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
            });
          }
        });
      });
    });
  })();
</script>