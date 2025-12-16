<!DOCTYPE html>
<html>
<head>
    <style>
        /* ================= PAGE SETUP ================= */
        @page {
            margin: 0;
        }

        @page:first {
            background: url("{{ !empty($company?->letterhead) ? public_path($company->letterhead) : '' }}") 
                        no-repeat top center;
            background-size: 100% auto;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 130px 35px 120px 35px;
        }

        /* ================= COMMON STYLES ================= */
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
            margin: 0;
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

        .text-right {
            text-align: right;
        }

        h4 {
            margin: 8px 0;
        }
    </style>
</head>

<body>

    <!-- SPACE FOR LETTERHEAD -->
    <br><br><br><br>

    <h2 class="heading">Purchase Order</h2>

    <!-- ================= HEADER ================= -->
    <table class="no-border" style="margin-bottom:6px;">
        <tr>
            <td class="no-border">
                <strong>Ref. No:</strong> {{ $po->po_no }}
            </td>
            <td class="no-border text-right">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <!-- ================= COMPANY & PO DETAILS ================= -->
    <table>
        <tr>
            <td class="border-box" style="width:50%;">
                <strong>{{ $company->name ?? '' }}</strong><br>
                {!! nl2br(e($company->address ?? '')) !!}<br>
                Contact: {{ $company->phone ?? '' }}<br>
                Email: {{ $company->email ?? '' }}<br>
                GSTIN: {{ $company->gstin ?? '' }}
            </td>

            <td class="border-box" style="width:50%;">
                <strong>PO No:</strong> {{ $po->po_no }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}<br>
                <strong>Supplier Ref:</strong> {{ $po->supplier_ref }}<br>
                <strong>Dispatch Through:</strong> {{ $po->dispatch_through }}<br>
                <strong>Destination:</strong> {{ $po->destination }}
            </td>
        </tr>
    </table>

    <!-- ================= CONSIGNEE & DELIVERY ================= -->
    <table>
        <tr>
            <td class="border-box" style="width:50%;">
                <strong>Consignee:</strong><br><br>
                {{ $po->consignee_name }}<br>
                {!! nl2br(e($po->consignee_address)) !!}<br>
                Phone: {{ $po->consignee_phone }}<br>
                Email: {{ $po->consignee_email }}<br>
                GSTIN: {{ $po->consignee_gstin }}
            </td>

            <td class="border-box" style="width:50%;">
                <strong>Delivery Location:</strong><br><br>
                {{ $po->buyer_name }}<br>
                {!! nl2br(e($po->buyer_address)) !!}<br>
                Phone: {{ $po->buyer_phone }}<br>
                Email: {{ $po->buyer_email }}<br>
                GSTIN: {{ $po->buyer_gstin }}
            </td>
        </tr>
    </table>

    <!-- ================= ITEMS ================= -->
    <h4>Items</h4>
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
                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ================= TOTALS ================= -->
    <table style="margin-top:6px;">
        <tr>
            <td class="no-border text-right" style="width:70%;">Subtotal:</td>
            <td class="text-right">{{ number_format($po->subtotal, 2) }}</td>
        </tr>

        @if($po->gst_type === 'igst')
            <tr>
                <td class="no-border text-right">IGST:</td>
                <td class="text-right">{{ number_format($po->items->sum('igst_amount'), 2) }}</td>
            </tr>
        @else
            <tr>
                <td class="no-border text-right">CGST:</td>
                <td class="text-right">{{ number_format($po->cgst_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="no-border text-right">SGST:</td>
                <td class="text-right">{{ number_format($po->sgst_amount, 2) }}</td>
            </tr>
        @endif

        <tr>
            <td class="no-border text-right"><strong>Grand Total:</strong></td>
            <td class="text-right"><strong>{{ number_format($po->grand_total, 2) }}</strong></td>
        </tr>
    </table>

    <!-- ================= AMOUNT IN WORDS ================= -->
    <p>
        <strong>Grand Total (In Words):</strong> {{ $po->grandTotalWords }}
    </p>

    <!-- ================= TERMS ================= -->
    <h4>Terms & Conditions</h4>
    <table>
        @foreach($po->terms as $term)
            <tr>
                <td>{{ $term->term }}</td>
            </tr>
        @endforeach
    </table>

    <!-- ================= SIGNATURE ================= -->
    <div style="margin-top:70px; text-align:right;">
        <strong>{{ $po->authorised_name }}</strong><br>
        <strong>For: {{ $po->forpo }}</strong><br><br><br>
        <strong>Authorised Signatory</strong>
    </div>

</body>
</html>
