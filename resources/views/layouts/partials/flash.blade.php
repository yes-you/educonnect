{{-- data-auto-dismiss = milliseconds before the box fades away (see educonnect.js). --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss="8000">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
