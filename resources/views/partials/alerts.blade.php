@if(session('success'))
<div class="alert alert-success" role="status">
    <i class="fa-solid fa-check me-2" aria-hidden="true"></i>{{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger" role="alert">
    <i class="fa-solid fa-triangle-exclamation me-2" aria-hidden="true"></i>{{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger" role="alert">
    <i class="fa-solid fa-triangle-exclamation me-2" aria-hidden="true"></i>
    <span class="fw-bold">Periksa data berikut:</span>
    <ul>
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif
