@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('store-requirement.create') }}" class="btn btn-primary btn-sm mb-3">
        ➕ New Material Requirement
    </a>

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
                <th width="160">Action</th>
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
                    <td>
                        <button class="btn btn-success btn-sm update-status" data-id="{{ $req->id }}" data-status="1">Accept</button>
                        <button class="btn btn-danger btn-sm update-status" data-id="{{ $req->id }}" data-status="2">Reject</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).on("click", ".update-status", function() {
    let id = $(this).data("id");
    let status = $(this).data("status");

    $.ajax({
        url: "{{ url('/store-requirements/status') }}/" + id,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            status: status
        },
        success: function(res) {
            if (res.success) {
                let statusText = "Pending";
                let badgeClass = "bg-secondary";
                if (status == 1) {
                    statusText = "Accepted";
                    badgeClass = "bg-success";
                } else if (status == 2) {
                    statusText = "Rejected";
                    badgeClass = "bg-danger";
                }

                let $row = $("#row-" + id).find(".status-text");
                $row.text(statusText)
                    .removeClass("bg-secondary bg-success bg-danger")
                    .addClass(badgeClass);
            }
        }
    });
});
</script>
@endsection
