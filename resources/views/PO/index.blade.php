@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Purchase Orders</h3>
        <a href="{{ route('createpo') }}" class="btn btn-primary">+ Create New PO</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">#</th>
                        <!-- <th>id</th> -->
                        <th width="15%">PO Number</th>
                        <th width="15%">Supplier Ref</th>
                        <th width="12%">Date</th>
                        <th width="12%">Subtotal</th>
                        <th width="12%">Grand Total</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($purchaseOrders as $index => $po)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                             <!-- <td>{{ $po->id }}</td> -->
                            <td>{{ $po->po_no }}</td>
                            <td>{{ $po->supplier_ref }}</td>
                            <td>{{ \Carbon\Carbon::parse($po->po_date)->format('d-m-Y') }}</td>
                            <td>₹ {{ number_format($po->subtotal, 2) }}</td>
                            <td class="fw-bold">₹ {{ number_format($po->grand_total, 2) }}</td>

                            <td>
                               
                                <a href="{{ url('po_pdfs/PO_'.$po->id.'.pdf') }}" target="_blank" class="btn btn-sm btn-secondary">
                                    PDF
                                </a>
                                 <form action="{{ route('po.destroy', $po->id) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this Purchase Order?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            Delete
        </button>
    </form>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No Purchase Orders Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
