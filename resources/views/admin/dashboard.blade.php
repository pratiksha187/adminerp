@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <h3 class="mb-4">Welcome, {{ Auth::user()->name }}!</h3>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4 hover-shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-bar-chart-fill me-2"></i> Stats</h5>
                    <p class="card-text">Some quick stats here.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4 hover-shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-gear-fill me-2"></i> Settings</h5>
                    <p class="card-text">Quick access to settings.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
