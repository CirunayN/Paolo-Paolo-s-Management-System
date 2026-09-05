<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->invoice_no }} | Paolo Paolo D.A Matting &amp; Accessories</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f3f4f6;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .receipt-container {
            width: 80mm;
            background: #ffffff;
            padding: 16px 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            color: #000000;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .logo-text {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .sub-text {
            font-size: 11px;
            margin-top: 2px;
            font-weight: 600;
        }
        .contact-info {
            font-size: 9px;
            margin-top: 4px;
            color: #333;
        }
        .meta-info {
            font-size: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 8px;
        }
        .items-table th {
            border-bottom: 1px solid #000;
            padding: 4px 0;
            text-align: left;
        }
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .totals-section {
            border-top: 1px dashed #000;
            padding-top: 6px;
            font-size: 10px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .row {
            display: flex;
            justify-content: space-between;
        }
        .grand-total {
            font-size: 12px;
            font-weight: 900;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 4px 0;
        }
        .footer {
            text-align: center;
            font-size: 9px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            line-height: 1.4;
        }
        .print-btn-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
        }
        .btn {
            background: #0284c7;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .receipt-container {
                width: 100%;
                box-shadow: none;
                padding: 4px;
            }
            .print-btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <!-- Header -->
    <div class="header">
        <div class="logo-text">PAOLO PAOLO</div>
        <div class="sub-text">D.A MATTING &amp; ACCESSORIES</div>
        <div class="contact-info">
            Automotive Deep Dish, Diamond Mats &amp; Car Accessories<br>
            Davao City, Philippines<br>
            Facebook: Paolo Paolo | Contact: 0917-123-4567
        </div>
    </div>

    <!-- Meta Info -->
    <div class="meta-info">
        <div class="row">
            <span>INVOICE:</span>
            <strong>{{ $order->invoice_no }}</strong>
        </div>
        <div class="row">
            <span>DATE:</span>
            <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="row">
            <span>CASHIER:</span>
            <span>{{ $order->user ? $order->user->name : 'Staff' }}</span>
        </div>
        <div class="row">
            <span>CUSTOMER:</span>
            <span>{{ $order->customer_name ?: ($order->customer ? $order->customer->name : 'Walk-in') }}</span>
        </div>
        @if($order->vehicle_details)
        <div class="row">
            <span>VEHICLE:</span>
            <span>{{ $order->vehicle_details }}</span>
        </div>
        @endif
        <div class="row">
            <span>TYPE:</span>
            <span>{{ $order->order_type }}</span>
        </div>
    </div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">QTY</th>
                <th style="width: 55%;">ITEM</th>
                <th style="width: 30%; text-align: right;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ (float)$item->quantity }}x</td>
                <td>
                    {{ $item->product_name }}<br>
                    <small style="color: #444;">@ ₱{{ number_format($item->unit_price, 2) }}</small>
                </td>
                <td style="text-align: right;">₱{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <div class="row">
            <span>Subtotal:</span>
            <span>₱{{ number_format($order->subtotal, 2) }}</span>
        </div>
        @if($order->installation_fee > 0)
        <div class="row">
            <span>Installation Fee:</span>
            <span>₱{{ number_format($order->installation_fee, 2) }}</span>
        </div>
        @endif
        @if($order->discount_amount > 0)
        <div class="row">
            <span>Discount:</span>
            <span>-₱{{ number_format($order->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="row grand-total">
            <span>TOTAL AMOUNT:</span>
            <span>₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
        <div class="row">
            <span>Payment Method:</span>
            <span>{{ $order->payment_method }}</span>
        </div>
        @if(!empty($order->payment_reference))
        <div class="row">
            <span>Ref No / Trx ID:</span>
            <strong style="font-family: monospace;">{{ $order->payment_reference }}</strong>
        </div>
        @endif
        <div class="row">
            <span>Amount Tendered:</span>
            <span>₱{{ number_format($order->amount_tendered, 2) }}</span>
        </div>
        <div class="row">
            <span>Change:</span>
            <strong>₱{{ number_format($order->change_amount, 2) }}</strong>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing</p>
        <strong style="font-size: 11px; letter-spacing: 0.5px;">PAOLO PAOLO D.A MATTING &amp; ACCESSORIES</strong>
        
        <div style="margin: 6px 0; padding: 5px 0; border-top: 1px dotted #555; border-bottom: 1px dotted #555;">
            <p style="font-weight: 700; font-size: 10px;">Like &amp; Follow us on Facebook:</p>
            <p style="font-size: 11px; font-weight: 900; letter-spacing: 0.5px;">Paolo Paolo</p>
            <p style="font-size: 8px; color: #444;">facebook.com/Paolo Paolo</p>
        </div>

        <p style="margin-top: 4px;">Strictly 7 days replacement for factory defects with this receipt.</p>
        <p style="margin-top: 3px; font-size: 8px;">*** DRIVE SAFELY &amp; ENJOY YOUR RIDE! ***</p>
    </div>
</div>

<!-- Print Button for Browser View -->
<div class="print-btn-bar">
    <button onclick="window.print()" class="btn">
        Print Receipt (Ctrl+P)
    </button>
</div>

<script>
    // Auto-trigger print on page open if desired
    window.addEventListener('load', () => {
        // Optional auto-print
    });
</script>

</body>
</html>
