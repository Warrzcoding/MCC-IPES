{{-- Temporary Login Monitor Page (mirrors Pending Requests page shell) --}}
<style>
  .enhanced-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
  .tab-separator { border: none; height: 2px; background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%); margin: 0.5rem 2rem 0.75rem 2rem; border-radius: 2px; }
  .back-btn { background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; border: none !important; border-radius: 10px !important; padding: 0.75rem 1.5rem !important; font-weight: 600 !important; transition: all 0.3s ease !important; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3) !important; color: white !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; min-width: 120px !important; }
  .back-btn:hover { transform: translateY(-2px) !important; box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4) !important; color: white !important; text-decoration: none !important; }
  .search-container { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 1.25rem; }
</style>

<div class="row">
  <div class="col-12">
    <div class="enhanced-card">
      <div class="card-body p-0">
        <div class="p-4 pb-0">
          <h5 class="mb-2">
            <i class="fas fa-user-shield me-2"></i> Login Monitoring (Temporary)
          </h5>
          <p class="text-muted mb-3">This is a temporary page linked from the Monitor Login icon. Replace with real data later.</p>
        </div>

        <hr class="tab-separator">

        <div class="p-4 pt-0">
          <div class="search-container">
            <div class="d-flex align-items-center mb-2">
              <label class="form-label fw-bold text-primary mb-0">
                <i class="fas fa-search me-2"></i>
                Search Login Attempts
              </label>
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search by user, email, or IP...">
              </div>
              <div class="col-md-3">
                <select class="form-select">
                  <option value="">All Status</option>
                  <option value="success">Success</option>
                  <option value="failed">Failed</option>
                </select>
              </div>
              <div class="col-md-3 text-end">
                <a href="{{ route('dashboard', ['page' => 'login-monitor']) }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Refresh</a>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>User</th>
                  <th>Email</th>
                  <th>IP Address</th>
                  <th>Result</th>
                  <th>When</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>John Doe</td>
                  <td>john@example.com</td>
                  <td>192.168.1.10</td>
                  <td><span class="badge bg-success">Success</span></td>
                  <td>Just now</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Jane Smith</td>
                  <td>jane@example.com</td>
                  <td>192.168.1.11</td>
                  <td><span class="badge bg-danger">Failed</span></td>
                  <td>2 mins ago</td>
                </tr>
              </tbody>
            </table>
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