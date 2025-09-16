@extends('layouts.app')

@section('title', 'All Store DPRs')

@section('content')
<div class="container my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">All Store DPRs</h3>
        <a href="{{ route('store-dpr.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i>Add DPR
        </a>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('store-dpr.list') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
<!-- Total Stock Available -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-stack me-2"></i>Total Stock Available</h5>
        @if($stocks->count())
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Item</th>
                        <th>Available Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td>{{ $stock->item_name }}</td>
                        <td>{{ $stock->available_qty }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted small">No stock records available.</p>
        @endif
    </div>
</div>

    <!-- DPR List -->
    @forelse($dprs as $index => $dpr)
    <div class="card shadow-sm mb-3 border-0 rounded-3">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse"
             data-bs-target="#dpr-{{ $index }}"
             role="button"
             aria-expanded="false"
             aria-controls="dpr-{{ $index }}"
             style="cursor:pointer;">
            <span class="fw-bold"><i class="bi bi-shop me-2"></i>Site: {{ $dpr->site_name }}</span>
        </div>

        <div id="dpr-{{ $index }}" class="collapse">
            <div class="card-body">

                <!-- Inwards -->
                <div class="mb-3">
                    <h6 class="text-success fw-bold"><i class="bi bi-box-arrow-in-down me-2"></i>Inward (Receipts)</h6>
                    @if(isset($inwards[$dpr->id]) && count($inwards[$dpr->id]))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>Item</th>
                                    <th>Vendor</th>
                                    <th>Rate</th>
                                    <th>Qty</th>
                                    <th>Type</th>
                                    <th>Available Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inwards[$dpr->id] as $inward)
                                <tr>
                                    <td>{{ $inward->item_name }}</td>
                                    <td>{{ $inward->vendor }}</td>
                                    <td>{{ $inward->rate }}</td>
                                    <td>{{ $inward->qty }}</td>
                                    <td>{{ $inward->type }}</td>
                                     <td>{{ $inward->available_qty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted small">No Inward items</p>
                    @endif
                </div>

                <!-- Outwards -->
                <div class="mb-3">
                    <h6 class="text-danger fw-bold"><i class="bi bi-box-arrow-up me-2"></i>Outward (Dispatches)</h6>
                    @if(isset($outwards[$dpr->id]) && count($outwards[$dpr->id]))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-danger">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Available Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outwards[$dpr->id] as $outward)
                                <tr>
                                    <td>{{ $outward->item_name }}</td>
                                    <td>{{ $outward->qty }}</td>
                                     <td>{{ $outward->available_qty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted small">No Outward items</p>
                    @endif
                </div>

                <!-- Issued -->
                <div class="mb-3">
                    <h6 class="text-warning fw-bold"><i class="bi bi-truck me-2"></i>Issued Materials</h6>
                    @if(isset($issued[$dpr->id]) && count($issued[$dpr->id]))
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-warning">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Available Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issued[$dpr->id] as $issue)
                                <tr>
                                    <td>{{ $issue->item_name }}</td>
                                    <td>{{ $issue->qty }}</td>
                                     <td>{{ $issue->available_qty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted small">No Issued items</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-warning">No DPRs found for the selected date range.</div>
    @endforelse

</div>
@endsection
