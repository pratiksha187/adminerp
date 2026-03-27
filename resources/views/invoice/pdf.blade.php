@extends('layouts.app')

@section('content')

<div style="max-width:900px; margin:auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);">

    <h2 style="text-align:center; margin-bottom:20px;">Invoice Preview</h2>

    <p><strong>Invoice No:</strong> {{ $invoice['invoice_no'] ?? '' }}</p>
    <p><strong>Date:</strong> {{ $invoice['date'] ?? '' }}</p>

    <!-- 🔥 VENDOR + BILL TO -->
    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
        <tr>
            <!-- Vendor -->
            <td style="width:50%; border:1px solid #000; padding:12px;">
                <strong>Vendor:</strong><br><br>

                <strong>{{ $invoice['vendor']['name'] ?? '' }}</strong><br>

                {!! nl2br(e($invoice['vendor']['address'] ?? '')) !!}<br>

                Phone: {{ $invoice['vendor']['phone'] ?? '' }}<br>
                Email: {{ $invoice['vendor']['email'] ?? '' }}<br>
                GSTIN: {{ $invoice['vendor']['gstin'] ?? '' }}
            </td>

            <!-- Bill To -->
            <td style="width:50%; border:1px solid #000; padding:12px;">
                <strong>Bill To:</strong><br><br>

                <strong>{{ $invoice['bill']['name'] ?? '' }}</strong><br>

                {!! nl2br(e($invoice['bill']['address'] ?? '')) !!}<br>

                Phone: {{ $invoice['bill']['phone'] ?? '' }}<br>
                Email: {{ $invoice['bill']['email'] ?? '' }}<br>
                GSTIN: {{ $invoice['bill']['gstin'] ?? '' }}
            </td>
        </tr>
    </table>

    <br>

    <!-- 🔹 DESCRIPTION -->
    <h4>Description</h4>
    <p>{!! nl2br(e($invoice['description'] ?? '')) !!}</p>

    <!-- 🔹 AMOUNT TABLE -->
    <table style="width:100%; border-collapse:collapse; margin-top:10px;">
        <tr>
            <th style="border:1px solid #000;">Amount (1%)</th>
            <th style="border:1px solid #000;">CGST (9%)</th>
            <th style="border:1px solid #000;">SGST (9%)</th>
            <th style="border:1px solid #000;">Total</th>
        </tr>

        <tr>
            <td style="border:1px solid #000; text-align:center;">
                ₹ {{ number_format($invoice['amount'] ?? 0, 2) }}
            </td>

            <td style="border:1px solid #000; text-align:center;">
                ₹ {{ number_format($invoice['cgst'] ?? 0, 2) }}
            </td>

            <td style="border:1px solid #000; text-align:center;">
                ₹ {{ number_format($invoice['sgst'] ?? 0, 2) }}
            </td>

            <td style="border:1px solid #000; text-align:center; font-weight:bold;">
                ₹ {{ number_format($invoice['total'] ?? 0, 2) }}
            </td>
        </tr>
    </table>

    <br>

    <p>
        <strong>Main Amount:</strong> 
        ₹ {{ number_format($invoice['main_amount'] ?? 0, 2) }}
    </p>

    <br><br>

    <!-- 🔹 BUTTON -->
    <a href="/invoice/download"
       style="background:#28a745; color:#fff; padding:10px 18px; border-radius:5px; text-decoration:none;">
        Download PDF
    </a>

</div>

@endsection