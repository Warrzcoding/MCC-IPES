@if(session('message') && session('message_type') != 'success')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Validation Error!',
            html: `<ul class="text-start">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>`,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
    });
    </script>
@endif

@if(session('message') && session('message_type') == 'error')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Duplicate Entry!',
            text: '{{ session('message') }}',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33',
            showClass: {
                popup: 'animate__animated animate__shakeX'
            }
        });
    });
    </script>
@endif

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Academic Year Management
                </h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAcademicYearModal">
                    <i class="fas fa-plus"></i> Add Academic Year
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Academic Year:</strong> This section allows administrators to manage academic years and evaluation periods.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($years as $year)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $year->year }}</span>
                                        @if($year->is_active)
                                            <span class="badge bg-success ms-2">Active</span>
                                        @elseif($year->used)
                                            <span class="badge bg-secondary ms-2">Used</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $year->semester ?? 1 }}</span>
                                    </td>
                                    <td>
                                        @if($year->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @elseif($year->used)
                                            <span class="badge bg-secondary">Used</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editAcademicYearModal{{ $year->id }}" @if($year->used) disabled title="This academic year is already used and cannot be edited." @endif>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger me-1" onclick="confirmDeleteAcademicYear('{{ $year->year }}', {{ $year->id }})" @if($year->is_active) disabled @endif>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($year->used)
                                            <button class="btn btn-sm btn-warning text-white" disabled title="This academic year has already been used and cannot be reactivated.">
                                                <i class="fas fa-ban"></i> Used
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('academic-years.toggle', $year->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" @if($year->is_active) disabled @endif>
                                                    <i class="fas fa-check-circle"></i> Set Active
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ url('/academic-year/' . $year->id . '/manage') }}" class="btn btn-sm btn-outline-info ms-1">
                                            <i class="fas fa-cogs"></i> Manage
                                        </a>
                                    </td>
                                </tr>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editAcademicYearModal{{ $year->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Academic Year</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('academic-years.update', $year->id) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="editYear{{ $year->id }}" class="form-label">Year</label>
                                                        <input type="text" class="form-control" id="editYear{{ $year->id }}" name="year" value="{{ $year->year }}" required @if($year->used) disabled @endif>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editSemester{{ $year->id }}" class="form-label">Semester</label>
                                                        <select class="form-control" id="editSemester{{ $year->id }}" name="semester" required @if($year->used) disabled @endif>
                                                            <option value="1" {{ ($year->semester ?? 1) == 1 ? 'selected' : '' }}>1</option>
                                                            <option value="2" {{ ($year->semester ?? 1) == 2 ? 'selected' : '' }}>2</option>
                                                        </select>
                                                    </div>
                                                    @if($year->used)
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            This academic year is already used and cannot be edited.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary" @if($year->used) disabled @endif><i class="fas fa-save me-1"></i>Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No academic years found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteAcademicYearForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" id="deleteYearId" name="year_id">
</form>

<!-- Add Modal -->
<div class="modal fade" id="addAcademicYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('academic-years.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="text" class="form-control" id="year" name="year" required
                               placeholder="e.g. 2024-2025"
                               maxlength="9"
                               pattern="^\d{4}-\d{4}$"
                               autocomplete="off">
                        <small class="form-text text-muted">Format: 0000-0000</small>
                    </div>
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
@if(session('message') && session('message_type') == 'success')
document.addEventListener('DOMContentLoaded', function() {
    // Show success message with SweetAlert
    const message = '{{ session('message') }}';
    let successText = 'Operation completed successfully!';
    
    if (message.toLowerCase().includes('added')) {
        successText = 'Academic year successfully added!';
    } else if (message.toLowerCase().includes('updated')) {
        successText = 'Academic year successfully updated!';
    } else if (message.toLowerCase().includes('deleted')) {
        successText = 'Academic year successfully deleted!';
    } else if (message.toLowerCase().includes('active')) {
        successText = 'Academic year set as active!';
    } else {
        successText = message;
    }
    
    Swal.fire({
        title: 'Success!',
        text: successText,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    });
});
@endif
</script>

<script>
// Enhanced delete confirmation with SweetAlert
function confirmDeleteAcademicYear(year, yearId) {
    Swal.fire({
        title: 'Delete Academic Year',
        html: `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Are you sure you want to delete <strong>"${year}"</strong>?</p>
                <p class="text-muted small">This action cannot be undone and will permanently remove the academic year from the system.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete It',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        reverseButtons: true,
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Set the year ID in the hidden form
            document.getElementById('deleteYearId').value = yearId;
            
            // Submit the delete form
            const deleteForm = document.getElementById('deleteAcademicYearForm');
            if (deleteForm) {
                deleteForm.action = `/dashboard/academic-years/${yearId}/delete`;
                deleteForm.submit();
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const yearInput = document.getElementById('year');
    if (yearInput) {
        yearInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, ''); // Remove non-digits
            if (value.length > 8) value = value.slice(0, 8);
            if (value.length > 4) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            }
            this.value = value;
        });

        yearInput.addEventListener('keydown', function(e) {
            // Allow navigation keys, backspace, delete, tab, etc.
            if (
                [8, 9, 13, 27, 37, 39, 46].includes(e.keyCode) ||
                // Allow Ctrl/Cmd+A, Ctrl/Cmd+C, Ctrl/Cmd+V, Ctrl/Cmd+X
                ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode))
            ) {
                return;
            }
            // Prevent input if max length reached and not deleting
            if (this.value.length >= 9 && ![8, 46, 37, 39].includes(e.keyCode)) {
                e.preventDefault();
            }
            // Only allow numbers
            if (!e.key.match(/[0-9]/)) {
                e.preventDefault();
            }
        });
    }
});
</script>


 