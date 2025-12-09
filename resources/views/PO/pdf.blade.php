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
             margin: 130px 35px 120px 35px; /* Top & bottom adjusted */
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
            margin-bottom: 25px; /* Increased */
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        .border-box {
            border: 1px solid #000;
            padding: 10px;
        }

        /* Footer space rule for preventing overlap */
        .space-bottom {
            margin-bottom: 75px;
        }

    </style>
</head>
<body>

<img src="{{ public_path('letterhead/SwarajyaLetterhead.jpg') }}" class="pdf-bg">

<h2 class="heading">Purchase Order</h2>

<!-- HEADER -->
<table style="border:0; margin-bottom:10px;">
<tr>
    <td style="border:0;">
        <strong>Ref. No.:</strong> {{ $po->ref_no }}
    </td>
    <td style="border:0; text-align:right;">
        <strong>Date:</strong> {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}
    </td>
</tr>
</table>

<!-- TOP TWO BOXES -->
<table class="space-bottom">
    <tr>
        <td class="border-box" style="width:50%">
            <strong>SWARAJYA CONSTRUCTION PRIVATE LIMITED</strong><br>
            Crescent peirl - B B-G/1, Veena Nagar,<br>
            Katrang Road, Near St. Anthony Church<br>
            Khopoli, Maharashtra 410203<br>
            Cont : 9326216153 <br>
            Email : swarajyaconstruction@outlook.com <br>
            GSTIN : 27ABOCS3387C1Z0 <br>
        </td>
        <td class="border-box" style="width:50%">
            <strong>Purchase Order No:</strong> {{ $po->po_no }}<br>
            <strong>Dated:</strong> {{ $po->po_date }}<br>
            <strong>Supplier’s Ref:</strong> {{ $po->supplier_ref }}<br>
            <strong>Dispatched through:</strong> {{ $po->dispatch_through }}<br>
            <strong>Destination:</strong> {{ $po->destination }}<br>
        </td>
    </tr>
</table>

<!-- CONSIGNEE & BUYER -->
<table>
    <tr>
        <td class="border-box" style="width:50%">
            <strong>Consignee :</strong><br><br>

            <strong>Company Name:</strong> {{ $po->consignee_name }}<br>
            <strong>Address:</strong><br>
            {!! nl2br($po->consignee_address) !!}<br>
            <strong>Phone:</strong> {{ $po->consignee_phone }}<br>
            <strong>Email:</strong> {{ $po->consignee_email }}<br>
            <strong>GSTIN:</strong> {{ $po->consignee_gstin }}<br>
        </td>

        <td class="border-box" style="width:50%">
            <strong>Delivery Location:</strong><br><br>

            <strong>Company Name:</strong> {{ $po->buyer_name }}<br>
            <strong>Address:</strong><br>
            {!! nl2br($po->buyer_address) !!}<br>
            <strong>Phone:</strong> {{ $po->buyer_phone }}<br>
            <strong>Email:</strong> {{ $po->buyer_email }}<br>
            <strong>GSTIN:</strong> {{ $po->buyer_gstin }}<br>
        </td>
    </tr>
</table>

<h4 style="margin-top:15px;">Items</h4>
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
        <td>{{ $i+1 }}</td>
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

<table style="margin-top:10px;">
    <tr>
        <td style="border:0; text-align:right; width:70%;">Subtotal:</td>
        <td>{{ number_format($po->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td style="border:0; text-align:right;">CGST ({{ $po->cgst_percent }}%):</td>
        <td>{{ number_format($po->cgst_amount, 2) }}</td>
    </tr>
    <tr>
        <td style="border:0; text-align:right;">SGST ({{ $po->sgst_percent }}%):</td>
        <td>{{ number_format($po->sgst_amount, 2) }}</td>
    </tr>
    <tr>
        <td style="border:0; text-align:right;"><strong>Grand Total:</strong></td>
        <td><strong>{{ number_format($po->grand_total, 2) }}</strong></td>
    </tr>
</table>

<p><strong>Grand Total In Words:</strong> {{ $po->grandTotalWords }}</p>

<h4>Terms & Conditions:</h4>
<table>
@foreach($po->terms as $term)
<tr><td>{{ $term->term }}</td></tr>
@endforeach
</table>

<!-- Signature -->
<div style="margin-top:80px; text-align:right;">
    <strong>For: {{$po->forpo}}</strong><br><br><br>
    <strong>Authorised Signatory</strong>
</div>

</body>
</html>
