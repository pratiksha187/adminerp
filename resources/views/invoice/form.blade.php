@extends('layouts.app')

@section('content')

<style>
    .invoice-container {
        max-width: 900px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .form-section {
        margin-bottom: 25px;
    }

    .form-section h4 {
        margin-bottom: 15px;
        border-bottom: 2px solid #eee;
        padding-bottom: 5px;
        font-weight: 600;
    }

    .form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }

    .form-group {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 13px;
        margin-bottom: 4px;
    }

    .form-group input,
    .form-group textarea {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    textarea {
        resize: none;
        height: 60px;
    }

    .btn-submit {
        background: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
    }

    .btn-submit:hover {
        background: #0056b3;
    }
</style>

<div class="invoice-container">

    <h2>Create Tax Invoice</h2>

    <form method="POST" action="/invoice/generate">
        @csrf

        <!-- 🔹 Invoice -->
        <div class="form-section">
            <h4>Invoice Details</h4>

            <div class="form-row">
                <div class="form-group">
                    <label>Invoice No</label>
                    <input type="text" name="invoice_no" required>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" required>
                </div>
            </div>
        </div>

        <!-- 🔹 Vendor -->
        <div class="form-section">
            <h4>Vendor Details</h4>

            <div class="form-row">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="vendor_name">
                </div>

                <div class="form-group">
                    <label>GSTIN</label>
                    <input type="text" name="vendor_gstin">
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="vendor_address"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="vendor_phone">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="vendor_email">
                </div>
            </div>
        </div>

        <!-- 🔹 Bill To -->
        <div class="form-section">
            <h4>Bill To</h4>

            <div class="form-row">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="bill_name">
                </div>

                <div class="form-group">
                    <label>GSTIN</label>
                    <input type="text" name="bill_gstin">
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="bill_address"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="bill_phone">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="bill_email">
                </div>
            </div>
        </div>

        <!-- 🔹 Item -->
        <div class="form-section">
            <h4>Item Details</h4>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <input type="number" name="amount" required>
            </div>
        </div>

        <button type="submit" class="btn-submit">Generate Invoice</button>

    </form>

</div>

@endsection