@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <p class="mb-1 fw-semibold">Please fix the following:</p>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
