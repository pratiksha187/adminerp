@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">👁️ Requirement Details</h3>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Requester:</strong> {{ $requirement->user->name ?? '-' }}</p>
            <p><strong>Created At:</strong> {{ $requirement->created_at->format('d M Y h:i A') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>📦 Materials</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requirement->items as $i => $item)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
