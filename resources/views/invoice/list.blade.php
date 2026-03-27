@extends('layouts.app')

@section('content')

<style>
    body { font-family: Arial; padding: 20px; }
    h2 { margin-bottom: 20px; }

    table { width: 100%; border-collapse: collapse; }
    table, th, td { border: 1px solid #ddd; }

    th { background: #f4f4f4; }
    th, td { padding: 10px; text-align: center; }

    .btn {
        padding: 6px 10px;
        text-decoration: none;
        border-radius: 4px;
        color: white;
    }

    .btn-view { background: #3490dc; }
    .btn-download { background: #38c172; }
    .btn-create { background: #ff9800; margin-bottom: 15px; display: inline-block; }
</style>

<h2>Invoice List</h2>

<a href="{{ route('invoice.form') }}" class="btn btn-create">+ Create Invoice</a>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Client</th>
            <th>Amount (₹)</th>
            <th>CGST (₹)</th>
            <th>SGST (₹)</th>
            <th>Total (₹)</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

    @forelse($invoices as $index => $inv)
        <tr>
            <td>{{ $index + 1 }}</td>

            <td>{{ $inv['invoice_no'] }}</td>
            <td>{{ $inv['date'] }}</td>

            <!-- ✅ CLIENT (NEW FORMAT) -->
            <td>
                {{ $inv['bill']['name'] ?? 'N/A' }}
            </td>

            <!-- ✅ AMOUNTS -->
            <td>{{ number_format($inv['amount'], 2) }}</td>
            <td>{{ number_format($inv['cgst'], 2) }}</td>
            <td>{{ number_format($inv['sgst'], 2) }}</td>
            <td><strong>{{ number_format($inv['total'], 2) }}</strong></td>

            <td>
                <a href="#" class="btn btn-view">View</a>
                <a href="/invoice/download" class="btn btn-download">PDF</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9">No invoices found</td>
        </tr>
    @endforelse

    </tbody>
</table>

@endsection