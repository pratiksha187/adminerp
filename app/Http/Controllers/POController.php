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

    return view('PO.index', compact('purchaseOrders','role'));
}


    public function createpo(){

            $latestPO = PurchaseOrder::orderBy('id', 'DESC')->first();
    $lastNumber = 1;

    if ($latestPO) {
        $parts = explode('/', $latestPO->po_no);
    
        $lastNumber = intval(end($parts)) + 1;
        
    }

    $nextNumber = str_pad($lastNumber, 2, '0', STR_PAD_LEFT);

    // Calculate financial year
    $currentYear = date('Y');
    $nextYear = date('y') + 1;
    $financialYear = $currentYear . '-' . $nextYear;

    // Final PO
    $po_no = "SC/$financialYear/$nextNumber";
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        return view('PO.createpo',compact('role','po_no'));
    }

    public function storepo(Request $request)
    {
    $latestPO = PurchaseOrder::orderBy('id', 'DESC')->first();
    $lastNumber = 1;

    if ($latestPO) {
        $parts = explode('/', $latestPO->po_no);
    
        $lastNumber = intval(end($parts)) + 1;
        
    }

    $nextNumber = str_pad($lastNumber, 2, '0', STR_PAD_LEFT);

    // Calculate financial year
    $currentYear = date('Y');
    $nextYear = date('y') + 1;
    $financialYear = $currentYear . '-' . $nextYear;

    // Final PO
    $po_no = "SC/$financialYear/$nextNumber";
//  dd( $po_no);
// dd($finalPONumber);
        // dd($po_no );
        $po = PurchaseOrder::create([
            'ref_no' => $po_no,
            'po_no' => $po_no,
            'po_date' => $request->po_date,
            'supplier_ref' => $request->supplier_ref,
            'dispatch_through' => $request->dispatch_through,
            'destination' => $request->destination,
            'forpo' =>$request->forpo,

            // CONSIGNEE
            'consignee_name' => $request->consignee_name,
            'consignee_address' => $request->consignee_address,
            'consignee_phone' => $request->consignee_phone,
            'consignee_email' => $request->consignee_email,
            'consignee_gstin' => $request->consignee_gstin,

            // BUYER
            'buyer_name' => $request->buyer_name,
            'buyer_address' => $request->buyer_address,
            'buyer_phone' => $request->buyer_phone,
            'buyer_email' => $request->buyer_email,
            'buyer_gstin' => $request->buyer_gstin,

            // TOTALS
            'subtotal' => $request->subtotal,
            'cgst_percent' => $request->cgstPercent,
            'cgst_amount' => $request->cgstAmount,
            'sgst_percent' => $request->sgstPercent,
            'sgst_amount' => $request->sgstAmount,
            'grand_total' => $request->grandTotal,
            'grandTotalWords' =>$request->grandTotalWords
        ]);

        // Save Items
        if ($request->items) {
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'description' => $item['description'],
                    'hsn' => $item['hsn'],
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount']
                ]);
            }
        }

        // Save Terms
        if ($request->terms) {
            foreach ($request->terms as $term) {
                PurchaseOrderTerm::create([
                    'purchase_order_id' => $po->id,
                    'term' => $term
                ]);
            }
        }

        // 4. Generate PDF
        $pdf = Pdf::loadView('PO.pdf', [
            'po' => $po->load('items')
        ]);

        // Create folder if not exists
        if (!file_exists(public_path('po_pdfs'))) {
            mkdir(public_path('po_pdfs'), 0777, true);
        }

        // PDF file name
        $pdfFile = 'PO_' . $po->id . '.pdf';

        // Save PDF in public folder
        $pdf->save(public_path('po_pdfs/' . $pdfFile));

        return redirect()->route('showpo') // assuming your route name is 'po.show'
                 ->with('success', 'Purchase Order Saved Successfully!')
                 ->with('pdf', asset('po_pdfs/' . $pdfFile));

        // return back()->with('success', 'Purchase Order Saved Successfully!')->with('pdf', asset('po_pdfs/' . $pdfFile));
    }

}
