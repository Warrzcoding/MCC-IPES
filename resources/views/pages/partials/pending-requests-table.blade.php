<div class="table-responsive">
    @if($pendingRequests->isEmpty())
        <div class="alert alert-info text-center mb-0">No pending requests at the moment.</div>
    @else
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Profile</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>MS Email Account</th>
                <th>School ID</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Requested At</th>
                <th class="text-center">
                    Actions
                    <input type="checkbox" id="select-all" class="ms-2" title="Select all" />
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingRequests as $request)
            <tr>
                <td class="text-center">
                    @php
                        $imageUrl = $request->profile_image
                            ? asset('uploads/students/' . $request->profile_image)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($request->full_name) . '&background=667eea&color=fff&size=50';
                    @endphp
                    <img src="{{ $imageUrl }}" alt="Profile" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; background-color: #f8f9fa;">
                </td>
                <td>{{ $request->full_name }}</td>
                <td>{{ $request->username }}</td>
                <td>{{ $request->email }}</td>
                <td>{{ $request->school_id }}</td>
                <td>{{ $request->course }}</td>
                <td>{{ $request->year_level }}</td>
                <td>{{ $request->section ?? '-' }}</td>
                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center align-items-center">
                        <input type="checkbox" class="me-2 row-checkbox" value="{{ $request->id }}" title="Select row" />
                        <form method="POST" action="{{ route('pending.requests.approve', $request->id) }}" class="approve-form">
                            @csrf
                            <button type="button" class="btn btn-success btn-sm approve-btn" 
                                    style="padding: 6px 12px; font-size: 12px;" 
                                    title="Approve Request">
                                <i class="fas fa-check me-1"></i>Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('pending.requests.reject', $request->id) }}" class="reject-form">
                            @csrf
                            <button type="button" class="btn btn-danger btn-sm reject-btn" 
                                    style="padding: 6px 12px; font-size: 12px;" 
                                    title="Reject Request">
                                <i class="fas fa-times me-1"></i>Reject
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: center; margin-top: 1rem; margin-bottom: 1rem;">
        <nav aria-label="Page navigation" style="display: inline-block;">
            <ul class="pagination mb-0">
                @if ($pendingRequests->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $pendingRequests->appends(request()->except('page'))->previousPageUrl() }}">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                @foreach ($pendingRequests->getUrlRange(1, $pendingRequests->lastPage()) as $page => $url)
                    @if ($page == $pendingRequests->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}{{ request()->query() ? '&' . http_build_query(request()->except('page')) : '' }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                @if ($pendingRequests->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $pendingRequests->appends(request()->except('page'))->nextPageUrl() }}">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    <!-- Hidden bulk approve form -->
    <form id="bulk-approve-form" method="POST" action="{{ route('pending.requests.approveMultiple') }}" style="display:none;">
        @csrf
        <div id="bulk-ids-container"></div>
    </form>
    @endif
</div>

<style>
    .table-responsive {
        font-size: 0.8rem;
    }

    .table-responsive table {
        font-size: 0.8rem;
    }

    .table-responsive table thead th {
        font-size: 0.75rem;
        padding: 0.45rem 0.5rem;
        white-space: nowrap;
    }

    .table-responsive table tbody td {
        font-size: 0.8rem;
        padding: 0.45rem 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table-responsive table img {
        width: 40px;
        height: 40px;
    }

    /* Enhanced action buttons */
    .approve-btn, .reject-btn {
        border-radius: 6px !important;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        padding: 0.3rem 0.5rem !important;
        font-size: 0.7rem !important;
    }
    
    .approve-btn {
        background: linear-gradient(135deg, #28a745, #20c997);
    }
    
    .approve-btn:hover {
        background: linear-gradient(135deg, #218838, #1ea085);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .reject-btn {
        background: linear-gradient(135deg, #dc3545, #e74c3c);
    }
    
    .reject-btn:hover {
        background: linear-gradient(135deg, #c82333, #dc2626);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .approve-btn i, .reject-btn i {
        font-size: 0.65rem !important;
        margin-right: 0.25rem !important;
    }
    
    /* Action buttons container */
    .d-flex.gap-1 {
        gap: 6px !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive table thead th {
            font-size: 0.7rem;
            padding: 0.35rem 0.35rem;
        }

        .table-responsive table tbody td {
            font-size: 0.75rem;
            padding: 0.35rem 0.35rem;
        }

        .approve-btn, .reject-btn {
            padding: 0.25rem 0.4rem !important;
            font-size: 0.65rem !important;
        }
        
        .d-flex.gap-1 {
            gap: 4px !important;
        }
    }
    
    @media (max-width: 576px) {
        .table-responsive table thead th {
            font-size: 0.65rem;
            padding: 0.3rem 0.25rem;
        }

        .table-responsive table tbody td {
            font-size: 0.7rem;
            padding: 0.3rem 0.25rem;
        }

        .table-responsive table img {
            width: 35px;
            height: 35px;
        }

        .approve-btn, .reject-btn {
            padding: 0.2rem 0.3rem !important;
            font-size: 0.6rem !important;
        }
        
        .approve-btn .me-1, .reject-btn .me-1 {
            margin-right: 2px !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Approve button
    document.querySelectorAll('.approve-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Approve Request?',
                text: 'Are you sure you want to approve this student sign-up request?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    // Reject button
    document.querySelectorAll('.reject-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Reject Request?',
                text: 'Are you sure you want to reject this student sign-up request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Show success alert if redirected with a message
    @if(session('message'))
        Swal.fire({
            icon: '{{ session('message_type', 'success') === 'success' ? 'success' : (session('message_type') === 'warning' ? 'warning' : 'info') }}',
            title: '{{ session('message_type', 'success') === 'success' ? 'Success!' : (session('message_type') === 'warning' ? 'Rejected' : 'Info') }}',
            text: @json(session('message')),
            confirmButtonColor: '#667eea',
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false
        });
    @endif
});
</script> 