@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Store Report - {{ $storeReport->store_name }}</h1>
        <p><strong>Date:</strong> {{ $storeReport->date }}</p>
        <p><strong>Inward Material:</strong> {{ json_encode($storeReport->inward_material) }}</p>
        <p><strong>Outward Material:</strong> {{ json_encode($storeReport->outward_material) }}</p>
        <p><strong>Tasks Completed:</strong> {{ json_encode($storeReport->tasks_completed) }}</p>
        <a href="{{ route('store_reports.index') }}" class="btn btn-secondary">Back</a>
    </div>
@endsection
