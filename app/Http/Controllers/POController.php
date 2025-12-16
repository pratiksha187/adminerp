<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderTerm;
use Barryvdh\DomPDF\Facade\Pdf;

class POController extends Controller
{


    public function showpo()
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;

        // Load Purchase Orders with Items and Terms
        $purchaseOrders = PurchaseOrder::with(['items', 'terms'])
                            ->orderBy('id', 'DESC')
                            ->get();
// dd( $purchaseOrders);
        return view('PO.index', compact('purchaseOrders','role'));
    }


    public function createpo(){

        $companies = DB::table('companies')->get();
        $latestPO = PurchaseOrder::orderBy('id', 'DESC')->first();
        // $lastNumber = 1;

        // if ($latestPO) {
        //     $parts = explode('/', $latestPO->po_no);
        
        //     $lastNumber = intval(end($parts)) + 1;
            
        // }

        // $nextNumber = str_pad($lastNumber, 2, '0', STR_PAD_LEFT);

        // Calculate financial year
        $currentYear = date('Y');
        $nextYear = date('y') + 1;
        $financialYear = $currentYear . '-' . $nextYear;

        // Final PO
        // $po_no = "SC/$financialYear/$nextNumber";
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        return view('PO.createpo',compact('role','companies'));
    }

   
public function storepo(Request $request)
{
    // 1️⃣ Save Purchase Order
    $po = PurchaseOrder::create([
        'company_id'        => $request->company_id,
        'ref_no'            => $request->ref_no,
        'po_no'             => $request->po_no,
        'po_date'           => $request->po_date,
        'supplier_ref'      => $request->supplier_ref,
        'dispatch_through'  => $request->dispatch_through,
        'destination'       => $request->destination,
        'forpo'             => $request->forpo,
        'gst_type'          => $request->gst_type,
        'sgst_percent'      => $request->sgst_percent,
        'cgst_percent'      => $request->cgst_percent,
        'subtotal'          => $request->subtotal,
        'cgst_amount'       => $request->cgst_amount,
        'sgst_amount'       => $request->sgst_amount,
        'grand_total'       => $request->grand_total,
        'grandTotalWords'   => $request->grandTotalWords,
        'authorised_name'   => $request->authorised_name,

        // Consignee
        'consignee_name'    => $request->consignee_name,
        'consignee_address' => $request->consignee_address,
        'consignee_phone'   => $request->consignee_phone,
        'consignee_email'   => $request->consignee_email,
        'consignee_gstin'   => $request->consignee_gstin,

        // Buyer
        'buyer_name'        => $request->buyer_name,
        'buyer_address'     => $request->buyer_address,
        'buyer_phone'       => $request->buyer_phone,
        'buyer_email'       => $request->buyer_email,
        'buyer_gstin'       => $request->buyer_gstin,
    ]);

    // 2️⃣ Save Items
    foreach ($request->items ?? [] as $item) {
        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'description'       => $item['description'],
            'hsn'               => $item['hsn'],
            'qty'               => $item['qty'],
            'unit'              => $item['unit'],
            'rate'              => $item['rate'],
            'amount'            => $item['amount'],
            'igst_percent'      => $item['igst_percent'] ?? 0,
            'igst_amount'       => $item['igst_amount'] ?? 0,
        ]);
    }

    // 3️⃣ Save Terms
    foreach ($request->terms ?? [] as $term) {
        PurchaseOrderTerm::create([
            'purchase_order_id' => $po->id,
            'term' => $term,
        ]);
    }

    // 4️⃣ Load relations
    $po = PurchaseOrder::with(['items', 'terms'])->find($po->id);

    // 5️⃣ Company details
    $company = DB::table('companies')
        ->where('id', $request->company_id)
        ->first();

    // 6️⃣ Generate PDF
    $pdf = Pdf::loadView('PO.pdf', compact('po', 'company'));

    if (!file_exists(public_path('po_pdfs'))) {
        mkdir(public_path('po_pdfs'), 0777, true);
    }

    $pdfFile = 'PO_' . $po->id . '.pdf';
    $pdf->save(public_path('po_pdfs/' . $pdfFile));

    return redirect()
        ->route('showpo')
        ->with('success', 'Purchase Order Saved Successfully!')
        ->with('pdf', asset('po_pdfs/' . $pdfFile));
}

//     public function storepo(Request $request)
// {
    
//       $company_id= $request->company_id;
    
//         $po = PurchaseOrder::create([
//         'company_id'        => $company_id,
//         'ref_no'            => $request->ref_no,
//         'po_no'             => $request->po_no,
//         'po_date'           => $request->po_date,
//         'supplier_ref'      => $request->supplier_ref,
//         'dispatch_through'  => $request->dispatch_through,
//         'destination'       => $request->destination,
//         'forpo'             => $request->forpo,
//         'sgst_percent'      =>$request->sgst_percent,
//         'cgst_percent'      =>$request->cgst_percent,

//         // Consignee
//         'consignee_name'    => $request->consignee_name,
//         'consignee_address' => $request->consignee_address,
//         'consignee_phone'   => $request->consignee_phone,
//         'consignee_email'   => $request->consignee_email,
//         'consignee_gstin'   => $request->consignee_gstin,

//         // Buyer
//         'buyer_name'        => $request->buyer_name,
//         'buyer_address'     => $request->buyer_address,
//         'buyer_phone'       => $request->buyer_phone,
//         'buyer_email'       => $request->buyer_email,
//         'buyer_gstin'       => $request->buyer_gstin,

//         // Totals
//         'subtotal'          => $request->subtotal,
//         'gst_type'          => $request->gst_type,
//         'cgst_amount'       => $request->cgst_amount,
//         'sgst_amount'       => $request->sgst_amount,
//         'grand_total'       => $request->grand_total,
//         'grandTotalWords' => $request->grandTotalWords,

//         'authorised_name'   => $request->authorised_name,
//     ]);

//     // 3️⃣ Save Items
//     foreach ($request->items ?? [] as $item) {
//         PurchaseOrderItem::create([
//             'purchase_order_id' => $po->id,
//             'description'       => $item['description'],
//             'hsn'               => $item['hsn'],
//             'qty'               => $item['qty'],
//             'unit'              => $item['unit'],
//             'rate'              => $item['rate'],
//             'amount'            => $item['amount'],
//             'igst_percent'      => $item['igst_percent'] ?? 0,
//             'igst_amount'       => $item['igst_amount'] ?? 0,
//         ]);
//     }

//     // 4️⃣ Save Terms
//     foreach ($request->terms ?? [] as $term) {
//         PurchaseOrderTerm::create([
//             'purchase_order_id' => $po->id,
//             'term' => $term,
//         ]);
//     }

   
//             $company = DB::table('companies')
//                 ->where('id', $company_id)
//                 ->first();

//             $pdf = Pdf::loadView('PO.pdf', [
//                 'po' => $request->po_no,
//                 'company' => $company
//             ]);


//     if (!file_exists(public_path('po_pdfs'))) {
//         mkdir(public_path('po_pdfs'), 0777, true);
//     }

//     $pdfFile = 'PO_' . $po->id . '.pdf';
//     $pdf->save(public_path('po_pdfs/' . $pdfFile));

//     return redirect()
//         ->route('showpo')
//         ->with('success', 'Purchase Order Saved Successfully!')
//         ->with('pdf', asset('po_pdfs/' . $pdfFile));
// }

public function destroy($id)
{
    $po = PurchaseOrder::findOrFail($id);

    // Optional: delete PDF file
    $pdfPath = public_path('po_pdfs/PO_'.$po->id.'.pdf');
    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }

    // Optional: delete related items & terms
    $po->items()->delete();
    $po->terms()->delete();

    $po->delete();

    return redirect()->back()->with('success', 'Purchase Order deleted successfully.');
}

}
