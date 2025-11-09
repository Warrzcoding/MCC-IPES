<style>
  .compact-scale { font-size: 0.8rem; max-width: 100%; }
  .compact-scale .search-container { padding: 0.9rem; }
  .compact-scale .stat-card { padding: 0.75rem; }
  .compact-scale .stat-icon { width: 36px; height: 36px; }
  .compact-scale .tab-nav .btn { padding: 0.45rem 0.7rem; font-size: 0.8rem; }
  .compact-scale .timeline-item { padding: 0.6rem 0.8rem; }
  .compact-scale .attempt-meta { font-size: 0.78rem; }
  .compact-scale .ua-muted { font-size: 0.72rem; }
  .compact-scale .btn { padding: 0.375rem 0.6rem; font-size: 0.8rem; }
  .compact-scale table th, .compact-scale table td { padding: 0.45rem 0.6rem; font-size: 0.8rem; }
  .compact-scale input, .compact-scale select { font-size: 0.8rem; }
  .compact-scale .back-btn { padding: 0.45rem 0.9rem !important; min-width: 98px !important; }
  @media (max-width: 576px) {
    .compact-scale .timeline-item .fw-semibold { font-size: 0.98rem; }
    .compact-scale #jobs-table th, .compact-scale #jobs-table td { font-size: 0.9rem; padding: 0.4rem 0.5rem; }
  }
  .enhanced-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
  .tab-separator { border: none; height: 2px; background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%); margin: 0.5rem 2rem 0.75rem 2rem; border-radius: 2px; }
  .back-btn { background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; border: none !important; border-radius: 10px !important; padding: 0.75rem 1.5rem !important; font-weight: 600 !important; transition: all 0.3s ease !important; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3) !important; color: white !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; min-width: 120px !important; }
  .back-btn:hover { transform: translateY(-2px) !important; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4) !important; color: white !important; text-decoration: none !important; }
  .search-container { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 1.25rem; }
  .stat-card { background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%); border-radius: 12px; padding: 1rem; box-shadow: 0 10px 26px rgba(15,23,42,0.06); border-left: 4px solid rgba(79,70,229,0.18); transition: transform 0.18s ease, box-shadow 0.18s ease; overflow: hidden; }
  .stat-card:hover { transform: translateY(-6px); box-shadow: 0 22px 46px rgba(15,23,42,0.10); }
  .stat-card .text-muted.small { color: #52575b; opacity: 0.98; }
  .stat-card .h5 { font-weight: 800; margin-bottom: 0.12rem; color: #0b1220; }
  .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 22px rgba(79,70,229,0.14); font-size: 1.05rem; flex-shrink: 0; }
  .bg-gradient-primary { background: linear-gradient(135deg, #3730a3, #4f46e5); }
  .bg-gradient-success { background: linear-gradient(135deg, #059669, #10b981); }
  .bg-gradient-danger { background: linear-gradient(135deg, #dc2626, #ef4444); }
  .bg-gradient-warning { background: linear-gradient(135deg, #d97706, #f59e0b); }
  .tab-nav .btn { border-radius: 10px; font-weight: 600; }
  .tab-nav .btn.active { background: linear-gradient(135deg, #0b5ed7, #0d6efd); color: #fff; box-shadow: 0 6px 20px rgba(13,110,253,0.35); }
  .timeline { position: relative; margin: 0; padding: 0 0 0 28px; list-style: none; }
  .timeline::before { content: ""; position: absolute; top: 0; left: 10px; width: 2px; height: 100%; background: #e9ecef; }
  .timeline-item { position: relative; margin-bottom: 18px; background: #fff; border-radius: 12px; padding: 0.85rem 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
  .timeline-item .marker { position: absolute; left: -6px; top: 14px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
  .marker-success { background: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.18); }
  .marker-failed { background: #ef4444; box-shadow: 0 2px 8px rgba(239,68,68,0.18); }
  .marker-running { background: #f59e0b; box-shadow: 0 2px 8px rgba(245,158,11,0.18); }
  .attempt-meta { color: #6b7280; font-size: 0.9rem; }
  .ua-muted { color: #9ca3af; font-size: 0.85rem; }
  .status-badge { font-size: 0.75rem; padding: 0.35rem 0.55rem; border-radius: 999px; font-weight: 700; letter-spacing: .2px; }
  .status-success { background: rgba(16,185,129,0.14); color: #065f46; border: 1px solid rgba(16,185,129,0.28); }
  .status-failed { background: rgba(239,68,68,0.14); color: #7f1d1d; border: 1px solid rgba(239,68,68,0.28); }
  .status-running { background: rgba(245,158,11,0.14); color: #92400e; border: 1px solid rgba(245,158,11,0.28); }
  .truncate-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
  .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .action-buttons { display: flex; gap: 0.5rem; justify-content: center; align-items: center; width: 100%; }
  .timeline-item .action-buttons { align-self: center; width: auto; }
  #jobs-table td.actions-cell { text-align: center; }
  #backupDetailsModal .modal-content { font-size: 0.8rem; }
  #backupDetailsModal .modal-title { font-size: 0.8rem; font-weight: 700; }
  #backupDetailsModal .modal-body table th { font-size: 0.8rem; width: 32%; vertical-align: top; padding: 0.35rem 0.5rem; }
  #backupDetailsModal .modal-body table td { font-size: 0.8rem; padding: 0.35rem 0.5rem; word-break: break-word; }
  #backupDetailsModal .modal-footer .btn { padding: 0.35rem 0.6rem; font-size: 0.8rem; }
  #modal-notes { min-height: 100px; background: #f8f9fa; border-radius: 8px; padding: 0.75rem; font-size: 0.8rem; color: #495057; }
  #confirmBackupModal, #backupDetailsModal { font-size: 0.8rem; }
  @media (max-width: 575px) {
    #backupDetailsModal .modal-body table th { font-size: 0.7rem; width: 35%; padding: 0.3rem 0.4rem; }
    #backupDetailsModal .modal-body table td { font-size: 0.75rem; padding: 0.3rem 0.4rem; }
    #modal-notes { margin-top: 1rem; }
  }
</style>

@php
  $collection = isset($backupLogs) ? (method_exists($backupLogs, 'getCollection') ? $backupLogs->getCollection() : collect($backupLogs)) : collect();
  $collection = $collection->unique(function ($item) {
      return strtolower(
          ($item->status ?? '-') . '|' .
          ($item->job_name ?? '-') . '|' .
          ($item->storage_path ?? '-') . '|' .
          optional($item->created_at)->format('Y-m-d H:i:s')
      );
  })->values();
  $totalCount = isset($backupLogs) && method_exists($backupLogs, 'total') ? $backupLogs->total() : $collection->count();
  $completedCount = $collection->where('status', 'completed')->count();
  $failedCount = $collection->where('status', 'failed')->count();
  $runningCount = $collection->where('status', 'running')->count();
@endphp

<div class="compact-scale">
<div class="row page-full-width">
  <div class="col-12">
    <div class="enhanced-card">
      <div class="card-body p-0">
        <div class="p-4 pb-0">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h5 class="mb-2">
                <i class="fas fa-database me-2"></i> Regular Backup Activity
              </h5>
              <p class="text-muted mb-0">Monitor scheduled backups, durations, and outcomes in one place.</p>
            </div>
            @if(Auth::user()->isAdmin())
            <div>
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmBackupModal">
                <i class="fas fa-download me-2"></i>
                Download Full Backup
              </button>
            </div>
            @endif
          </div>

          <!-- Flash Messages -->
          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif

          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif
        </div>

        <hr class="tab-separator">

        <div class="p-4 pt-0">
          <div class="search-container">
            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label fw-bold text-primary mb-1"><i class="fas fa-search me-2"></i>Search</label>
                <input id="rb-search" type="text" class="form-control" placeholder="Search by job name, storage, initiator, or notes">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold text-primary mb-1">Status</label>
                <select id="rb-status" class="form-select">
                  <option value="">All</option>
                  <option value="completed">Completed</option>
                  <option value="running">Running</option>
                  <option value="failed">Failed</option>
                  <option value="pending">Pending</option>
                </select>
              </div>
              <div class="col-md-3 text-end">
                <a href="{{ route('dashboard', ['page' => 'regularbackup']) }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Refresh</a>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 tab-nav mb-3">
            <button type="button" class="btn btn-light active" data-tab="timeline"><i class="fas fa-stream me-1"></i> Timeline</button>
            <button type="button" class="btn btn-light" data-tab="table"><i class="fas fa-table me-1"></i> Table</button>
          </div>

          <div id="tab-timeline">
            <ul class="timeline" id="timeline-list">
              @forelse($collection as $job)
                @php
                  $status = strtolower($job->status ?? 'unknown');
                  $isSuccess = $status === 'completed';
                  $isFailed = $status === 'failed';
                  $jobName = $job->job_name ?? 'Unnamed Backup';
                  $storage = $job->storage_path ?? '-';
                  $initiatedBy = $job->initiated_by ?? 'System';
                  $sizeValue = $job->size_mb ?? ($job->size ?? null);
                  if ($sizeValue !== null) {
                      $sizeLabel = is_numeric($sizeValue) ? number_format((float) $sizeValue, 2) . ' MB' : (string) $sizeValue;
                  } else {
                      $sizeLabel = 'N/A';
                  }
                  $startedAt = $job->started_at ?? ($job->created_at ?? null);
                  $completedAt = $job->completed_at ?? null;
                  $startedLabel = $startedAt ? optional($startedAt)->diffForHumans() : '-';
                  $completedLabel = $completedAt ? optional($completedAt)->diffForHumans() : $startedLabel;
                  $durationSeconds = $job->duration_seconds ?? null;
                  $durationLabel = $job->duration_human ?? null;
                  if (!$durationLabel && $durationSeconds !== null) {
                      $durationLabel = gmdate('H:i:s', max(0, (int) $durationSeconds));
                  }
                  $durationLabel = $durationLabel ?? ($job->duration ?? '-');
                  $notes = trim((string) ($job->notes ?? ''));
                  $rawText = strtolower(trim($jobName . ' ' . $storage . ' ' . $initiatedBy . ' ' . $sizeLabel . ' ' . $notes));
                  $statusClass = $isSuccess ? 'status-success' : ($isFailed ? 'status-failed' : 'status-running');
                  $markerClass = $isSuccess ? 'marker-success' : ($isFailed ? 'marker-failed' : 'marker-running');
                  $statusLabel = strtoupper($status);
                  $startTimestamp = $startedAt instanceof \Carbon\Carbon ? $startedAt->format('Y-m-d H:i:s') : (is_string($startedAt) ? $startedAt : '-');
                  $completeTimestamp = $completedAt instanceof \Carbon\Carbon ? $completedAt->format('Y-m-d H:i:s') : (is_string($completedAt) ? $completedAt : '-');
                @endphp
                <li class="timeline-item backup-item" data-status="{{ $status }}" data-text="{{ $rawText }}">
                  <span class="marker {{ $markerClass }}"></span>
                  <div class="d-flex justify-content-between align-items-start flex-column flex-md-row">
                    <div class="me-md-2 flex-grow-1">
                      <div class="fw-semibold truncate-1 mb-1">
                        <i class="fas {{ $isSuccess ? 'fa-check-circle text-success' : ($isFailed ? 'fa-times-circle text-danger' : 'fa-hourglass-half text-warning') }} me-1"></i>
                        {{ $jobName }}
                        <span class="status-badge {{ $statusClass }} ms-2 text-uppercase">{{ $statusLabel }}</span>
                      </div>
                      <div class="attempt-meta mt-1">
                        <i class="fas fa-server me-1"></i> {{ $storage }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-weight-hanging me-1"></i> {{ $sizeLabel }}
                        <span class="mx-2">|</span>
                        <i class="far fa-clock me-1"></i> {{ $completedLabel }}
                      </div>
                      <div class="ua-muted truncate-2 mt-1">
                        <i class="fas fa-user-cog me-1"></i>{{ \Illuminate\Support\Str::limit($initiatedBy, 80) }}
                      </div>
                      <div class="ua-muted truncate-2 mt-1">
                        <i class="fas fa-sticky-note me-1"></i>{{ \Illuminate\Support\Str::limit($notes !== '' ? $notes : 'No notes provided.', 160) }}
                      </div>
                    </div>
                    <div class="action-buttons mt-3 mt-md-0">
                      <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="{{ $job->id }}" data-name="{{ $jobName }}">
                        <i class="fas fa-trash"></i> Delete
                      </button>
                    </div>
                  </div>
                </li>
              @empty
                <li class="timeline-item">
                  <span class="marker marker-success"></span>
                  <div class="text-muted">No backup jobs recorded yet.</div>
                </li>
              @endforelse
            </ul>
          </div>

          <div id="tab-table" class="d-none">
            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="jobs-table">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Job</th>
                    <th>Storage</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Started</th>
                    <th>Completed</th>
                    <th>Initiated By</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(($backupLogs ?? []) as $job)
                    @php
                      $status = strtolower($job->status ?? 'unknown');
                      $isSuccess = $status === 'completed';
                      $isFailed = $status === 'failed';
                      $jobName = $job->job_name ?? 'Unnamed Backup';
                      $storage = $job->storage_path ?? '-';
                      $initiatedBy = $job->initiated_by ?? 'System';
                      $sizeValue = $job->size_mb ?? ($job->size ?? null);
                      if ($sizeValue !== null) {
                          $sizeLabel = is_numeric($sizeValue) ? number_format((float) $sizeValue, 2) . ' MB' : (string) $sizeValue;
                      } else {
                          $sizeLabel = 'N/A';
                      }
                      $startedAt = $job->started_at ?? ($job->created_at ?? null);
                      $completedAt = $job->completed_at ?? null;
                      $startedLabel = $startedAt instanceof \Carbon\Carbon ? $startedAt->format('Y-m-d H:i:s') : (is_string($startedAt) ? $startedAt : '-');
                      $completedLabel = $completedAt instanceof \Carbon\Carbon ? $completedAt->format('Y-m-d H:i:s') : (is_string($completedAt) ? $completedAt : '-');
                      $durationSeconds = $job->duration_seconds ?? null;
                      $durationLabel = $job->duration_human ?? null;
                      if (!$durationLabel && $durationSeconds !== null) {
                          $durationLabel = gmdate('H:i:s', max(0, (int) $durationSeconds));
                      }
                      $durationLabel = $durationLabel ?? ($job->duration ?? '-');
                      $notes = trim((string) ($job->notes ?? ''));
                      $rawText = strtolower(trim($jobName . ' ' . $storage . ' ' . $initiatedBy . ' ' . $sizeLabel . ' ' . $notes));
                      $statusBadge = $isSuccess ? 'success' : ($isFailed ? 'danger' : 'warning');
                      $statusLabel = strtoupper($status);
                    @endphp
                    <tr class="backup-item" data-status="{{ $status }}" data-text="{{ $rawText }}">
                      <td>{{ ($backupLogs->firstItem() ?? 1) + $loop->index }}</td>
                      <td class="text-nowrap" title="{{ $jobName }}">{{ \Illuminate\Support\Str::limit($jobName, 60) }}</td>
                      <td class="text-nowrap" title="{{ $storage }}">{{ \Illuminate\Support\Str::limit($storage, 60) }}</td>
                      <td>{{ $sizeLabel }}</td>
                      <td>
                        <span class="badge bg-{{ $statusBadge }}">{{ $statusLabel }}</span>
                      </td>
                      <td class="text-nowrap">{{ $startedLabel }}</td>
                      <td class="text-nowrap">{{ $completedLabel }}</td>
                      <td class="text-nowrap" title="{{ $initiatedBy }}">{{ \Illuminate\Support\Str::limit($initiatedBy, 60) }}</td>
                      <td class="actions-cell text-center">
                        <div class="action-buttons">
                          <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="{{ $job->id }}" data-name="{{ $jobName }}">
                            <i class="fas fa-trash"></i> Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted">No backup jobs recorded yet.</td>
                    </tr>
                  @endforelse
                </tbody>
                @if(isset($backupLogs) && method_exists($backupLogs, 'hasPages') && $backupLogs->hasPages())
                <tfoot>
                  <tr>
                    <td colspan="9">
                      <nav aria-label="Table pagination">
                        <ul class="pagination justify-content-end mb-0">
                          <li class="page-item {{ $backupLogs->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $backupLogs->previousPageUrl() }}" rel="prev">
                              <i class="fas fa-chevron-left me-1"></i> Previous
                            </a>
                          </li>
                          <li class="page-item {{ $backupLogs->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $backupLogs->nextPageUrl() }}" rel="next">
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

<!-- Confirmation Modal for Backup Download -->
<div class="modal fade" id="confirmBackupModal" tabindex="-1" aria-labelledby="confirmBackupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmBackupModalLabel">
          <i class="fas fa-database me-2 text-success"></i>
          Confirm Database Backup
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
        </div>
        <p class="mb-3">Are you sure you want to download a full backup of the database?</p>
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          <strong>Note:</strong> This will create a complete SQL dump of all database tables and data. The process may take a few minutes depending on database size.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>
          Cancel
        </button>
        <a href="{{ route('backup.download') }}" class="btn btn-success">
          <i class="fas fa-download me-2"></i>
          Yes, Download Backup
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal for Backup Deletion -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">
          <i class="fas fa-trash me-2 text-danger"></i>
          Confirm Backup Deletion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
        </div>
        <p class="mb-3">Are you sure you want to delete the backup "<span id="delete-backup-name"></span>"?</p>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Warning:</strong> This action cannot be undone. The backup data will be permanently removed.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>
          Cancel
        </button>
        <form id="delete-form" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-2"></i>
            Yes, Delete Backup
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="backupDetailsModal" tabindex="-1" aria-labelledby="backupDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="backupDetailsModalLabel">Backup Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12 col-lg-5">
            <h6 class="mb-3">Backup Summary</h6>
            <table class="table table-sm mb-0">
              <tr>
                <th>Job Name:</th>
                <td id="modal-name">-</td>
              </tr>
              <tr>
                <th>Status:</th>
                <td id="modal-status">-</td>
              </tr>
              <tr>
                <th>Storage:</th>
                <td id="modal-storage">-</td>
              </tr>
              <tr>
                <th>Size:</th>
                <td id="modal-size">-</td>
              </tr>
              <tr>
                <th>Initiated By:</th>
                <td id="modal-initiated">-</td>
              </tr>
              <tr>
                <th>Started:</th>
                <td id="modal-start">-</td>
              </tr>
              <tr>
                <th>Completed:</th>
                <td id="modal-complete">-</td>
              </tr>
              <tr>
                <th>Duration:</th>
                <td id="modal-duration">-</td>
              </tr>
            </table>
          </div>
          <div class="col-12 col-lg-7">
            <h6 class="mb-3">Notes</h6>
            <div id="modal-notes">No additional notes.</div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  (function(){
    const $ = (selector, ctx = document) => ctx.querySelector(selector);
    const $$ = (selector, ctx = document) => Array.from(ctx.querySelectorAll(selector));

    const search = $('#rb-search');
    const status = $('#rb-status');
    const items = $$('.backup-item');

    const applyFilter = () => {
      const q = (search?.value || '').toLowerCase().trim();
      const st = (status?.value || '').toLowerCase();
      items.forEach(el => {
        const txt = (el.getAttribute('data-text') || '').toLowerCase();
        const s = (el.getAttribute('data-status') || '').toLowerCase();
        const matchQ = !q || txt.includes(q);
        const matchS = !st || s === st;
        el.style.display = matchQ && matchS ? '' : 'none';
      });
    };

    if (search) search.addEventListener('input', applyFilter);
    if (status) status.addEventListener('change', applyFilter);

    const tabButtons = $$('.tab-nav .btn');
    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const tab = btn.getAttribute('data-tab');
        $('#tab-timeline')?.classList.toggle('d-none', tab !== 'timeline');
        $('#tab-table')?.classList.toggle('d-none', tab !== 'table');
      });
    });

    const deleteModalElement = $('#confirmDeleteModal');
    const deleteForm = $('#delete-form');
    const deleteName = $('#delete-backup-name');

    if (deleteModalElement) {
      deleteModalElement.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        deleteName.textContent = name;
        if (deleteForm) {
          // Assuming the delete route is /backup/{id} with DELETE method
          deleteForm.action = `/backup/${id}`;
        }
      });
    }

    applyFilter();
  })();
</script>
