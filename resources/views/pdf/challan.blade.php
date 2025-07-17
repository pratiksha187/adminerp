<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <title>Delivery Challan</title>
      <style>
         /* same styles as you already shared, keeping as-is for brevity */
         body {
         font-family: Arial, sans-serif;
         margin: 0;
         padding: 20px;
         }
         .challan-box {
         width: 100%;
         max-width: 600px;
         border: 2px solid #000;
         padding: 20px;
         margin: auto;
         }
         .header {
         text-align: center;
         margin-bottom: 15px;
         border-bottom: 1px solid #eee;
         padding-bottom: 10px;
         }
         .header h2 {
         margin: 0;
         font-size: 24px;
         text-transform: uppercase;
         }
         .header .company-name {
         font-size: 26px;
         font-weight: bold;
         margin: 4px 0;
         }
         .header .details {
         font-size: 13px;
         line-height: 1.4;
         }
         .info-table {
         width: 100%;
         margin-top: 15px;
         font-size: 14px;
         border-collapse: collapse;
         }
         .info-table td {
         vertical-align: top;
         padding: 8px 4px;
         }
         .label {
         width: 140px;
         font-weight: bold;
         }
         .colon {
         width: 10px;
         text-align: center;
         }
         .value {
         border-bottom: 1px solid #000;
         width: 100%;
         min-height: 18px;
         box-sizing: border-box;
         padding-bottom: 2px;
         }
         .section-title {
         font-size: 16px;
         font-weight: bold;
         margin-top: 20px;
         margin-bottom: 10px;
         border-bottom: 1px solid #000;
         padding-bottom: 5px;
         }
         .item-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 15px;
         font-size: 13px;
         }
         .item-table th, .item-table td {
         border: 1px solid #000;
         padding: 8px;
         text-align: left;
         }
         .item-table th {
         background-color: #f2f2f2;
         text-align: center;
         }
         .item-table .qty, .item-table .rate, .item-table .amount {
         text-align: center;
         width: 80px;
         }
         .item-table .description {
         width: auto;
         }
         .total-row td {
         font-weight: bold;
         }
         .total-amount {
         text-align: right;
         }
         .notes {
         margin-top: 20px;
         font-size: 13px;
         }
         .footer {
         margin-top: 40px;
         display: flex;
         justify-content: space-between;
         flex-wrap: wrap;
         }
         /* .signature {
         text-align: center;
         width: 48%;
         margin-top: 20px;
         } */
         /* .signature-line {
         border-top: 1px solid #000;
         margin-top: 40px;
         font-size: 12px;
         padding-top: 4px;
         } */
      </style>
   </head>
   <body>
      <div class="challan-box">
         <div class="header">
            <h2>Delivery Challan</h2>
            <div class="company-name">Shreeyash Construction</div>
            <div class="details">
               Khopoli, Tal- Khalapur, Dist - Raigad<br>
               Contact No. 9923299301 / 9326216153
            </div>
         </div>
         <!-- Challan Details Section -->
         <table class="info-table">
            <tr>
               <td class="label">Challan No.</td>
               <td class="colon">:</td>
               <td class="value">{{ $challan->challan_no }}</td>
               <td class="label" style="width: 100px;">Date</td>
               <td class="colon">:</td>
               <td class="value" style="width: 120px;">{{ \Carbon\Carbon::parse($challan->date)->format('d/m/Y') }}</td>
            </tr>
         </table>
         <!-- Consignee Details Section -->
         <div class="section-title">Consignee Details</div>
         <table class="info-table">
            <tr>
               <td class="label">Name Of Party</td>
               <td class="colon">:</td>
               <td class="value">{{ $challan->party_name }}</td>
            </tr>
            <tr>
               <td class="label">Location</td>
               <td class="colon">:</td>
               <td class="value">{{ $challan->location }}</td>
            </tr>
            <tr>
               <td class="label">Vehicle No</td>
               <td class="colon">:</td>
               <td class="value">{{ $challan->vehicle_no }}</td>
            </tr>
            <tr>
               <td class="label">Driver Name</td>
               <td class="colon">:</td>
               <td class="value">{{ $challan->driver_name }}</td>
            </tr>
            <tr>
               <td class="label">Time</td>
               <td class="colon">:</td>
               <td class="value">{{ $challan->time }}</td>
            </tr>
         </table>
         <div class="section-title">Material / M/C</div>
         <table class="item-table">
            <thead>
               <tr>
                  <th>Sr. No.</th>
                  <th class="description">Description of Goods</th>
                  <th class="qty">Qty</th>
               </tr>
            </thead>
            <tbody>
               @php
               $materials = explode(',', $challan->material);
               $quantitys = explode(',', $challan->quantity);
               @endphp
               @foreach($materials as $index => $material)
               <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ trim($material) }}</td>
                  <td>{{ isset($quantitys[$index]) ? trim($quantitys[$index]) : '—' }}</td>
               
               </tr>
               @endforeach
            </tbody>
         </table>
         <p><strong>Remark:</strong> {{ $challan->remark }}</p>
         <div class="footer">
            <div class="signature">
               <div class="signature-line">{{ $challan->party_name ?? "Receiver's Signature" }}</div>
            </div>
            <div class="signature">
               <div class="signature-line">For Shreeyash Construction</div>
            </div>
         </div>
      </div>
   </body>
</html>