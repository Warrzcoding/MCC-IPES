{{-- Rejected Requests Subpage --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">
                    <i class="fas fa-times-circle me-2 text-danger"></i>
                    Rejected Student Sign-Up Requesdswdsdsdsdts
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="rejectedSearch" class="form-control" placeholder="Search by name, username, email, or school ID...">
                    </div>
                </div>
                <div id="rejectedRequestsTable">
                    @include('pages.partials.rejected-requests-table', ['rejectedRequests' => $rejectedRequests])
                </div>
                <a href="{{ route('dashboard', ['page' => 'pending-requests']) }}" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Pending Requests
                </a>
            </div>
        </div>
    </div>
</div>
<script>
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('rejectedSearch');
    const tableDiv = document.getElementById('rejectedRequestsTable');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const value = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('search', value);
            url.searchParams.set('page', 'rejected-requests');
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.getElementById('rejectedRequestsTable');
                    if (newTable) {
                        tableDiv.innerHTML = newTable.innerHTML;
                    }
                });
        }, 350));
    }
});
</script> 