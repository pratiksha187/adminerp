<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; }
        .invoice-box { width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; }
    </style>
</head>
<body>

<div class="invoice-box">

    <!-- LETTERHEAD -->
    <img src="{{ public_path('letterhead/SwarajyaLetterhead.jpg') }}" style="width:100%;">

    <h3 style="text-align:center;">TAX INVOICE</h3>

    <p><strong>Invoice No:</strong> {{ $invoice['invoice_no'] }}</p>
    <p><strong>Date:</strong> {{ $invoice['date'] }}</p>

    <table>
        <tr>
            <td>
                <strong>Vendor:</strong><br>
                {{ $invoice['vendor']['name'] }}<br>
                {{ $invoice['vendor']['address'] }}
            </td>
            <td>
                <strong>Bill To:</strong><br>
                {{ $invoice['bill']['name'] }}<br>
                {{ $invoice['bill']['address'] }}
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Amount</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>{{ $invoice['amount'] }}</td>
            <td>{{ $invoice['cgst'] }}</td>
            <td>{{ $invoice['sgst'] }}</td>
            <td>{{ $invoice['total'] }}</td>
        </tr>
    </table>

</div>

</body>
</html>