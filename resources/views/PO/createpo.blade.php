@extends('layouts.app')

@section('content')

<title>Purchase Order</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body { background: #fff; font-size: 14px; }
    .border-box { border: 1px solid #000; padding: 10px; }
    .table th, .table td { border: 1px solid #000 !important; padding: 4px 6px; }
    .heading { font-weight: 600; text-decoration: underline; }
    .stamp { width: 160px; opacity: 0.7; }
    .p-4 { padding: 3.5rem !important; }
</style>

<body class="p-4">
<div class="container border p-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <!-- FORM START -->
    <form action="{{ route('po.store') }}" method="POST">
        @csrf
        {{-- COMPANY SELECT --}}
        <div class="mb-3">
            <label class="form-label"><strong>Select Company</strong></label>
            <select name="company_id" id="companySelect" class="form-control" required>
                <option value="">-- Select Company --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                        data-name="{{ $company->name }}"
                        data-address="{{ $company->address }}"
                        data-phone="{{ $company->phone }}"
                        data-email="{{ $company->email }}"
                        data-gstin="{{ $company->gstin }}">
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <!-- HEADER -->
        <div class="d-flex justify-content-between mb-3">

            <div class="mb-2">
                <label class="form-label"><strong>Ref. No.:</strong></label>
                <input type="text" name="ref_no" class="form-control" placeholder="Enter Ref No" value="{{$po_no}}">
            </div>

            <div class="d-flex align-items-center">
                <strong class="me-2">Date:</strong>
                <span id="todayDate"></span>
            </div>

        </div>

        <h4 class="text-center heading mb-4">Purchase Order</h4>

        <div class="row mb-3">

            <!-- LEFT BOX -->
            <!-- <div class="col-6 border-box">
                <strong>SWARAJYA CONSTRUCTION PRIVATE LIMITED</strong><br>
                Crescent peirl - B B-G/1, Veena Nagar,<br>
                Katrang Road, Near St. Anthony Church<br>
                Khopoli, Maharashtra 410203<br>
                Cont : 9326216153 <br>
                Email : swarajyaconstruction@outlook.com <br>
                GSTIN : 27ABOCS3387C1Z0 <br>
            </div> -->
            <div class="col-6 border-box" id="companyBox">
                <strong id="c_name">Select Company</strong><br>
                <span id="c_address"></span><br>
                Cont : <span id="c_phone"></span><br>
                Email : <span id="c_email"></span><br>
                GSTIN : <span id="c_gstin"></span><br>
            </div>


            <!-- RIGHT BOX -->
            <div class="col-6 border-box">

                <div class="mb-2">
                    <strong>Purchase Order No:</strong>
                    <input type="text" name="po_no" class="form-control form-control-sm mt-1"
                           value="{{$po_no}}">
                </div>

                <div class="mb-2">
                    <strong>Dated:</strong>
                    <input type="date" name="po_date" class="form-control form-control-sm mt-1"
                           value="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-2">
                    <strong>Supplier’s Ref:</strong>
                    <input type="text" name="supplier_ref" class="form-control form-control-sm mt-1" placeholder="Enter Supplier Ref">
                </div>

                <div class="mb-2">
                    <strong>Dispatched through:</strong>
                    <input type="text" name="dispatch_through" class="form-control form-control-sm mt-1" placeholder="Enter Dispatch Mode">
                </div>

                <div class="mb-2">
                    <strong>Destination:</strong>
                    <input type="text" name="destination" class="form-control form-control-sm mt-1" placeholder="Enter Destination">
                </div>

            </div>
        </div>
        <div class="row mb-3">

            <!-- CONSIGNEE -->
            <div class="col-6 border-box">
                <strong>Consignee :</strong>
                <br>
                <label class="mt-1 mb-0 small">Company Name:</label>
               
                <input type="text" name="consignee_name" class="form-control form-control-sm"
                    value="SWARAJYA CONSTRUCTION PRIVATE LIMITED">

                <label class="mt-1 mb-0 small">Address:</label>
                <textarea name="consignee_address" class="form-control form-control-sm">Crescent Pearl - B B-G/1, Veena Nagar,
                    Katrang Road, Near St. Anthony Church
                    Khopoli, Maharashtra - 410203</textarea>

                <label class="mt-1 mb-0 small">Phone No.:</label>
                <input type="text" name="consignee_phone" class="form-control form-control-sm"
                    value="+91 93262 16153">

                <label class="mt-1 mb-0 small">Email:</label>
                <input type="email" name="consignee_email" class="form-control form-control-sm"
                    value="info@constructkaro.com">

                <label class="mt-1 mb-0 small">GSTIN:</label>
                <input type="text" name="consignee_gstin" class="form-control form-control-sm"
                    value="27AASCS6790G1Z0">
            </div>

            <!-- BUYER -->
            <div class="col-6 border-box">
                <strong>Delivery Location:     </strong>
                <br>
                <label class="mt-1 mb-0 small">Company Name:</label>
                <input type="text" name="buyer_name" class="form-control form-control-sm"
                    value="SWARAJYA CONSTRUCTION PRIVATE LIMITED">

                <label class="mt-1 mb-0 small">Address:</label>
                <textarea name="buyer_address" class="form-control form-control-sm mt-1">Crescent peirl - B B-G/1, Veena Nagar,
                    Katrang Road, Near St. Anthony Church
                    Khopoli, Maharashtra 410203</textarea>

                <label class="mt-1 mb-0 small">Phone No.:</label>
                <input type="text" name="buyer_phone" class="form-control form-control-sm"
                    value="+91 93262 16153">

                <label class="mt-1 mb-0 small">Email:</label>
                <input type="email" name="buyer_email" class="form-control form-control-sm"
                    value="info@constructkaro.com">

                <label class="mt-1 mb-0 small">GSTIN:</label>
                <input type="text" name="buyer_gstin" class="form-control form-control-sm"
                    value="27AASCS6790G1Z0">
            </div>

        </div>

        <!-- ITEMS TABLE -->
        <table class="table table-bordered text-center" id="poTable">
            <thead>
            <tr>
                <th>Sr. No</th>
                <th>Item Description</th>
                <th>HSN Code</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td class="sr">1</td>
                <td><input type="text" name="items[0][description]" class="form-control"></td>
                <td><input type="text" name="items[0][hsn]" class="form-control"></td>
                <td><input type="number" name="items[0][qty]" class="form-control qty"></td>
                <td><input type="text" name="items[0][unit]" class="form-control"></td>
                <td><input type="number" name="items[0][rate]" class="form-control rate"></td>
                <td><input type="number" name="items[0][amount]" class="form-control amount" readonly></td>
                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
            </tbody>

            <tfoot>
            <tr>
                <th colspan="6" class="text-end">Subtotal</th>
                <th><input type="text" id="subtotal" name="subtotal" class="form-control" readonly></th>
                <th></th>
            </tr>

            <tr>
                <th colspan="6" class="text-end">CGST (%)</th>
                <th><input type="number" id="cgstPercent" name="cgstPercent" class="form-control" value="9"></th>
                <th></th>
            </tr>

            <tr>
                <th colspan="6" class="text-end">CGST Amount</th>
                <th><input type="text" id="cgstAmount" name="cgstAmount" class="form-control" readonly></th>
                <th></th>
            </tr>

            <tr>
                <th colspan="6" class="text-end">SGST (%)</th>
                <th><input type="number" id="sgstPercent" name="sgstPercent" class="form-control" value="9"></th>
                <th></th>
            </tr>

            <tr>
                <th colspan="6" class="text-end">SGST Amount</th>
                <th><input type="text" id="sgstAmount" name="sgstAmount" class="form-control" readonly></th>
                <th></th>
            </tr>

            <tr>
                <th colspan="6" class="text-end">Grand Total</th>
                <th><input type="text" id="grandTotal" name="grandTotal" class="form-control fw-bold" readonly></th>
                <th></th>
            </tr>
           


            </tfoot>

        </table>
            Grand Total (in Words)<input type="text" name="grandTotalWords" id="grandTotalWords" class="form-control fw-bold" readonly>
        <!-- <button class="btn btn-primary mb-3" id="addRowBtn">Add Item</button> -->
         <button type="button" class="btn btn-primary mb-3" id="addRowBtn">Add Item</button>


        <div class="border-box mt-3">
            <strong>Terms & Conditions:</strong>

            <table class="table table-bordered mt-2" id="termsTable">
                <tbody>
                <tr>
                    <td>
                        <input type="text" name="terms[]" class="form-control form-control-sm"
                               placeholder="Enter term & condition">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-success btn-sm" id="addTermBtn">
                + Add New Row
            </button>
        </div>

        <!-- SIGNATURE -->
        <!-- <div class="mt-4 text-end">
            <strong class="me-2">For:</strong>
            <input type="text" class="form-control form-control-sm d-inline-block" name="forpo" style="width:250px;">
            <br><br><br>
            <strong>Authorised Signatory</strong>
        </div> -->
        <!-- SIGNATURE -->
<div class="mt-4 text-end">
    <label class="small fw-bold">Authorised Person Name</label>
    <input type="text"
           name="authorised_name"
           class="form-control form-control-sm d-inline-block"
           style="width:250px;"
           placeholder="Enter Name">

    <br><br>

    <strong>For:</strong>
    <input type="text"
           class="form-control form-control-sm d-inline-block"
           name="forpo"
           style="width:250px;"
           placeholder="Company / Firm Name">

    <br><br><br>

    <strong>Authorised Signatory</strong>
</div>


        <button type="submit" class="btn btn-success mt-4 w-100">SAVE PURCHASE ORDER</button>

    </form>
    <!-- FORM END -->

</div>
<script>
    // Company selection dynamic change
document.getElementById('companySelect').addEventListener('change', function () {
    let opt = this.options[this.selectedIndex];

    document.getElementById('c_name').innerText = opt.dataset.name || '';
    document.getElementById('c_address').innerText = opt.dataset.address || '';
    document.getElementById('c_phone').innerText = opt.dataset.phone || '';
    document.getElementById('c_email').innerText = opt.dataset.email || '';
    document.getElementById('c_gstin').innerText = opt.dataset.gstin || '';
});

</script>
<script>
// Set Today's Date
const today = new Date();
document.getElementById("todayDate").textContent =
    today.toLocaleDateString("en-GB");

// Update serial numbers
function updateSerialNumbers() {
    document.querySelectorAll("#poTable tbody tr").forEach((row, index) => {
        row.querySelector(".sr").textContent = index + 1;
    });
}

// Calculate row amount
function calculateAmount(row) {
    let qty = parseFloat(row.querySelector(".qty").value) || 0;
    let rate = parseFloat(row.querySelector(".rate").value) || 0;
    row.querySelector(".amount").value = (qty * rate).toFixed(2);
    calculateTotal();
}

// Calculate totals + GST
function calculateTotal() {
    let subtotal = 0;

    document.querySelectorAll(".amount").forEach(amount => {
        subtotal += parseFloat(amount.value) || 0;
    });

    document.getElementById("subtotal").value = subtotal.toFixed(2);

    let cgstPercent = parseFloat(document.getElementById("cgstPercent").value) || 0;
    let sgstPercent = parseFloat(document.getElementById("sgstPercent").value) || 0;

    let cgstAmount = (subtotal * cgstPercent / 100);
    let sgstAmount = (subtotal * sgstPercent / 100);

    document.getElementById("cgstAmount").value = cgstAmount.toFixed(2);
    document.getElementById("sgstAmount").value = sgstAmount.toFixed(2);

    // let grandTotal = subtotal + cgstAmount + sgstAmount;

    // document.getElementById("grandTotal").value = grandTotal.toFixed(2);

    // // Add this line to display in words
    // document.getElementById("grandTotalWords").value = numberToWords(Math.round(grandTotal));

    let grandTotal = subtotal + cgstAmount + sgstAmount;
    document.getElementById("grandTotal").value = grandTotal.toFixed(2);
    document.getElementById("grandTotalWords").value = numberToWords(grandTotal);


}

// Add Item Row
let itemIndex = 1;

document.getElementById("addRowBtn").addEventListener("click", () => {
    let tbody = document.querySelector("#poTable tbody");
    let newRow = document.createElement("tr");

    newRow.innerHTML = `
        <td class="sr"></td>
        <td><input type="text" name="items[${itemIndex}][description]" class="form-control"></td>
        <td><input type="text" name="items[${itemIndex}][hsn]" class="form-control"></td>
        <td><input type="number" name="items[${itemIndex}][qty]" class="form-control qty"></td>
        <td><input type="text" name="items[${itemIndex}][unit]" class="form-control"></td>
        <td><input type="number" name="items[${itemIndex}][rate]" class="form-control rate"></td>
        <td><input type="number" name="items[${itemIndex}][amount]" class="form-control amount" readonly></td>
        <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
    `;

    tbody.appendChild(newRow);
    updateSerialNumbers();

    newRow.querySelector(".qty").addEventListener("input", () => calculateAmount(newRow));
    newRow.querySelector(".rate").addEventListener("input", () => calculateAmount(newRow));

    newRow.querySelector(".removeRow").addEventListener("click", () => {
        newRow.remove();
        updateSerialNumbers();
        calculateTotal();
    });

    itemIndex++;
});

// First row listeners
document.querySelector(".qty").addEventListener("input", function () {
    calculateAmount(this.closest("tr"));
});

document.querySelector(".rate").addEventListener("input", function () {
    calculateAmount(this.closest("tr"));
});

// GST change listener
document.getElementById("cgstPercent").addEventListener("input", calculateTotal);
document.getElementById("sgstPercent").addEventListener("input", calculateTotal);

// Add Term Row
document.getElementById('addTermBtn').addEventListener('click', function () {
    let tableBody = document.querySelector('#termsTable tbody');

    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" name="terms[]" class="form-control form-control-sm" placeholder="Enter term"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
    `;

    tableBody.appendChild(newRow);
});

// Remove Terms Row
document.addEventListener('click', function (e) {
    if(e.target && e.target.classList.contains('removeRow')){
        e.target.closest('tr').remove();
    }
});

function numberToWords(num) {
    const a = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    ];
    const b = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    if (isNaN(num)) return '';

    let str = '';
    let [rupees, paise] = num.toString().split('.');
    rupees = parseInt(rupees);
    paise = parseInt(paise || 0);

    if (rupees === 0) str = 'Zero Rupees';
    else {
        const crore = Math.floor(rupees / 10000000);
        rupees %= 10000000;
        const lakh = Math.floor(rupees / 100000);
        rupees %= 100000;
        const thousand = Math.floor(rupees / 1000);
        rupees %= 1000;
        const hundred = Math.floor(rupees / 100);
        rupees %= 100;

        if (crore) str += (a[crore] || (b[Math.floor(crore/10)] + ' ' + a[crore%10])) + ' Crore ';
        if (lakh) str += (a[lakh] || (b[Math.floor(lakh/10)] + ' ' + a[lakh%10])) + ' Lakh ';
        if (thousand) str += (a[thousand] || (b[Math.floor(thousand/10)] + ' ' + a[thousand%10])) + ' Thousand ';
        if (hundred) str += a[hundred] + ' Hundred ';
        if (rupees) str += (str !== '' ? 'and ' : '') + (a[rupees] || (b[Math.floor(rupees/10)] + ' ' + a[rupees%10]));
        str += ' Rupees';
    }

    if (paise > 0) {
        if (paise < 20) str += ' and ' + a[paise] + ' Paise';
        else str += ' and ' + b[Math.floor(paise / 10)] + ' ' + a[paise % 10] + ' Paise';
    }

    return str.trim() + ' Only';
}


// function numberToWords(num) {
//     const a = [
//         '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
//         'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
//     ];
//     const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

//     if ((num = num.toString()).length > 9) return 'Overflow';
//     let n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{3})$/);
//     if (!n) return; 
//     let str = '';
//     str += (Number(n[1]) !== 0) ? (a[Number(n[1])] || (b[n[1][0]] + ' ' + a[n[1][1]])) + ' Crore ' : '';
//     str += (Number(n[2]) !== 0) ? (a[Number(n[2])] || (b[n[2][0]] + ' ' + a[n[2][1]])) + ' Lakh ' : '';
//     str += (Number(n[3]) !== 0) ? (a[Number(n[3])] || (b[n[3][0]] + ' ' + a[n[3][1]])) + ' Thousand ' : '';
//     str += (Number(n[4]) !== 0) ? (a[Number(n[4])] || (b[n[4][0]] + ' ' + a[n[4][1]])) + ' ' : '';
//     return str.trim() + ' Rupees Only';
// }

</script>

@endsection
