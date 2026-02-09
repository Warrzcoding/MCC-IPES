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
                    /* Responsive table styles for zoom compatibility */
                    .table-responsive {
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    }
                    
                    #staffTable {
                        min-width: 100%;
                        white-space: nowrap;
                        font-size: 0.66rem;
                    }
                    
                    #staffTable th,
                    #staffTable td {
                        padding: 0.2rem 0.14rem;
                        vertical-align: middle;
                        border: 1px solid #dee2e6;
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
                        padding: 0.24rem 0.55rem !important;
                    }
                    .refresh-btn-enhanced i,
                    .refresh-btn-enhanced span {
                        font-size: 0.65rem !important;
                    }
                    
                    .search-box {
                        font-size: 0.66rem;
                    }
                    .search-box input {
                        font-size: 0.66rem !important;
                        padding-left: 36px !important;
                        padding-top: 0.28rem !important;
                        padding-bottom: 0.28rem !important;
                    }
                    .search-box i {
                        font-size: 0.62rem !important;
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
                        border: 1px solid #dee2e6;
                        border-radius: 0.375rem;
                    }
                    
                    /* Header styling */
                    #staffTable thead th {
                        background-color: #f8f9fa;
                        font-weight: 600;
                        border-bottom: 2px solid #dee2e6;
                        position: sticky;
                        top: 0;
                        z-index: 10;
                    }
                    
                    /* Hover effects */
                    #staffTable tbody tr:hover {
                        background-color: #f8f9fa;
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
                                        /* Custom Pagination Styles */
                    .pagination-custom {
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    .page-btn {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        min-width: 2rem;
                        height: 2rem;
                        padding: 0.25rem 0.5rem;
                        margin: 0 0.125rem;
                        font-size: 0.7rem;
                        font-weight: 500;
                        text-decoration: none;
                        color: #495057;
                        background-color: #fff;
                        border: 1px solid #dee2e6;
                        border-radius: 0.25rem;
                        transition: all 0.15s ease-in-out;
                        cursor: pointer;
                    }

                    .page-btn:hover:not(.disabled):not(.active) {
                        color: #0056b3;
                        background-color: #e9ecef;
                        border-color: #adb5bd;
                        text-decoration: none;
                    }

                    .page-btn.active {
                        color: #fff;
                        background-color: #007bff;
                        border-color: #007bff;
                        font-weight: 600;
                    }

                    .page-btn.disabled {
                        color: #6c757d;
                        background-color: #e9ecef;
                        border-color: #dee2e6;
                        cursor: not-allowed;
                        opacity: 0.6;
                        pointer-events: none;
                    }

                    .page-btn i {
                        font-size: 0.65rem;
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
                
                @if($staff->isEmpty())
                    <p class="text-muted text-center py-4">No staff found.</p>
                @else

                  <!--Start of golbal serach-->
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h7  class="m-0 font-weight-bold text-primary">Instructors List</h7>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2" style="flex-wrap: wrap; align-items: center;">
                        <input type="hidden" name="page" value="add-staff">
                        <div class="position-relative" style="min-width: 200px;">
                            <i class="fas fa-search position-absolute" style="left: 0.7rem; top: 50%; transform: translateY(-50%); color: #999; font-size: 0.75rem;"></i>
                            <input type="text" name="search_staff" class="form-control" placeholder="Search by name, ID, email..." 
                                   value="{{ request('search_staff') }}" style="padding-left: 2rem; font-size: 0.75rem; padding: 0.4rem 0.6rem 0.4rem 2rem;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-compact-action">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if(request('search_staff'))
                            <a href="{{ route('dashboard', ['page' => 'add-staff']) }}" class="btn btn-secondary btn-compact-action">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>
           <!--end of golbal serach-->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="staffTable">
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
                                        <td>{{ $staff_member->department }}</td>
                                        <td>
                                            @php
                                                $statusValue = strtolower($staff_member->status ?? '');
                                                $statusDisplay = in_array($statusValue, ['jo','cos']) ? strtoupper($statusValue) : ucfirst($statusValue);
                                            @endphp
                                            {{ $statusDisplay }}
                                        </td>
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
                                                    onclick="loadStaffData('{{ $staff_member->staff_id }}', '{{ addslashes($staff_member->full_name) }}', '{{ addslashes($staff_member->email) }}', '{{ addslashes($staff_member->status) }}', '{{ addslashes($staff_member->department) }}', '{{ $staff_member->staff_type }}', '{{ $editImageUrl }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>                     
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="viewStaffData('{{ $staff_member->staff_id }}', '{{ addslashes($staff_member->full_name) }}', '{{ addslashes($staff_member->email) }}', '{{ addslashes($staff_member->status) }}', '{{ addslashes($staff_member->department) }}', '{{ ucfirst($staff_member->staff_type) }}', '{{ $imageUrl }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                              <button class="btn btn-sm btn-outline-danger" onclick="deleteStaff('{{ $staff_member->staff_id }}', '{{ addslashes($staff_member->full_name) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!--pagination page-->
                      @if(method_exists($staff, 'hasPages') && $staff->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Showing {{ $staff->firstItem() }}-{{ $staff->lastItem() }} of {{ $staff->total() }} staff members
                            </div>
                            <nav aria-label="Staff pagination">
                                <div class="pagination-custom d-flex align-items-center gap-1">
                                    {{-- Previous Button --}}
                                    @if($staff->onFirstPage())
                                        <span class="page-btn disabled">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $staff->previousPageUrl() }}" class="page-btn">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    {{-- Page Numbers --}}
                                    @php
                                        $currentPage = $staff->currentPage();
                                        $lastPage = $staff->lastPage();
                                        $showPages = 8;
                                        $halfShow = floor($showPages / 2);

                                        $startPage = max(1, $currentPage - $halfShow);
                                        $endPage = min($lastPage, $currentPage + $halfShow);

                                        if ($endPage - $startPage + 1 < $showPages) {
                                            if ($startPage == 1) {
                                                $endPage = min($lastPage, $startPage + $showPages - 1);
                                            } elseif ($endPage == $lastPage) {
                                                $startPage = max(1, $endPage - $showPages + 1);
                                            }
                                        }
                                    @endphp

                                    {{-- Show first page if not in range --}}
                                    @if($startPage > 1)
                                        <a href="{{ $staff->url(1) }}" class="page-btn">1</a>
                                        @if($startPage > 2)
                                            <span class="page-btn disabled">...</span>
                                        @endif
                                    @endif

                                    {{-- Show page range --}}
                                    @for($page = $startPage; $page <= $endPage; $page++)
                                        @if($page == $currentPage)
                                            <span class="page-btn active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $staff->url($page) }}" class="page-btn">{{ $page }}</a>
                                        @endif
                                    @endfor

                                    {{-- Show last page if not in range --}}
                                    @if($endPage < $lastPage)
                                        @if($endPage < $lastPage - 1)
                                            <span class="page-btn disabled">...</span>
                                        @endif
                                        <a href="{{ $staff->url($lastPage) }}" class="page-btn">{{ $lastPage }}</a>
                                    @endif

                                    {{-- Next Button --}}
                                    @if($staff->hasMorePages())
                                        <a href="{{ $staff->nextPageUrl() }}" class="page-btn">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="page-btn disabled">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </div>
                            </nav>
                        </div>
                    @endif
                 <!--end of pagination page-->
                @endif
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

<script>
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
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjUwIiBjeT0iNTAiIHI9IjUwIiBmaWxsPSIjZTllY2VmIi8+Cjxzdmcgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iMjUiIHk9IjI1Ij4KPHBhdGggZD0iTTEyIDEyQzE0LjIxIDAgMjQtMS4yNyAyNC02cy05Ljc5LTYtMjQtNi0yNCAxLjI3LTI0IDYgOS43OSA2IDI0IDZ6IiBmaWxsPSIjNmM3NTdkIi8+CjxwYXRoIGQ9Ik0xMiAxMmM2LjYyNyAwIDEyLTUuMzczIDEyLTEycy01LjM3My0xMi0xMi0xMi0xMiA1LjM3My0xMiAxMiA1LjM3MyAxMiAxMiAxMnoiIGZpbGw9IiM2Yzc1N2QiLz4KPC9zdmc+Cjwvc3ZnPg=='">
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
    // --- UI: Flex container for search and filter ---
  /* const searchFilterWrapper = document.createElement('div');
    searchFilterWrapper.className = 'd-flex flex-wrap align-items-center gap-2 mb-2';
    // --- Search box with icon ---
    const searchBox = document.createElement('div');
    searchBox.className = 'search-box mb-0';
    searchBox.style.flex = '1 1 220px';
    searchBox.style.minWidth = '200px';
    searchBox.style.position = 'relative';
    searchBox.style.fontSize = '0.66rem';
    const searchIcon = document.createElement('i');
    searchIcon.className = 'fas fa-search search-icon';
    searchIcon.style.fontSize = '0.62rem';
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
    searchInput.style.fontSize = '0.66rem';
    searchInput.style.paddingLeft = '34px';
    searchInput.style.paddingTop = '0.28rem';
    searchInput.style.paddingBottom = '0.28rem';
    searchInput.style.borderRadius = '20px';
    searchInput.style.border = '1.3px solid #e9ecef';
    searchInput.style.boxShadow = 'none';
    searchInput.addEventListener('focus', function() {
        searchInput.style.borderColor = '#007bff';
        searchInput.style.boxShadow = '0 0 0 0.15rem rgba(0,123,255,.2)';
    });
    searchInput.addEventListener('blur', function() {
        searchInput.style.borderColor = '#e9ecef';
        searchInput.style.boxShadow = 'none';
    });
    searchBox.appendChild(searchIcon);
    searchBox.appendChild(searchInput);
    // --- Staff type filter select ---
    const filterDiv = document.createElement('div');
    filterDiv.style.minWidth = '180px';
    const staffTypeSelect = document.createElement('select');
    staffTypeSelect.id = 'staffTypeFilter';
    staffTypeSelect.className = 'form-select staff-type-filter';
    staffTypeSelect.style.fontSize = '0.62rem';
    staffTypeSelect.style.padding = '0.24rem 1.5rem 0.24rem 0.45rem';
    staffTypeSelect.style.minHeight = '1.8rem';
    staffTypeSelect.innerHTML = `
      <option value="">All Staff Types</option>
      <option value="Teaching">Teaching</option>
      <option value="Non-teaching">Non-Teaching</option>
    `;*/
   /* filterDiv.appendChild(staffTypeSelect);
    // --- Assemble UI ---
    searchFilterWrapper.appendChild(searchBox);
    searchFilterWrapper.appendChild(filterDiv);
    // --- Refresh button ---
    const refreshButton = document.createElement('button');
    refreshButton.type = 'button';
    refreshButton.className = 'btn btn-primary ms-2 shadow-sm d-flex align-items-center gap-2 rounded-pill refresh-btn-enhanced';
    refreshButton.style.height = '28px';
    refreshButton.style.fontWeight = '600';
    refreshButton.style.fontSize = '0.65rem';
    refreshButton.style.padding = '0.24rem 0.55rem';
    refreshButton.innerHTML = '<i class="fas fa-sync-alt fa-spin-on-hover" style="font-size:0.62rem;"></i> <span style="font-size:0.65rem;">Refresh</span>';
    refreshButton.onclick = function() {
        location.reload();
    };*/
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
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Instructor updated successfully!',
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
                        window.location.reload();
                    }
                });
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
            .then(response => response.text())
            .then(html => {
                Swal.close();
                
                Swal.fire({
                    title: 'Instructor Deleted!',
                    text: 'Instructor has been successfully removed from the system.',
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