<div class="table-responsive">
    @if($rejectedRequests->isEmpty())
        <div class="alert alert-warning text-center mb-0">No rejected requests at the moment.</div>
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
                <th>Status</th>
                <th>Requested At</th>
                <th>Rejected At</th>
                <th class="text-center">
                    Actions
                    <input type="checkbox" id="select-all-rejected" class="ms-2" title="Select all" />
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($rejectedRequests as $request)
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
                <td>{{ $request->student_status ?? '-' }}</td>
                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $request->updated_at ? $request->updated_at->format('Y-m-d H:i') : '-' }}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center align-items-center">
                        <input type="checkbox" class="me-2 row-checkbox-rejected" value="{{ $request->id }}" title="Select row" />
                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                onclick="deleteRejectedRequest({{ $request->id }}, '{{ $request->full_name }}')"
                                style="padding: 6px 12px; font-size: 12px;"
                                title="Delete Request">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: center; margin-top: 1rem; margin-bottom: 1rem;">
        <nav aria-label="Page navigation" style="display: inline-block;">
            <ul class="pagination mb-0">
                @if ($rejectedRequests->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $rejectedRequests->appends(request()->except('page'))->previousPageUrl() }}">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                @foreach ($rejectedRequests->getUrlRange(1, $rejectedRequests->lastPage()) as $page => $url)
                    @if ($page == $rejectedRequests->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}{{ request()->query() ? '&' . http_build_query(request()->except('page')) : '' }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                @if ($rejectedRequests->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $rejectedRequests->appends(request()->except('page'))->nextPageUrl() }}">
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

    <!-- Hidden bulk delete form -->
    <form id="bulk-delete-form" method="POST" action="{{ route('pending.requests.deleteMultiple') }}" style="display:none;">
        @csrf
        <div id="bulk-delete-ids-container"></div>
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

    /* Enhanced delete button */
    .delete-btn {
        border-radius: 6px !important;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        background: linear-gradient(135deg, #dc3545, #e74c3c);
        padding: 0.3rem 0.5rem !important;
        font-size: 0.7rem !important;
    }
    
    .delete-btn:hover {
        background: linear-gradient(135deg, #c82333, #dc2626);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .delete-btn i {
        font-size: 0.65rem !important;
        margin-right: 0.25rem !important;
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

        .delete-btn {
            padding: 0.25rem 0.4rem !important;
            font-size: 0.65rem !important;
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

        .delete-btn {
            padding: 0.2rem 0.3rem !important;
            font-size: 0.6rem !important;
        }
        
        .delete-btn .me-1 {
            margin-right: 2px !important;
        }
    }
</style>

<script>
function deleteRejectedRequest(requestId, fullName) {
    Swal.fire({
        title: 'Delete Rejected Request?',
        text: `Are you sure you want to permanently delete the rejected request for "${fullName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/dashboard/pending-requests/${requestId}/delete`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Select all functionality for rejected requests
document.addEventListener('DOMContentLoaded', function() {
    const selectAllRejected = document.getElementById('select-all-rejected');
    if (selectAllRejected) {
        selectAllRejected.addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('.row-checkbox-rejected').forEach(cb => {
                cb.checked = checked;
            });
        });
    }
});
</script> 