<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 130px 35px 120px 35px;
        }

        .pdf-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: -1;
        }

        .heading {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0; /* 🔥 removes gap between tables */
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .border-box {
            border: 1px solid #000;
            padding: 10px;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>

<body>

@if($company && $company->letterhead)
    <img src="{{ public_path($company->letterhead) }}" class="pdf-bg">
@endif
<br><br><br><br>
<h2 class="heading">Purchase Order</h2>

<!-- HEADER -->
<table class="no-border" style="margin-bottom:6px;">
    <tr>
        <td class="no-border">
            <strong>Ref. No:</strong> {{ $po->po_no }}
        </td>
        <td class="no-border" style="text-align:right;">
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}
        </td>
    </tr>
</table>

<!-- TOP TWO BOXES -->
<table>
    <tr>
        <td class="border-box" style="width:50%;">
            <strong>{{ $company->name }}</strong><br>
            {!! nl2br($company->address) !!}<br>
            Contact: {{ $company->phone }}<br>
            Email: {{ $company->email }}<br>
            GSTIN: {{ $company->gstin }}
        </td>

        <td class="border-box" style="width:50%;">
            <strong>Purchase Order No:</strong> {{ $po->po_no }}<br>
            <strong>Dated:</strong> {{ $po->po_date }}<br>
            <strong>Supplier’s Ref:</strong> {{ $po->supplier_ref }}<br>
            <strong>Dispatched Through:</strong> {{ $po->dispatch_through }}<br>
            <strong>Destination:</strong> {{ $po->destination }}
        </td>
    </tr>
</table>

<!-- CONSIGNEE & DELIVERY (NO GAP) -->
<table>
    <tr>
        <td class="border-box" style="width:50%;">
            <strong>Consignee:</strong><br><br>
            <strong>Company Name:</strong> {{ $po->consignee_name }}<br>
            <strong>Address:</strong><br>
            {!! nl2br($po->consignee_address) !!}<br>
            <strong>Phone:</strong> {{ $po->consignee_phone }}<br>
            <strong>Email:</strong> {{ $po->consignee_email }}<br>
            <strong>GSTIN:</strong> {{ $po->consignee_gstin }}
        </td>

        <td class="border-box" style="width:50%;">
            <strong>Delivery Location:</strong><br><br>
            <strong>Company Name:</strong> {{ $po->buyer_name }}<br>
            <strong>Address:</strong><br>
            {!! nl2br($po->buyer_address) !!}<br>
            <strong>Phone:</strong> {{ $po->buyer_phone }}<br>
            <strong>Email:</strong> {{ $po->buyer_email }}<br>
            <strong>GSTIN:</strong> {{ $po->buyer_gstin }}
        </td>
    </tr>
</table>

<!-- ITEMS -->
<h4 style="margin-top:10px;">Items</h4>

<table>
    <thead>
        <tr>
            <th>Sr</th>
            <th>Description</th>
            <th>HSN</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($po->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->hsn }}</td>
            <td>{{ $item->qty }}</td>
            <td>{{ $item->unit }}</td>
            <td>{{ number_format($item->rate, 2) }}</td>
            <td>{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- TOTALS -->
<table style="margin-top:0px;">
    <tr>
        <td class="no-border" style="width:70%; text-align:right;">Subtotal:</td>
        <td>{{ number_format($po->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td class="no-border" style="text-align:right;">CGST ({{ $po->cgst_percent }}%):</td>
        <td>{{ number_format($po->cgst_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="no-border" style="text-align:right;">SGST ({{ $po->sgst_percent }}%):</td>
        <td>{{ number_format($po->sgst_amount, 2) }}</td>
    </tr>

    <br> <br> <br> <br>
    <tr>
        <td class="no-border" style="text-align:right;"><strong>Grand Total:</strong></td>
        <td><strong>{{ number_format($po->grand_total, 2) }}</strong></td>
    </tr>
</table>

<p><strong>Grand Total In Words:</strong> {{ $po->grandTotalWords }}</p>

<!-- TERMS -->
<h4>Terms & Conditions:</h4>
<table>
    @foreach($po->terms as $term)
    <tr>
        <td>{{ $term->term }}</td>
    </tr>
    @endforeach
</table>

<!-- SIGNATURE -->
<div style="margin-top:70px; text-align:right;">
    <strong>{{ $po->authorised_name }}</strong><br>
    <strong>For: {{ $po->forpo }}</strong><br><br><br>
    <strong>Authorised Signatory</strong>
</div>

</body>
</html>
