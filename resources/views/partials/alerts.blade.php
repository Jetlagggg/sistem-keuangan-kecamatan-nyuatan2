@if(session('success'))
<div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif
