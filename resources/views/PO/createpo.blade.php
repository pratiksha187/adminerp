@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#fff; font-size:14px; }
.border-box { border:1px solid #000; padding:10px; }
.table th,.table td { border:1px solid #000!important; padding:4px 6px; }
.heading { font-weight:600; text-decoration:underline; }
</style>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container border p-4">

<form action="{{ route('po.store') }}" method="POST">
@csrf

<h4 class="text-center heading mb-4">Purchase Order</h4>

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

{{-- HEADER --}}
<div class="d-flex justify-content-between mb-3">
    <div style="width:220px;">
        <label><strong>Ref. No.</strong></label>
        <input type="text" name="ref_no" class="form-control" value="{{ $po_no }}">
    </div>
    <div class="align-self-end">
        <strong>Date:</strong> <span id="todayDate"></span>
    </div>
</div>

{{-- COMPANY BOX --}}
<div class="row mb-3">
    <div class="col-6 border-box">
        <strong id="c_name">Select Company</strong><br>
        <span id="c_address"></span><br>
        Cont: <span id="c_phone"></span><br>
        Email: <span id="c_email"></span><br>
        GSTIN: <span id="c_gstin"></span>
    </div>

    <div class="col-6 border-box">
        <label>PO No</label>
        <input name="po_no" class="form-control mb-2" value="{{ $po_no }}">

        <label>Date</label>
        <input type="date" name="po_date" class="form-control mb-2" value="{{ date('Y-m-d') }}">

        <input name="supplier_ref" class="form-control mb-2" placeholder="Supplier Ref">
        <input name="dispatch_through" class="form-control mb-2" placeholder="Dispatch Through">
        <input name="destination" class="form-control" placeholder="Destination">
    </div>
</div>
   <div class="row mb-3">
            <!-- CONSIGNEE -->
            <div class="col-6 border-box">
                <strong>Consignee :</strong><br>

                <label class="mt-1 mb-0 small">Company Name:</label>
                <input type="text" name="consignee_name" class="form-control form-control-sm"
                       value="">

                <label class="mt-1 mb-0 small">Address:</label>
                <textarea name="consignee_address" class="form-control form-control-sm" rows="3">
</textarea>

                <label class="mt-1 mb-0 small">Phone No.:</label>
                <input type="text" name="consignee_phone" class="form-control form-control-sm"
                       value="">

                <label class="mt-1 mb-0 small">Email:</label>
                <input type="email" name="consignee_email" class="form-control form-control-sm"
                       value="">

                <label class="mt-1 mb-0 small">GSTIN:</label>
                <input type="text" name="consignee_gstin" class="form-control form-control-sm"
                       value="">
            </div>

            <!-- DELIVERY LOCATION -->
            <div class="col-6 border-box">
                <strong>Delivery Location :</strong><br>

                <label class="mt-1 mb-0 small">Company Name:</label>
                <input type="text" name="buyer_name" class="form-control form-control-sm"
                       value="">

                <label class="mt-1 mb-0 small">Address:</label>
                <textarea name="buyer_address" class="form-control form-control-sm" rows="3">
</textarea>

                <label class="mt-1 mb-0 small">Phone No.:</label>
                <input type="text" name="buyer_phone" class="form-control form-control-sm"
                       value="">

                <label class="mt-1 mb-0 small">Email:</label>
                <input type="email" name="buyer_email" class="form-control form-control-sm"
                       value="">

                <label class="mt-1 mb-0 small">GSTIN:</label>
                <input type="text" name="buyer_gstin" class="form-control form-control-sm"
                       value="">
            </div>
        </div>


{{-- ITEMS TABLE --}}
<table class="table text-center" id="poTable">
<thead>
<tr>
    <th>Sr</th>
    <th>Description</th>
    <th>HSN</th>
    <th>Qty</th>
    <th>Unit</th>
    <th>Rate</th>
    <th>Amount</th>
    <th class="igstCol d-none">IGST %</th>
    <th class="igstCol d-none">IGST Amt</th>
    <th>Action</th>
</tr>
</thead>

<tbody id="itemBody">
<tr>
    <td class="sr">1</td>
    <td><input name="items[0][description]" class="form-control"></td>
    <td><input name="items[0][hsn]" class="form-control"></td>
    <td><input name="items[0][qty]" class="form-control qty"></td>
    <td><input name="items[0][unit]" class="form-control"></td>
    <td><input name="items[0][rate]" class="form-control rate"></td>
    <td><input name="items[0][amount]" class="form-control amount" readonly></td>

    <td class="igstCol d-none">
        <select name="items[0][igst_percent]" class="form-control igst_percent">
            <option value="0">0%</option>
            <option value="5">5%</option>
            <option value="12">12%</option>
            <option value="18">18%</option>
        </select>
    </td>

    <td class="igstCol d-none">
        <input name="items[0][igst_amount]" class="form-control igst_amount" readonly>
    </td>

    <td><button type="button" class="btn btn-danger btn-sm remove-item">X</button></td>
</tr>
</tbody>

<tfoot>


<tr>
    <th colspan="6" class="text-end">GST Type</th>
    <th>
        <!-- <select id="gstType" class="form-control"> -->
            <select id="gstType" name="gst_type" class="form-control">

            <option value="cgst_sgst">CGST + SGST</option>
            <option value="igst">IGST</option>
        </select>
    </th>
</tr>

<tbody id="igstSummary"></tbody>

<tr class="cgstRow">
    <th colspan="6" class="text-end">CGST (9%)</th>
    <th>
        <input id="cgstAmount" name="cgst_amount" class="form-control" readonly>
    </th>
</tr>

<tr class="cgstRow">
    <th colspan="6" class="text-end">SGST (9%)</th>
    <th>
        <input id="sgstAmount" name="sgst_amount" class="form-control" readonly>
    </th>
</tr>
<tr>
    <th colspan="6" class="text-end">Sub Total</th>
    <th><input id="subtotal" name="subtotal" class="form-control" readonly>
    </th>
</tr>
<tr>
    <th colspan="6" class="text-end">Grand Total</th>
    <th>
        <input id="grandTotal" name="grand_total" class="form-control fw-bold" readonly>
    </th>
</tr>
</tfoot>
</table>

<!-- <input id="grandTotalWords" class="form-control fw-bold mb-3" readonly> -->
<input id="grandTotalWords" name="grandTotalWords" class="form-control fw-bold mb-3" readonly>
<button type="button" id="addRowBtn" class="btn btn-primary">Add Item</button>

 <!-- TERMS -->
        <div class="border-box mt-4">
            <strong>Terms & Conditions:</strong>

            <table class="table table-bordered mt-2" id="termsTable">
                <tbody>
                <tr>
                    <td>
                        <input type="text" name="terms[]" class="form-control form-control-sm" placeholder="Enter term & condition">
                    </td>
                    <td class="text-center" style="width:80px;">
                        <button type="button" class="btn btn-danger btn-sm remove-term">X</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-success btn-sm" id="addTermBtn">+ Add New Row</button>
        </div>

        <!-- SIGNATURE -->
        <div class="mt-4 text-end">
            <label class="small fw-bold">Authorised Person Name</label>
            <input type="text" name="authorised_name" class="form-control form-control-sm d-inline-block"
                   style="width:250px;" placeholder="Enter Name">

            <br><br>

            <strong>For:</strong>
            <input type="text" class="form-control form-control-sm d-inline-block"
                   name="forpo" style="width:250px;" placeholder="Company / Firm Name">

            <br><br><br>
            <strong>Authorised Signatory</strong>
        </div>

<button type="submit" class="btn btn-success w-100 mt-3">SAVE PURCHASE ORDER</button>

</form>
</div>

<script>
let itemIndex = 1;
function updateSerialNumbers() {
    document.querySelectorAll('#itemBody tr').forEach((row, index) => {
        row.querySelector('.sr').innerText = index + 1;
    });
}
/* DATE */
document.getElementById('todayDate').innerText =
new Date().toLocaleDateString();

/* COMPANY DATA */
document.getElementById('companySelect').addEventListener('change', e => {
    let o = e.target.selectedOptions[0];
    ['name','address','phone','email','gstin'].forEach(k=>{
        document.getElementById('c_'+k).innerText = o.dataset[k] || '';
    });
});

/* CALC ROW */
function calcRow(row){
    let q = row.querySelector('.qty').value || 0;
    let r = row.querySelector('.rate').value || 0;
    let ig = row.querySelector('.igst_percent')?.value || 0;

    let amt = q * r;
    let igAmt = amt * ig / 100;

    row.querySelector('.amount').value = amt.toFixed(2);
    if(row.querySelector('.igst_amount')){
        row.querySelector('.igst_amount').value = igAmt.toFixed(2);
    }
    calcTotal();
}

/* TOTAL */
function calcTotal(){
    let sub = 0, igst = 0;
    document.querySelectorAll('#itemBody tr').forEach(r=>{
        sub += +r.querySelector('.amount').value || 0;
        igst += +r.querySelector('.igst_amount')?.value || 0;
    });

    document.getElementById('subtotal').value = sub.toFixed(2);

    let grand = document.getElementById('gstType').value === 'igst'
        ? sub + igst
        : sub * 1.18;

    document.getElementById('cgstAmount').value = (sub*0.09).toFixed(2);
    document.getElementById('sgstAmount').value = (sub*0.09).toFixed(2);
    document.getElementById('grandTotal').value = grand.toFixed(2);
    document.getElementById('grandTotalWords').value =
        grand.toFixed(2) + ' Rupees Only';
}

/* EVENTS */
document.addEventListener('input', e=>{
    if(e.target.closest('#itemBody tr')){
        calcRow(e.target.closest('tr'));
    }
});



document.getElementById('addRowBtn').addEventListener('click', () => {
    let tbody = document.getElementById('itemBody');
    let tr = tbody.querySelector('tr').cloneNode(true);

    // Clear values
    tr.querySelectorAll('input, select').forEach(el => el.value = '');

    // Update input names with new index
    tr.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/\[\d+]/, `[${itemIndex}]`);
    });

    tbody.appendChild(tr);
    itemIndex++;

    updateSerialNumbers();   // ✅ IMPORTANT
});


/* REMOVE */
document.addEventListener('click', e=>{
    if(e.target.classList.contains('remove-item')){
        e.target.closest('tr').remove();
        calcTotal();
    }
});

/* GST TOGGLE */
document.getElementById('gstType').onchange = e=>{
    document.querySelectorAll('.igstCol').forEach(c=>{
        c.classList.toggle('d-none', e.target.value!=='igst');
    });
    document.querySelectorAll('.cgstRow').forEach(c=>{
        c.classList.toggle('d-none', e.target.value==='igst');
    });
    calcTotal();
};

calcTotal();



 document.getElementById('addTermBtn').addEventListener('click', function () {
        let tbody = document.querySelector('#termsTable tbody');
        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="terms[]" class="form-control form-control-sm" placeholder="Enter term & condition"></td>
            <td class="text-center" style="width:80px;"><button type="button" class="btn btn-danger btn-sm remove-term">X</button></td>
        `;
        tbody.appendChild(tr);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-term')) {
            e.target.closest('tr').remove();
        }
    });

</script>

@endsection
