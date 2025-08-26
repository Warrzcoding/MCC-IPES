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
                <th>Requested At</th>
                <th>Rejected At</th>
                <th>Actions</th>
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
                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $request->updated_at ? $request->updated_at->format('Y-m-d H:i') : '-' }}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm delete-btn" 
                            onclick="deleteRejectedRequest({{ $request->id }}, '{{ $request->full_name }}')"
                            style="padding: 6px 12px; font-size: 12px;"
                            title="Delete Request">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $rejectedRequests->appends(request()->except('page'))->links() }}
    </div>
    @endif
</div>

<style>
    /* Enhanced delete button */
    .delete-btn {
        border-radius: 6px !important;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        background: linear-gradient(135deg, #dc3545, #e74c3c);
    }
    
    .delete-btn:hover {
        background: linear-gradient(135deg, #c82333, #dc2626);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .delete-btn {
            padding: 4px 8px !important;
            font-size: 11px !important;
        }
        
        .delete-btn i {
            font-size: 10px !important;
        }
    }
    
    @media (max-width: 576px) {
        .delete-btn {
            padding: 3px 6px !important;
            font-size: 10px !important;
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
</script> 