{{-- Enhanced Pending Requests Subpage --}}
<style>


.custom-nav-tabs {
    border: none;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
}

.custom-nav-tabs .nav-link {
    border: none;
    border-radius: 8px;
    padding: 1rem 1.5rem;
    margin: 0 0.25rem;
    font-weight: 600;
    transition: all 0.3s ease;
    color: #6c757d;
}

.custom-nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.custom-nav-tabs .nav-link:hover:not(.active) {
    background: #e9ecef;
    color: #495057;
}

.search-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.search-input-group {
    position: relative;
}

.search-input-group .form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem 0.75rem 3rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input-group .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 10;
}

.enhanced-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
}

.tab-content {
    padding: 0.5rem 2rem 2rem 2rem;
}

.tab-separator {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 50%, #e9ecef 100%);
    margin: 0.5rem 2rem 0.75rem 2rem;
    border-radius: 2px;
}

.back-btn {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 0.75rem 1.5rem !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3) !important;
    color: white !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    min-width: 120px !important;
}

.back-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4) !important;
    color: white !important;
    text-decoration: none !important;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge-pending {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #d63384;
}

.badge-rejected {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    color: #dc3545;
}
</style>

@if(session('message'))
    <div class="alert alert-{{ session('message_type', 'info') }} alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



<div class="row">
    <div class="col-12">
        <div class="enhanced-card">
            <div class="card-body p-0">
                <!-- Enhanced Nav tabs -->
                <div class="p-4 pb-0">
                    <ul class="nav custom-nav-tabs" id="requestTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                                <i class="fas fa-hourglass-half me-2"></i>
                                <span class="d-none d-sm-inline">Pending Requests</span>
                                <span class="d-sm-none">Pending</span>
                                <span class="badge bg-warning text-dark ms-2">{{ count($pendingRequests ?? []) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                <i class="fas fa-ban me-2"></i>
                                <span class="d-none d-sm-inline">Rejected Requests</span>
                                <span class="d-sm-none">Rejected</span>
                                <span class="badge bg-danger ms-2">{{ count($rejectedRequests ?? []) }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Separator -->
                <hr class="tab-separator">
                
                <!-- Enhanced Tab panes -->
                <div class="tab-content" id="requestTabContent">
                    <!-- Pending Requests Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <!-- Search Section -->
                        <div class="search-container">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-primary mb-2">
                                        <i class="fas fa-search me-2"></i>
                                        Search Pending Requests
                                    </label>
                                    <div class="search-input-group">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" id="pendingSearch" class="form-control" placeholder="Search by name, username, email, or school ID...">
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Tip: Use keywords to quickly find specific requests
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="status-badge badge-pending">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ count($pendingRequests ?? []) }} Pending
                                        </span>
                                        <small class="text-muted mt-2">
                                            <i class="fas fa-sync-alt me-1"></i>
                                            Auto-refresh enabled
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Container -->
                        <div class="table-container">
                            <div id="pendingRequestsTable">
                                @include('pages.partials.pending-requests-table', ['pendingRequests' => $pendingRequests])
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Requests Tab -->
                    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                        <!-- Search Section -->
                        <div class="search-container">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-danger mb-2">
                                        <i class="fas fa-search me-2"></i>
                                        Search Rejected Requests
                                    </label>
                                    <div class="search-input-group">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" id="rejectedSearch" class="form-control" placeholder="Search by name, username, email, or school ID...">
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Review rejected requests for potential reconsideration
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="status-badge badge-rejected">
                                            <i class="fas fa-times me-1"></i>
                                            {{ count($rejectedRequests ?? []) }} Rejected
                                        </span>
                                        <small class="text-muted mt-2">
                                            <i class="fas fa-history me-1"></i>
                                            Historical records
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Container -->
                        <div class="table-container">
                            <div id="rejectedRequestsTable">
                                @include('pages.partials.rejected-requests-table', ['rejectedRequests' => $rejectedRequests])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Back Button -->
            <div class="text-start mt-4 pb-3">
                <a href="{{ route('dashboard', ['page' => 'add-students']) }}" class="btn back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    <span class="d-none d-sm-inline">Back to Students Management</span>
                    <span class="d-sm-none">Back</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
// Enhanced JavaScript with loading states and animations
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function showLoadingState(container, searchInput) {
    const loadingHtml = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-3">
                <i class="fas fa-search me-2 text-muted"></i>
                <span class="text-muted">Searching requests...</span>
            </div>
        </div>
    `;
    container.innerHTML = loadingHtml;
    searchInput.classList.add('is-loading');
}

function hideLoadingState(searchInput) {
    searchInput.classList.remove('is-loading');
}

function showNoResults(container, searchTerm) {
    const noResultsHtml = `
        <div class="text-center py-5">
            <i class="fas fa-search-minus text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
            <h5 class="mt-3 text-muted">No results found</h5>
            <p class="text-muted mb-0">
                No requests match your search for "<strong>${searchTerm}</strong>"
            </p>
            <small class="text-muted">
                <i class="fas fa-lightbulb me-1"></i>
                Try using different keywords or check spelling
            </small>
        </div>
    `;
    container.innerHTML = noResultsHtml;
}

document.addEventListener('DOMContentLoaded', function() {
    // Add custom CSS for loading state
    const style = document.createElement('style');
    style.textContent = `
        .is-loading {
            background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%23667eea' fill-opacity='0.4'%3e%3cpath d='m10 3 a7 7 0 0 1 7 7 a7 7 0 0 1-7 7 a7 7 0 0 1-7-7 a7 7 0 0 1 7-7'/%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 20px 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { background-position: right 1rem center; }
            to { background-position: right 1rem center; }
        }
        
        .table-container {
            min-height: 300px;
            transition: all 0.3s ease;
        }
        
        .search-input-group .form-control:focus + .search-icon {
            color: #667eea;
        }
    `;
    document.head.appendChild(style);

    // Enhanced Pending search with loading states
    const pendingSearchInput = document.getElementById('pendingSearch');
    const pendingTableDiv = document.getElementById('pendingRequestsTable');
    
    if (pendingSearchInput && pendingTableDiv) {
        pendingSearchInput.addEventListener('input', debounce(function() {
            const value = this.value.trim();
            
            if (value.length > 0) {
                showLoadingState(pendingTableDiv, this);
            }
            
            const url = new URL(window.location.href);
            url.searchParams.set('search', value);
            url.searchParams.set('page', 'pending-requests');
            
            fetch(url, { 
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                } 
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                hideLoadingState(pendingSearchInput);
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('pendingRequestsTable');
                
                if (newTable) {
                    // Check if table has content
                    const hasResults = newTable.querySelector('tbody tr:not(.no-results)');
                    if (!hasResults && value.length > 0) {
                        showNoResults(pendingTableDiv, value);
                    } else {
                        pendingTableDiv.innerHTML = newTable.innerHTML;
                    }
                } else {
                    pendingTableDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Unable to load results. Please try again.</div>';
                }
            })
            .catch(error => {
                hideLoadingState(pendingSearchInput);
                console.error('Search error:', error);
                pendingTableDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Error loading results. Please refresh the page.</div>';
            });
        }, 350));
    }

    // Enhanced Rejected search with loading states
    const rejectedSearchInput = document.getElementById('rejectedSearch');
    const rejectedTableDiv = document.getElementById('rejectedRequestsTable');
    
    if (rejectedSearchInput && rejectedTableDiv) {
        rejectedSearchInput.addEventListener('input', debounce(function() {
            const value = this.value.trim();
            
            if (value.length > 0) {
                showLoadingState(rejectedTableDiv, this);
            }
            
            const url = new URL(window.location.href);
            url.searchParams.set('search_rejected', value);
            url.searchParams.set('page', 'pending-requests');
            
            fetch(url, { 
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                } 
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                hideLoadingState(rejectedSearchInput);
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('rejectedRequestsTable');
                
                if (newTable) {
                    // Check if table has content
                    const hasResults = newTable.querySelector('tbody tr:not(.no-results)');
                    if (!hasResults && value.length > 0) {
                        showNoResults(rejectedTableDiv, value);
                    } else {
                        rejectedTableDiv.innerHTML = newTable.innerHTML;
                    }
                } else {
                    rejectedTableDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Unable to load results. Please try again.</div>';
                }
            })
            .catch(error => {
                hideLoadingState(rejectedSearchInput);
                console.error('Search error:', error);
                rejectedTableDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Error loading results. Please refresh the page.</div>';
            });
        }, 350));
    }

    // Add smooth transitions for tab switching
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            // Add a subtle animation when switching tabs
            const targetPane = document.querySelector(e.target.getAttribute('data-bs-target'));
            if (targetPane) {
                targetPane.style.opacity = '0';
                targetPane.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    targetPane.style.transition = 'all 0.3s ease';
                    targetPane.style.opacity = '1';
                    targetPane.style.transform = 'translateY(0)';
                }, 50);
            }
        });
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + F to focus search in active tab
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab) {
                const searchInput = activeTab.querySelector('input[type="text"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        }
    });
});
</script> 