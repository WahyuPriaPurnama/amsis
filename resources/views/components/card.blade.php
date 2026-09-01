@if (session()->has('success'))
<div class="alert alert-success alert-dismissible fade show my-3 shadow-sm border-0" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session()->get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif(session()->has('error'))
<div class="alert alert-danger alert-dismissible fade show my-3 shadow-sm border-0" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i> {{ session()->get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow border-0">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="mb-0 fw-bold text-dark"> {{ $header }}
        </h6>
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>