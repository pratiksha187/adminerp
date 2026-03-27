@extends('layouts.app')

@section('content')

<style>
    body { font-family: Arial; }
    .invoice-box { width: 800px; margin: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid black; padding: 8px; }
</style>

<div class="invoice-box">

    <!-- ✅ LETTERHEAD -->
    <div style="width: 100%; margin-bottom: 15px;">
        <img src="{{ public_path('letterhead/SwarajyaLetterhead.jpg') }}" 
             style="width:100%; height:auto;">
    </div>

    <h3 style="text-align:center;">TAX INVOICE</h3>

    <p><strong>Invoice No:</strong> {{ $invoice['invoice_no'] }}</p>
    <p><strong>Date:</strong> {{ $invoice['date'] }}</p>

    <!-- 🔥 VENDOR + BILL TO -->
    <table>
        <tr>
            <td style="width:50%;">
                <strong>Vendor:</strong><br>
                {{ $invoice['vendor']['name'] }}<br>
                {{ $invoice['vendor']['address'] }}<br>
                Phone: {{ $invoice['vendor']['phone'] }}<br>
                Email: {{ $invoice['vendor']['email'] }}<br>
                GSTIN: {{ $invoice['vendor']['gstin'] }}
            </td>

            <td style="width:50%;">
                <strong>Bill To:</strong><br>
                {{ $invoice['bill']['name'] }}<br>
                {{ $invoice['bill']['address'] }}<br>
                Phone: {{ $invoice['bill']['phone'] }}<br>
                Email: {{ $invoice['bill']['email'] }}<br>
                GSTIN: {{ $invoice['bill']['gstin'] }}
            </td>
        </tr>
    </table>

    <!-- 🔹 DESCRIPTION -->
    <h4>Description</h4>
    <p>{{ $invoice['description'] }}</p>

    <!-- 🔹 TABLE -->
    <table>
        <tr>
            <th>Amount (1%)</th>
            <th>CGST (9%)</th>
            <th>SGST (9%)</th>
            <th>Total</th>
        </tr>

        <tr>
            <td>{{ number_format($invoice['amount'], 2) }}</td>
            <td>{{ number_format($invoice['cgst'], 2) }}</td>
            <td>{{ number_format($invoice['sgst'], 2) }}</td>
            <td>{{ number_format($invoice['total'], 2) }}</td>
        </tr>
    </table>

    <br>

    <p><strong>Main Amount:</strong> ₹ {{ number_format($invoice['main_amount'], 2) }}</p>

    <br><br>

    <a href="/invoice/download">Download PDF</a>

    <br><br>
    <p>Authorized Signature</p>

</div>

@endsection