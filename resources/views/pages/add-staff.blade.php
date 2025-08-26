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

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
              <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                     Staff Members Managements
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Staff Management:</strong> This section allows administrators to add and manage teaching staff members.
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Staff List -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Staff List</h6>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Add Staff
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($staff->isEmpty())
                    <p class="text-muted text-center py-4">No staff found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered" id="staffTable">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Staff ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Department</th>
                                    <th>Staff Type</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $staff_member)
                                    <tr>
                                        <td>
                                            @php
                                                $imageUrl = '';
                                                if ($staff_member->image_path) {
                                                    if (str_starts_with($staff_member->image_path, 'uploads/')) {
                                                        $imageUrl = asset($staff_member->image_path);
                                                    } else {
                                                        $imageUrl = asset('storage/' . $staff_member->image_path);
                                                    }
                                                } else {
                                                    $imageUrl = asset('images/default-avatar.png');
                                                }
                                            @endphp
                                            <img src="{{ $imageUrl }}" 
                                                 alt="Staff Photo" 
                                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background-color: #f8f9fa;"
                                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNlOWVjZWYiLz4KPHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIxMCIgeT0iMTAiPgo8cGF0aCBkPSJNMTIgMTJDMTRuMjEgMCAyNC0xLjI3IDI0LTZzLTkuNzktNi0yNC02LTI0IDEuMjctMjQgNiA5Ljc5IDYgMjQgNnoiIGZpbGw9IiM2Yzc1N2QiLz4KPHBhdGggZD0iTTEyIDEyYzYuNjI3IDAgMTItNS4zNzMgMTItMTJzLTUuMzczLTEyLTEyLTEyLTEyIDUuMzczLTEyIDEyIDUuMzczIDEyIDEyIDEyeiIgZmlsbD0iIzZjNzU3ZCIvPgo8L3N2Zz4KPC9zdmc+'">
                                        </td>
                                        <td>{{ $staff_member->staff_id }}</td>
                                        <td>{{ $staff_member->full_name }}</td>
                                        <td>{{ $staff_member->email }}</td>
                                        <td>{{ $staff_member->phone }}</td>
                                        <td>{{ $staff_member->department }}</td>
                                        <td>{{ ucfirst($staff_member->staff_type) }}</td>
                                        <td>{{ $staff_member->created_at ? $staff_member->created_at->format('Y-m-d') : '' }}</td>
                                        <td>
                                            @php
                                                $editImageUrl = '';
                                                if ($staff_member->image_path) {
                                                    if (str_starts_with($staff_member->image_path, 'uploads/')) {
                                                        $editImageUrl = asset($staff_member->image_path);
                                                    } else {
                                                        $editImageUrl = asset('storage/' . $staff_member->image_path);
                                                    }
                                                }
                                            @endphp
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    onclick="loadStaffData('{{ $staff_member->staff_id }}', '{{ addslashes($staff_member->full_name) }}', '{{ addslashes($staff_member->email) }}', '{{ addslashes($staff_member->phone) }}', '{{ addslashes($staff_member->department) }}', '{{ $staff_member->staff_type }}', '{{ $editImageUrl }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteStaff('{{ $staff_member->staff_id }}', '{{ addslashes($staff_member->full_name) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="viewStaffData('{{ $staff_member->staff_id }}', '{{ addslashes($staff_member->full_name) }}', '{{ addslashes($staff_member->email) }}', '{{ addslashes($staff_member->phone) }}', '{{ addslashes($staff_member->department) }}', '{{ ucfirst($staff_member->staff_type) }}', '{{ $imageUrl }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



<!-- Add Staff Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="{{ url('/dashboard/add-staff') }}">
                    @csrf
                    <div class="mb-3 text-center">
                        <div class="mb-2">
                            <img id="imagePreview" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg==" alt="Preview" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; background-color: #f8f9fa;">
                        </div>
                        <label for="staff_image" class="form-label">Staff Photo</label>
                        <input type="file" class="form-control" id="staff_image" name="staff_image" accept="image/*" onchange="previewImage(this)">
                        <small class="form-text text-muted">Optional. Supported formats: JPG, JPEG, PNG, GIF</small>
                    </div>

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff ID</label>
                        <input type="text" class="form-control" id="staff_id" name="staff_id" value="{{ old('staff_id') }}" required pattern="[A-Z]{2}[0-9]{4}" minlength="6" maxlength="6" inputmode="text" title="Enter a Staff ID in the format: two uppercase letters followed by four digits (e.g., WI3453)">
                        <small class="form-text text-muted">Format: WI3453 (2 uppercase letters, 4 digits)</small>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required pattern="09[0-9]{9}" minlength="11" maxlength="11" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" title="Enter an 11-digit number starting with 09 (e.g., 09000000000)">
                        <small class="form-text text-muted">Format: 09000000000 (11 digits, numbers only)</small>
                    </div>

                    <div class="mb-3">
                        <label for="staff_type" class="form-label">Staff Type</label>
                        <select class="form-select" id="staff_type" name="staff_type" required onchange="updateDepartmentOptions('staff_type', 'department')">
                            <option value="">Select Type</option>
                            <option value="teaching" {{ old('staff_type') == 'teaching' ? 'selected' : '' }}>Teaching (Instructor)</option>
                            <option value="non-teaching" {{ old('staff_type') == 'non-teaching' ? 'selected' : '' }}>Non-Teaching Staff</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department" required>
                            <option value="">Select Department</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="{{ url('/dashboard/update-staff') }}">
                    @csrf
                    <input type="hidden" name="original_staff_id" id="originalStaffId">
                    <div class="mb-3 text-center">
                        <div class="mb-2">
                            <img id="editImagePreview" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg==" alt="Preview" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; background-color: #f8f9fa;">
                        </div>
                        <label for="edit_staff_image" class="form-label">Staff Photo</label>
                        <input type="file" class="form-control" id="edit_staff_image" name="staff_image" accept="image/*" onchange="previewEditImage(this)">
                        <small class="form-text text-muted">Leave empty to keep current image</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editStaffId" class="form-label">Staff ID</label>
                        <input type="text" class="form-control" id="editStaffId" name="staff_id" required pattern="[A-Z]{2}[0-9]{4}" minlength="6" maxlength="6" inputmode="text" title="Enter a Staff ID in the format: two uppercase letters followed by four digits (e.g., WI3453)">
                        <small class="form-text text-muted">Format: WI3453 (2 uppercase letters, 4 digits)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editFullName" name="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editStaffType" class="form-label">Staff Type</label>
                        <select class="form-select" id="editStaffType" name="staff_type" required onchange="updateDepartmentOptions('editStaffType', 'editDepartment')">
                            <option value="">Select Type</option>
                            <option value="teaching">Teaching (Instructor)</option>
                            <option value="non-teaching">Non-Teaching Staff</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDepartment" class="form-label">Department</label>
                        <select class="form-select" id="editDepartment" name="department" required>
                            <option value="">Select Department</option>
                        </select>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="staffName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ url('/dashboard/delete-staff') }}" id="deleteStaffForm" style="display: inline;">
                    @csrf
                    <input type="hidden" name="staff_id" id="deleteStaffId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Department options based on staff type
const departmentOptions = {
    teaching: [
        { value: 'BSIT', text: 'BSIT - Bachelor of Science in Information Technology' },
        { value: 'BSHM', text: 'BSHM - Bachelor of Science in Hospitality Management' },
        { value: 'BSBA', text: 'BSBA - Bachelor of Science in Business Administration' },
        { value: 'BSED', text: 'BSED - Bachelor of Science in Education' },
        { value: 'BEED', text: 'BEED - Bachelor of Elementary Education' }
    ],
    'non-teaching': [
        { value: 'HR', text: 'HR - Human Resources' },
       // { value: 'IT', text: 'IT - Information Technology' },
       // { value: 'Finance', text: 'Finance' },
        { value: 'Marketing', text: 'Marketing' },
        { value: 'Academic Affairs', text: 'Academic Affairs' },
        { value: 'Student Services', text: 'Student Services' },
        { value: 'Registrar', text: 'Registrar' },
        { value: 'Library', text: 'Library' },
        { value: 'Maintenance', text: 'Maintenance/Utility' },
        { value: 'Security', text: 'Security' },
        { value: 'Accounting', text: 'Accounting' },
        { value: 'Admissions', text: 'Admissions' },
        { value: 'Canteen', text: 'Canteen' },
        { value: 'Clinic', text: 'Clinic' }
    ]
};

function updateDepartmentOptions(staffTypeId, departmentId) {
    const staffTypeSelect = document.getElementById(staffTypeId);
    const departmentSelect = document.getElementById(departmentId);
    const selectedType = staffTypeSelect.value;
    
    // Clear existing options
    departmentSelect.innerHTML = '<option value="">Select Department</option>';
    
    if (selectedType && departmentOptions[selectedType]) {
        departmentOptions[selectedType].forEach(function(option) {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.text;
            departmentSelect.appendChild(optionElement);
        });
    }
}

function deleteStaff(id, name) {
    Swal.fire({
        title: 'Delete Staff',
        html: `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Are you sure you want to delete <strong>"${name}"</strong>?</p>
                <p class="text-muted small">This action cannot be undone and will permanently remove the staff from the system.</p>
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
            document.getElementById('deleteStaffId').value = id;
            document.getElementById('staffName').textContent = name;
            
            // Submit the delete form
            const deleteForm = document.getElementById('deleteStaffForm');
            if (deleteForm) {
                deleteForm.submit();
            }
        }
    });
}

function viewStaffData(staffId, fullName, email, phone, department, staffType, imagePath) {
    // Use SweetAlert for viewing staff details
    const imageUrl = imagePath && imagePath.trim() !== '' ? imagePath : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg==';
    
    Swal.fire({
        html: `
            <div class="text-center mb-3">
                <img src="${imageUrl}" alt="Staff Avatar" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #dee2e6; background-color: #f8f9fa;"
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg=='">
                <h5 class="mt-2 mb-3">${fullName}</h5>
            </div>
            <div class="text-start">
                <p><strong>Staff ID:</strong> ${staffId}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Phone:</strong> ${phone}</p>
                <p><strong>Department:</strong> ${department}</p>
                <p><strong>Staff Type:</strong> ${staffType}</p>
            </div>
        `,
        confirmButtonText: 'Close',
        confirmButtonColor: '#3085d6',
        width: '500px'
    });
}

function loadStaffData(id, fullName, email, phone, department, staffType, imagePath) {
    document.getElementById('editStaffId').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone;
    document.getElementById('editStaffType').value = staffType;
    
    // Update department options based on staff type
    updateDepartmentOptions('editStaffType', 'editDepartment');
    
    // Set department value after options are updated
    setTimeout(function() {
        document.getElementById('editDepartment').value = department;
    }, 100);
    
    // Set image preview - fix for image display
    const imagePreview = document.getElementById('editImagePreview');
    if (imagePath && imagePath.trim() !== '') {
        imagePreview.src = imagePath;
    } else {
        imagePreview.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg==';
    }
    document.getElementById('originalStaffId').value = id;
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editImagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Initialize DataTable if available
// and department options for add modal
// and search

document.addEventListener('DOMContentLoaded', function() {
    updateDepartmentOptions('staff_type', 'department');
    // --- UI: Flex container for search and filter ---
    const searchFilterWrapper = document.createElement('div');
    searchFilterWrapper.className = 'd-flex flex-wrap align-items-center gap-3 mb-3';
    // --- Search box with icon ---
    const searchBox = document.createElement('div');
    searchBox.className = 'search-box mb-0';
    searchBox.style.flex = '1 1 250px';
    searchBox.style.minWidth = '220px';
    searchBox.style.position = 'relative';
    const searchIcon = document.createElement('i');
    searchIcon.className = 'fas fa-search search-icon';
    searchIcon.style.position = 'absolute';
    searchIcon.style.left = '15px';
    searchIcon.style.top = '50%';
    searchIcon.style.transform = 'translateY(-50%)';
    searchIcon.style.color = '#6c757d';
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.id = 'searchInput';
    searchInput.className = 'form-control';
    searchInput.placeholder = 'Search staff by name, department, or email...';
    searchInput.style.paddingLeft = '45px';
    searchInput.style.borderRadius = '25px';
    searchInput.style.border = '2px solid #e9ecef';
    searchInput.style.boxShadow = 'none';
    searchInput.addEventListener('focus', function() {
        searchInput.style.borderColor = '#007bff';
        searchInput.style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,.25)';
    });
    searchInput.addEventListener('blur', function() {
        searchInput.style.borderColor = '#e9ecef';
        searchInput.style.boxShadow = 'none';
    });
    searchBox.appendChild(searchIcon);
    searchBox.appendChild(searchInput);
    // --- Staff type filter select ---
    const filterDiv = document.createElement('div');
    filterDiv.style.minWidth = '200px';
    const staffTypeSelect = document.createElement('select');
    staffTypeSelect.id = 'staffTypeFilter';
    staffTypeSelect.className = 'form-select';
    staffTypeSelect.innerHTML = `
      <option value="">All Staff Types</option>
      <option value="Teaching">Teaching</option>
      <option value="Non-teaching">Non-Teaching</option>
    `;
    filterDiv.appendChild(staffTypeSelect);
    // --- Assemble UI ---
    searchFilterWrapper.appendChild(searchBox);
    searchFilterWrapper.appendChild(filterDiv);
    // --- Refresh button ---
    const refreshButton = document.createElement('button');
    refreshButton.type = 'button';
    refreshButton.className = 'btn btn-primary ms-2 shadow-sm d-flex align-items-center gap-2 rounded-pill refresh-btn-enhanced';
    refreshButton.style.height = '40px';
    refreshButton.style.fontWeight = 'bold';
    refreshButton.style.fontSize = '1rem';
    refreshButton.innerHTML = '<i class="fas fa-sync-alt fa-spin-on-hover"></i> <span>Refresh</span>';
    refreshButton.onclick = function() {
        location.reload();
    };
    searchFilterWrapper.appendChild(refreshButton);
    // --- Insert UI ---
    const table = document.getElementById('staffTable');
    if (table) {
        table.parentNode.insertBefore(searchFilterWrapper, table);
        // --- Filtering logic ---
        function filterRows() {
            const input = searchInput.value.toLowerCase();
            const type = staffTypeSelect.value;
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                // If this is the empty state row, always show if no results
                if (row.querySelector('td[colspan]')) {
                    row.style.display = 'none';
                    return;
                }
                let found = false;
                // Search in all columns except actions
                for (let j = 1; j < row.cells.length - 1; j++) {
                    if (row.cells[j] && row.cells[j].textContent.toLowerCase().indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                // Staff type is in the 7th cell (index 6)
                let staffType = row.cells[6] ? row.cells[6].textContent.trim() : '';
                let matchesType = !type || staffType.includes(type);
                row.style.display = (found && matchesType) ? '' : 'none';
            });
            // Show empty state if all rows are hidden
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
            const emptyRow = table.querySelector('tbody tr td[colspan]')?.parentElement;
            if (emptyRow) {
                emptyRow.style.display = visibleRows.length === 0 ? '' : 'none';
            }
        }
        searchInput.addEventListener('keyup', filterRows);
        staffTypeSelect.addEventListener('change', filterRows);
    }
});

// Form submission handling with SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit form submission
    const editStaffForm = document.querySelector('form[action*="update-staff"]');
    if (editStaffForm) {
        editStaffForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Updating Staff...',
                html: `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>Please wait while we update the staff information.</p>
                    </div>
                `,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(editStaffForm.action, {
                method: 'POST',
                body: new FormData(editStaffForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Staff updated successfully!',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    didClose: () => {
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update staff. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
    
    // Handle delete form submission
    const deleteStaffForm = document.getElementById('deleteStaffForm');
    if (deleteStaffForm) {
        deleteStaffForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Deleting Staff...',
                html: `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>Please wait while we remove the staff from the system.</p>
                    </div>
                `,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(deleteStaffForm.action, {
                method: 'POST',
                body: new FormData(deleteStaffForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Staff Deleted!',
                    text: 'The staff has been successfully removed from the system.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    didClose: () => {
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Delete Failed!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>Failed to delete the staff. Please try again.</p>
                            <p class="text-muted small">If the problem persists, contact the administrator.</p>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33',
                    width: '500px'
                });
            });
        });
    }
});

@if(session('message') && session('message_type') == 'success')
document.addEventListener('DOMContentLoaded', function() {
    // Show success message with SweetAlert
    const message = '{{ session('message') }}';
    let successText = 'Operation completed successfully!';
    
    if (message.toLowerCase().includes('added')) {
        successText = 'Staff successfully added!';
    } else if (message.toLowerCase().includes('updated')) {
        successText = 'Staff successfully updated!';
    } else if (message.toLowerCase().includes('deleted')) {
        successText = 'Staff successfully deleted!';
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