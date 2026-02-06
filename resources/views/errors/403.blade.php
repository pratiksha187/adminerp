@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-5 text-center">
      <h1 class="fw-bold text-danger">403</h1>
      <p class="mb-3">You don’t have permission to access this page.</p>
      <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
  </div>
</div>
@endsection
