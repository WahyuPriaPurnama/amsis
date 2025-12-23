@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show my-3">
        {{ session()->get('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show my-3">
        {{ session()->get('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="card shadow">
    <div class="card-header">
        <b> {{ $header }} </b>
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
