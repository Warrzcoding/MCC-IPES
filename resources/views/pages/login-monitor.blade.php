{{-- Login Monitoring Tracker UI (Frontend-only enhancement) --}}
<style>
  .enhanced-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
  .tab-separator { border: none; height: 2px; background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%); margin: 0.5rem 2rem 0.75rem 2rem; border-radius: 2px; }
  .back-btn { background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; border: none !important; border-radius: 10px !important; padding: 0.75rem 1.5rem !important; font-weight: 600 !important; transition: all 0.3s ease !important; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3) !important; color: white !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; min-width: 120px !important; }
  .back-btn:hover { transform: translateY(-2px) !important; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4) !important; color: white !important; text-decoration: none !important; }
  .search-container { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 1.25rem; }

  /* Summary cards */
  .stat-card { background: #fff; border-radius: 12px; padding: 1rem; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
  .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; }
  .bg-gradient-primary { background: linear-gradient(135deg, #4f46e5, #6366f1); }
  .bg-gradient-success { background: linear-gradient(135deg, #10b981, #34d399); }
  .bg-gradient-danger { background: linear-gradient(135deg, #ef4444, #f87171); }

  /* Tabs */
  .tab-nav .btn { border-radius: 10px; font-weight: 600; }
  .tab-nav .btn.active { background: #0d6efd; color: #fff; box-shadow: 0 6px 20px rgba(13,110,253,0.35); }

  /* Timeline */
  .timeline { position: relative; margin: 0; padding: 0 0 0 28px; list-style: none; }
  .timeline::before { content: ""; position: absolute; top: 0; left: 10px; width: 2px; height: 100%; background: #e9ecef; }
  .timeline-item { position: relative; margin-bottom: 18px; background: #fff; border-radius: 12px; padding: 0.85rem 1rem 0.85rem 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
  .timeline-item .marker { position: absolute; left: -6px; top: 14px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .marker-success { background: #16a34a; }
  .marker-failed { background: #dc2626; }
  .attempt-meta { color: #6b7280; font-size: 0.9rem; }
  .ua-muted { color: #9ca3af; font-size: 0.85rem; }
  .status-badge { font-size: 0.75rem; padding: 0.35rem 0.55rem; border-radius: 999px; font-weight: 700; letter-spacing: .2px; }
  .status-success { background: rgba(34,197,94,0.12); color: #15803d; border: 1px solid rgba(34,197,94,0.25); }
  .status-failed { background: rgba(239,68,68,0.12); color: #b91c1c; border: 1px solid rgba(239,68,68,0.25); }

  /* Utilities */
  .truncate-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
  .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

@php
  $collection = isset($loginAttempts) ? (method_exists($loginAttempts, 'getCollection') ? $loginAttempts->getCollection() : collect($loginAttempts)) : collect();
  $pageSuccess = $collection->where('status', 'success')->count();
  $pageFailed  = $collection->where('status', 'failed')->count();
  $totalCount  = isset($loginAttempts) && method_exists($loginAttempts, 'total') ? $loginAttempts->total() : $collection->count();
@endphp

<div class="row">
  <div class="col-12">
    <div class="enhanced-card">
      <div class="card-body p-0">
        <div class="p-4 pb-0">
          <h5 class="mb-2">
            <i class="fas fa-user-shield me-2"></i> Login Monitoring
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
                <input id="lm-search" type="text" class="form-control" placeholder="Search by user, email, IP or agent...">
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
                        <i class="far fa-clock me-1"></i> {{ $when }}
                      </div>
                      <div class="ua-muted truncate-2 mt-1"><i class="fas fa-desktop me-1"></i>{{ \Illuminate\Support\Str::limit($ua, 160) }}</div>
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

            @if(isset($loginAttempts) && method_exists($loginAttempts, 'links'))
              <div class="d-flex justify-content-end mt-3">
                {{ $loginAttempts->links() }}
              </div>
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
                    <th>User Agent</th>
                    <th>Result</th>
                    <th>When</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(($loginAttempts ?? []) as $attempt)
                    @php
                      $name = $attempt->user->full_name ?? '-';
                      $ua = $attempt->user_agent ?? '-';
                    @endphp
                    <tr class="lm-item" data-status="{{ $attempt->status }}" data-text="{{ strtolower(trim(($name.' ') . (($attempt->email ?? '-') . ' ') . (($attempt->ip_address ?? '-') . ' ') . ($ua))) }}">
                      <td>{{ ($loginAttempts->firstItem() ?? 1) + $loop->index }}</td>
                      <td class="truncate-1" title="{{ $name }}">{{ $name }}</td>
                      <td>{{ $attempt->email ?? '-' }}</td>
                      <td>{{ $attempt->ip_address ?? '-' }}</td>
                      <td class="truncate-1" title="{{ $ua }}">{{ \Illuminate\Support\Str::limit($ua, 80) }}</td>
                      <td>
                        @if($attempt->status === 'success')
                          <span class="badge bg-success">Success</span>
                        @else
                          <span class="badge bg-danger">Failed</span>
                        @endif
                      </td>
                      <td>{{ $attempt->created_at?->diffForHumans() }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center text-muted">No login attempts recorded yet.</td>
                    </tr>
                  @endforelse
                </tbody>
                @if(isset($loginAttempts) && method_exists($loginAttempts, 'links'))
                <tfoot>
                  <tr>
                    <td colspan="7">
                      <div class="d-flex justify-content-end">
                        {{ $loginAttempts->links() }}
                      </div>
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

<script>
  (function(){
    const $ = (s,ctx=document)=>ctx.querySelector(s);
    const $$ = (s,ctx=document)=>Array.from(ctx.querySelectorAll(s));

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
  })();
</script>