@extends('layouts.app')

@section('content')
<div class="container">
   
    <h3 class="mb-4">📋 Material Requirements</h3>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Requester</th>
                <th>Material</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Remarks</th>
                <th>Status</th>
                
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($requirements as $req)
                <tr id="row-{{ $req->id }}">
                    <td>{{ $i++ }}</td>
                    <td>{{ $req->requester_name }}</td>
                    <td>{{ $req->name }}</td>
                    <td>{{ $req->qty }}</td>
                    <td>{{ $req->unit }}</td>
                    <td>{{ $req->remark }}</td>
                    <td>
                        @php
                            $statusText = 'Pending';
                            $badgeClass = 'bg-secondary';
                            if ($req->is_approved == 1) {
                                $statusText = 'Accepted';
                                $badgeClass = 'bg-success';
                            } elseif ($req->is_approved == 2) {
                                $statusText = 'Rejected';
                                $badgeClass = 'bg-danger';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }} status-text">{{ $statusText }}</span>
                    </td>
                   
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection
