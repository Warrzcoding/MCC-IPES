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

<div class="row page-full-width">
    <div class="col-12">  
           <!-- <div class="card-body">
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Staff Management:</strong> This section allows administrators to add and manage teaching staff members.
                  </div>           
            </div>-->  
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="row page-full-width">
    <!-- Staff List -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
               <h5 class="mb-0">
                    <!--<i class="fas fa-chalkboard-teacher me-2"></i>
                     Instructors Managements-->
                </h5>
                <div>
                    <button class="btn btn-primary btn-compact-action" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Add Instructor
                    </button>
                </div>
            </div>
         
            <div class="card-body">
                <style>
                    /* DataTables overrides for hacker/compact theme */
                    .dataTables_wrapper .dataTables_filter {
                        display: flex;
                        justify-content: flex-end;
                        margin-bottom: 1rem;
                    }
                    .dataTables_wrapper .dataTables_filter input {
                        background: #fff;
                        border: 1px solid #e2e8f0;
                        border-radius: 0.375rem;
                        padding: 0.4rem 0.75rem;
                        font-size: 0.75rem;
                        width: 250px;
                        transition: all 0.2s;
                    }
                    .dataTables_wrapper .dataTables_filter input:focus {
                        outline: none;
                        border-color: #3b82f6;
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                    }
                    .dataTables_wrapper .dataTables_length select {
                        padding: 0.3rem 2rem 0.3rem 0.75rem;
                        font-size: 0.75rem;
                        border-radius: 0.375rem;
                        border: 1px solid #e2e8f0;
                    }
                    .dataTables_wrapper .dataTables_info {
                        font-size: 0.7rem;
                        color: #64748b;
                        padding-top: 1rem;
                    }
                    .dataTables_wrapper .dataTables_paginate {
                        padding-top: 1rem;
                    }

                    .dataTables_wrapper {
                        position: relative;
                        width: 100% !important;
                    }

                    /* Ensure processing indicator is always on top and centered */
                    .dataTables_wrapper .dataTables_processing {
                        display: none !important;
                    }
                    
                    .search-loader {
                        position: absolute;
                        left: 10px;
                        top: 50%;
                        transform: translateY(-50%);
                        width: 12px;
                        height: 12px;
                        border: 1.5px solid rgba(59, 130, 246, 0.2);
                        border-top-color: #3b82f6;
                        border-radius: 50%;
                        animation: search-spin 0.6s linear infinite;
                        display: none;
                        z-index: 10;
                    }
                    @keyframes search-spin {
                        to { transform: translateY(-50%) rotate(360deg); }
                    }
                    .pagination {
                        gap: 0.25rem;
                    }
                    .page-link {
                        padding: 0.4rem 0.75rem;
                        font-size: 0.75rem;
                        border-radius: 0.375rem !important;
                        color: #475569;
                        border: 1px solid #e2e8f0;
                        transition: all 0.2s;
                    }
                    .page-item.active .page-link {
                        background-color: #3b82f6;
                        border-color: #3b82f6;
                    }
                    .page-item.disabled .page-link {
                        background-color: #f8fafc;
                        color: #94a3b8;
                    }
                    
                    /* Responsive table styles for zoom compatibility */
                    .table-responsive {
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    }
                    
                    #staffTable {
                        min-width: 100%;
                        white-space: nowrap;
                        font-size: 0.7rem;
                        border-collapse: collapse;
                    }
                    
                    #staffTable thead th {
                        background-color: #f8fafc;
                        color: #64748b;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 0.025em;
                        border: none !important;
                        border-bottom: 1px solid #e2e8f0 !important;
                        padding: 0.75rem 0.5rem;
                        position: sticky;
                        top: 0;
                        z-index: 10;
                    }

                    #staffTable tbody tr {
                        transition: all 0.2s ease;
                        cursor: pointer;
                    }

                    #staffTable tbody tr:hover td,
                    #staffTable tbody tr:focus-within td {
                        background-color: #f5f5f5 !important;
                    }

                    #staffTable tbody tr:hover td:first-child,
                    #staffTable tbody tr:focus-within td:first-child {
                        box-shadow: inset 3px 0 0 #6c757d;
                    }
                    
                    #staffTable th,
                    #staffTable td {
                        padding: 0.75rem 0.5rem;
                        vertical-align: middle;
                        border: none !important;
                        border-bottom: 1px solid #f1f5f9 !important;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    
                    /* Profile image column */
                    #staffTable th:nth-child(1),
                    #staffTable td:nth-child(1) {
                        width: 60px;
                        min-width: 60px;
                        max-width: 60px;
                        text-align: center;
                    }
                    
                    /* Staff ID column */
                    #staffTable th:nth-child(2),
                    #staffTable td:nth-child(2) {
                        width: 100px;
                        min-width: 100px;
                        max-width: 100px;
                    }
                    
                    /* Full Name column */
                    #staffTable th:nth-child(3),
                    #staffTable td:nth-child(3) {
                        width: 150px;
                        min-width: 150px;
                        max-width: 150px;
                    }
                    
                    /* Email column */
                    #staffTable th:nth-child(4),
                    #staffTable td:nth-child(4) {
                        width: 180px;
                        min-width: 180px;
                        max-width: 180px;
                    }
                    
                    /* Department column */
                    #staffTable th:nth-child(5),
                    #staffTable td:nth-child(5) {
                        width: 120px;
                        min-width: 120px;
                        max-width: 120px;
                    }
                    
                    /* Status column */
                    #staffTable th:nth-child(6),
                    #staffTable td:nth-child(6) {
                        width: 80px;
                        min-width: 80px;
                        max-width: 80px;
                    }
                    
                    /* Staff Type column */
                    #staffTable th:nth-child(7),
                    #staffTable td:nth-child(7) {
                        width: 100px;
                        min-width: 100px;
                        max-width: 100px;
                    }
                    
                    /* Created column */
                    #staffTable th:nth-child(8),
                    #staffTable td:nth-child(8) {
                        width: 90px;
                        min-width: 90px;
                        max-width: 90px;
                    }
                    
                    /* Actions column */
                    #staffTable th:nth-child(9),
                    #staffTable td:nth-child(9) {
                        width: 120px;
                        min-width: 120px;
                        max-width: 120px;
                        text-align: center;
                    }
                    
                    /* Action buttons styling */
                    #staffTable .btn-sm {
                        padding: 0.16rem 0.32rem;
                        font-size: 0.68rem;
                        margin: 0 1px;
                        white-space: nowrap;
                    }
                    
                    /* Profile image responsive */
                    #staffTable img {
                        width: 30px !important;
                        height: 30px !important;
                        border-radius: 50%;
                        object-fit: cover;
                    }
                    
                    /* Compact action button for header */
                    .btn-compact-action {
                        font-size: 0.68rem;
                        padding: 0.24rem 0.55rem;
                        border-radius: 0.35rem;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.35rem;
                    }
                    .btn-compact-action i {
                        font-size: 0.62rem;
                    }
                    
                    .refresh-btn-enhanced {
                        height: 28px !important;
                        font-weight: 600;
                        font-size: 0.65rem !important;
                        padding: 0 0.75rem !important;
                        display: flex;
                        align-items: center;
                        gap: 0.25rem;
                        border-radius: 0.375rem !important;
                    }
                    .refresh-btn-enhanced i,
                    .refresh-btn-enhanced span {
                        font-size: 0.65rem !important;
                    }
                    
                    .search-box {
                        position: relative;
                        display: flex;
                        align-items: center;
                    }
                    .search-box input {
                        font-size: 0.66rem !important;
                        padding-left: 32px !important;
                        padding-top: 0.28rem !important;
                        padding-bottom: 0.28rem !important;
                        height: 28px !important;
                        width: 220px;
                        border: 1px solid #cbd5e1 !important;
                        border-radius: 0.375rem !important;
                    }
                    .search-box i {
                        position: absolute;
                        left: 10px;
                        top: 50%;
                        transform: translateY(-50%);
                        font-size: 0.62rem !important;
                        color: #64748b;
                        z-index: 5;
                    }
                    
                    .staff-type-filter {
                        font-size: 0.64rem !important;
                        padding: 0.24rem 1.5rem 0.24rem 0.45rem !important;
                        min-height: 1.8rem !important;
                    }
                    
                    /* Modal compaction */
                    .modal-compact {
                        width: 80vw;
                        max-width: 380px;
                    }
                    .modal-compact .modal-content {
                        font-size: 0.7rem;
                    }
                    .modal-compact .modal-title {
                        font-size: 0.76rem;
                        font-weight: 600;
                    }
                    .modal-compact .form-label,
                    .modal-compact .form-text {
                        font-size: 0.64rem;
                    }
                    .modal-compact .form-control,
                    .modal-compact .form-select {
                        font-size: 0.66rem;
                        padding: 0.32rem 0.48rem;
                    }
                    .modal-compact .modal-footer .btn {
                        font-size: 0.66rem;
                        padding: 0.28rem 0.5rem;
                    }
                    .modal-compact .form-control::placeholder {
                        font-size: 0.64rem;
                    }
                    
                    /* Responsive adjustments for different zoom levels */
                    @media (max-width: 1400px) {
                        #staffTable {
                            font-size: 0.66rem;
                        }
                        
                        #staffTable th,
                        #staffTable td {
                            padding: 0.18rem 0.14rem;
                        }
                        
                        #staffTable .btn-sm {
                            padding: 0.14rem 0.26rem;
                            font-size: 0.64rem;
                        }
                        
                        #staffTable img {
                            width: 28px !important;
                            height: 28px !important;
                        }
                    }
                    
                    @media (max-width: 1200px) {
                        #staffTable {
                            font-size: 0.64rem;
                        }
                        
                        #staffTable th,
                        #staffTable td {
                            padding: 0.16rem 0.12rem;
                        }
                        
                        #staffTable .btn-sm {
                            padding: 0.12rem 0.22rem;
                            font-size: 0.6rem;
                        }
                        
                        #staffTable img {
                            width: 26px !important;
                            height: 26px !important;
                        }
                    }
                    
                    @media (max-width: 992px) {
                        #staffTable {
                            font-size: 0.6rem;
                        }
                        
                        #staffTable th,
                        #staffTable td {
                            padding: 0.14rem 0.1rem;
                        }
                        
                        #staffTable .btn-sm {
                            padding: 0.1rem 0.18rem;
                            font-size: 0.56rem;
                        }
                        
                        #staffTable img {
                            width: 24px !important;
                            height: 24px !important;
                        }
                        
                        /* Stack action buttons vertically on smaller screens */
                        #staffTable td:nth-child(9) .btn-sm {
                            display: block;
                            margin: 1px 0;
                            width: 100%;
                        }
                    }
                    
                    @media (max-width: 768px) {
                        #staffTable {
                            font-size: 0.56rem;
                        }
                        
                        #staffTable th,
                        #staffTable td {
                            padding: 0.12rem 0.08rem;
                        }
                        
                        #staffTable .btn-sm {
                            padding: 0.05rem 0.14rem;
                            font-size: 0.5rem;
                        }
                        
                        #staffTable img {
                            width: 22px !important;
                            height: 22px !important;
                        }
                    }
                    
                    /* Ensure table doesn't break layout */
                    .table-responsive {
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    }
                    
                    /* Text truncation for long content */
                    #staffTable td {
                        max-width: 0;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }
                    
                    /* Allow text wrapping only for action buttons */
                    #staffTable td:nth-child(9) {
                        white-space: normal;
                    }
                                        /* Pagination Styling */
                    .pagination-custom .page-btn {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        min-width: 32px;
                        height: 32px;
                        padding: 0.5rem;
                        font-size: 0.75rem;
                        font-weight: 600;
                        color: #475569;
                        background-color: #fff;
                        border: 1px solid #e2e8f0;
                        border-radius: 0.375rem;
                        transition: all 0.2s;
                        text-decoration: none;
                    }

                    .pagination-custom .page-btn:hover:not(.disabled):not(.active) {
                        background-color: #f8fafc;
                        border-color: #cbd5e1;
                        color: #1e293b;
                    }

                    .pagination-custom .page-btn.active {
                        background-color: #3b82f6;
                        border-color: #3b82f6;
                        color: #fff;
                    }

                    .pagination-custom .page-btn.disabled {
                        background-color: #f1f5f9;
                        border-color: #e2e8f0;
                        color: #94a3b8;
                        cursor: not-allowed;
                    }

                    /* Responsive pagination */
                    @media (max-width: 576px) {
                        .pagination-custom {
                            gap: 0.25rem;
                        }

                        .page-btn {
                            min-width: 1.75rem;
                            height: 1.75rem;
                            font-size: 0.65rem;
                            padding: 0.2rem 0.4rem;
                        }
                    }
                       /* End Responsive pagination */

                </style>
                
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h7 class="m-0 font-weight-bold text-primary">Instructors List</h7>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="search-box">
                            <i class="fas fa-search" id="searchIcon"></i>
                            <input type="text" id="customSearch" class="form-control" placeholder="Search database...">
                            <div id="searchLoader" class="search-loader"></div>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary refresh-btn-enhanced" onclick="window.staffTable.ajax.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm" id="staffTable">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Staff ID</th>
                                <th>Full Name</th>
                                <th>Email</th>                                
                                <th>Department</th>
                                <th>Status</th>
                                <th>Staff Type</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Add Staff Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-compact">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Instructors</h5>
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
                        <label for="staff_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="staff_image" name="staff_image" accept="image/*" onchange="previewImage(this)">
                        <small class="form-text text-muted">Optional. Supported formats: JPG, JPEG, PNG, GIF</small>
                    </div>
                   
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Instructor  ID</label>
                        <input type="text" class="form-control" id="staff_id" name="staff_id" value="{{ old('staff_id') }}" readonly>
                        <small class="form-text text-muted">Auto-generated from full name initials + 6 random digits (e.g., WI123456).</small>
                    </div>
 
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        <small class="form-text text-muted">Tip: Type @ to auto-complete @gmail.com</small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="full-time" {{ old('status') == 'full-time' ? 'selected' : '' }}>Full time</option>
                            <option value="part-time" {{ old('status') == 'part-time' ? 'selected' : '' }}>Part time</option>
                            <option value="jo" {{ old('status') == 'jo' ? 'selected' : '' }}>JO</option>
                            <option value="cos" {{ old('status') == 'cos' ? 'selected' : '' }}>COS</option>
                        </select>
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
                        <button type="submit" class="btn btn-primary">Add Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-compact">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Instructor</h5>
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
                        <label for="edit_staff_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="edit_staff_image" name="staff_image" accept="image/*" onchange="previewEditImage(this)">
                        <small class="form-text text-muted">Leave empty to keep current image</small>
                    </div>
                    
                    
                    
                    <div class="mb-3">
                        <label for="editFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editFullName" name="full_name" readonly="true" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStaffId" class="form-label">Instructor ID</label>
                        <input type="text" class="form-control" id="editStaffId" name="staff_id" required pattern="[A-Z]{2}[0-9]{6}" minlength="8" maxlength="8" inputmode="text" title="Enter a Staff ID in the format: two uppercase letters followed by six digits (e.g., WI123456)" readonly>
                        <small class="form-text text-muted">Format: WI123456 (2 uppercase letters, 6 digits)</small>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                        <small class="form-text text-muted">Tip: Type @ to auto-complete @gmail.com</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="full-time">Full time</option>
                            <option value="part-time">Part time</option>
                            <option value="jo">JO</option>
                            <option value="cos">COS</option>
                        </select>
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

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Server-side DataTable for Staff
    const table = $('#staffTable').DataTable({
        processing: false, // Disable default loader
        serverSide: true,
        autoWidth: false,
        dom: 'lrtip', // Hide default search box
        ajax: {
            url: "{{ route('staff.data') }}",
            error: function(xhr, error, code) {
                console.error('DataTables Ajax Error:', error);
                console.log('XHR Response:', xhr.responseText);
                // Only show alert if it's not a cancelled request
                if (xhr.status !== 0) {
                    Swal.fire({
                        title: 'Data Load Error',
                        text: 'Failed to retrieve staff data. Please check console for details.',
                        icon: 'error'
                    });
                }
            }
        },
        columns: [
            { 
                data: 'image_url',
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    return `<img src="${data}" alt="Staff Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background-color: #f8f9fa;" onerror="this.src='{{ asset('images/ins.png') }}'; this.onerror=null;">`;
                }
            },
            { data: 'staff_id' },
            { data: 'full_name' },
            { data: 'email' },
            { data: 'department' },
            { 
                data: 'status_display',
                render: function(data) {
                    return data;
                }
            }, 
            { data: 'staff_type_display' },
            { data: 'created_at_formatted' },
            { 
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    const fullNameEscaped = data.full_name.replace(/'/g, "\\'");
                    const emailEscaped = data.email.replace(/'/g, "\\'");
                    const statusEscaped = data.status.replace(/'/g, "\\'");
                    const deptEscaped = data.department.replace(/'/g, "\\'");
                    
                    return `
                        <button class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal"
                                onclick="loadStaffData('${data.staff_id}', '${fullNameEscaped}', '${emailEscaped}', '${statusEscaped}', '${deptEscaped}', '${data.staff_type}', '${data.image_url}')">
                            <i class="fas fa-edit"></i>
                        </button>                     
                        <button class="btn btn-sm btn-outline-info" 
                                onclick="viewStaffData('${data.staff_id}', '${fullNameEscaped}', '${emailEscaped}', '${statusEscaped}', '${deptEscaped}', '${data.staff_type_display}', '${data.image_url}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteStaff('${data.staff_id}', '${fullNameEscaped}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search staff...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                next: '<i class="fas fa-chevron-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>'
            }
        },
        order: [[2, 'asc']], // Default sort by Full Name
        pageLength: 10,
        drawCallback: function() {
            // Re-apply any custom styling after draw
        }
    });

    // Make table globally accessible
    window.staffTable = table;

    // Custom search with debounce for "faster" feel and less server load
    let searchTimer;
    $('#customSearch').on('keyup change', function() {
        const searchValue = this.value;
        
        // Only show loader if we're actually searching
        if (searchValue.trim() !== "") {
            $('#searchIcon').hide();
            $('#searchLoader').show();
        } else {
            $('#searchLoader').hide();
            $('#searchIcon').show();
        }
        
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            table.search(searchValue).draw();
        }, 300); // 300ms debounce
    });

    // Hide loader after draw
    table.on('draw', function() {
        $('#searchLoader').hide();
        $('#searchIcon').show();
    });
});

// Email auto-complete: when user types '@', append '@gmail.com' if no domain yet
function setupEmailAutocomplete(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function(e) {
        const val = input.value;
        const atIndex = val.indexOf('@');
        if (atIndex !== -1) {
            const local = val.slice(0, atIndex);
            const domain = val.slice(atIndex + 1);
            // If just '@' typed or domain empty, set gmail.com
            if (domain === '') {
                input.value = local + '@gmail.com';
                // Move caret to end
                input.setSelectionRange(input.value.length, input.value.length);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setupEmailAutocomplete('email');
    setupEmailAutocomplete('editEmail');
});

// Department options based on staff type
const departmentOptions = {
    teaching: [
        { value: 'BSIT', text: 'BSIT' },
        { value: 'BSBA', text: 'BSBA' },
        { value: 'BSHM', text: 'BSHM' },
        { value: 'EDUC', text: 'EDUC' },
        { value: 'GEC', text: 'GEC' }
    ],
    'non-teaching': [
        { value: 'HR', text: 'HR - Human Resources' },
       // { value: 'IT', text: 'IT - Information Technology' },
       // { value: 'Finance', text: 'Finance' },
        { value: 'Watchman', text: 'Watchman' },
        { value: 'Office Staff', text: 'Office Staff' },
        { value: 'Utility', text: 'Utility' },
        { value: 'Purchaser/Payroll', text: 'Purchaser/Payroll' },
        { value: 'Electrician', text: 'Electrician' },
        { value: 'Clinic Staff', text: 'Clinic Staff' },
        { value: 'Security', text: 'Security' },
        { value: 'Library Staff', text: 'Library Staff' },
        { value: 'Registrar Staff', text: 'Registrar Staff' },
        { value: 'Admin Staff', text: 'Admin Staff' },
        { value: 'VP Office Staff', text: 'VP Office Staff' },
        { value: 'BSIT Staff', text: 'BSIT Staff' },
        { value: 'SAS Staff', text: 'SAS Staff' },
        { value: 'IT Endocer', text: 'IT Endocer' },
        { value: 'Encoder', text: 'Encoder' }
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
        title: 'Delete Instructor',
        html: `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Are you sure you want to delete <strong>"${name}"</strong>?</p>
                <p class="text-muted small">This action cannot be undone and will permanently remove the instructor from the system.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        width: '500px',
        customClass: {
            confirmButton: 'btn btn-danger btn-sm',
            cancelButton: 'btn btn-secondary btn-sm'
        }
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

function viewStaffData(staffId, fullName, email, status, department, staffType, imagePath) {
    // Use SweetAlert for viewing staff details
    const imageUrl = imagePath && imagePath.trim() !== '' ? imagePath : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg==';

    // Normalize and display status: JO/COS in uppercase, others capitalized
    const statusNorm = (status || '').toString().trim().toLowerCase();
    const statusDisplay = ['jo','cos'].includes(statusNorm) ? statusNorm.toUpperCase() : (statusNorm ? statusNorm.charAt(0).toUpperCase() + statusNorm.slice(1) : '');
    
    Swal.fire({
        html: `
            <div class="text-center mb-3">
                <img src="${imageUrl}" alt="Staff Avatar" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #dee2e6; background-color: #f8f9fa;"
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg=='; this.onerror=null;">
                <h5 class="mt-2 mb-3">${fullName}</h5>
            </div>
            <div class="text-start">
                <p><strong>Staff ID:</strong> ${staffId}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Status:</strong> ${statusDisplay}</p>
                <p><strong>Department:</strong> ${department}</p>
                <p><strong>Staff Type:</strong> ${staffType}</p>
            </div>
        `,
        confirmButtonText: 'Close',
        confirmButtonColor: '#3085d6',
        width: '500px'
    });
}

function loadStaffData(id, fullName, email, status, department, staffType, imagePath) {
    document.getElementById('editStaffId').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editEmail').value = email;
    document.getElementById('editStatus').value = status;
    document.getElementById('editStaffType').value = staffType;
    
    // Update department options based on staff type
    updateDepartmentOptions('editStaffType', 'editDepartment');
    
    // Set department value after options are updated
    setTimeout(function() {
        const mappedDept = (department === 'BSED' || department === 'BEED') ? 'EDUC' : department;
        document.getElementById('editDepartment').value = mappedDept;
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

    // Auto-generate Staff ID from Full Name (first + middle initials + 6 random digits)
    const fullNameInput = document.getElementById('full_name');
    const staffIdInput = document.getElementById('staff_id');
    function generateStaffIdFromName(name) {
        if (!name) return '';
        const parts = name.trim().split(/\s+/).filter(Boolean);
        let initials = '';
        if (parts.length >= 2) {
            initials = (parts[0][0] + parts[1][0]).toUpperCase();
        } else if (parts.length === 1) {
            initials = (parts[0].slice(0, 2)).toUpperCase();
            if (initials.length === 1) initials += 'X';
        } else {
            initials = 'XX';
        }
        const rand = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
        return initials + rand;
    }
    if (fullNameInput && staffIdInput) {
        fullNameInput.addEventListener('input', function() {
            staffIdInput.value = generateStaffIdFromName(fullNameInput.value);
        });
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
                title: 'Updating...',
                html: `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>Please wait while we update the instructor information.</p>
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
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.message_type === 'success' || data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message || 'Instructor updated successfully!',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'success-alert-popup'
                        },
                        didOpen: function() {
                            const popup = Swal.getPopup();
                            if (popup) {
                                popup.style.minWidth = window.innerWidth <= 768 ? '280px' : '350px';
                                popup.style.minHeight = window.innerWidth <= 768 ? '200px' : '220px';
                            }
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        },
                        didClose: () => {
                            const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                            if (editModal) editModal.hide();
                            if (window.staffTable) window.staffTable.ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to update instructor.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update instructor. Please try again.',
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
                        <p>Please wait while we remove the instructor from the system.</p>
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
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.message_type === 'success' || data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message || 'Instructor has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        didClose: () => {
                            if (window.staffTable) window.staffTable.ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to delete instructor.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
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