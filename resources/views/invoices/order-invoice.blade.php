<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            padding: 40px;
        }
        .header {
            margin-bottom: 40px;
            border-bottom: 2px solid #7367f0;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #7367f0;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-align: right;
            color: #333;
        }
        .invoice-details {
            text-align: right;
            margin-top: 10px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #7367f0;
            text-transform: uppercase;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #7367f0;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }
        .summary-table td {
            border: none;
            padding: 5px 0;
        }
        .summary-table .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #28c76f;
            color: white;
        }
        .status-pending {
            background-color: #ff9f43;
            color: white;
        }
        .delivered-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            background-color: #28c76f;
            color: white;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; width: 50%;">
                        <div class="company-name">Monsieur WiFi</div>
                        <div style="color: #666; font-size: 11px;">
                            Professional WiFi Solutions<br>
                            contact@monsieurwifi.com<br>
                            www.monsieurwifi.com
                        </div>
                    </td>
                    <td style="border: none; width: 50%; vertical-align: top;">
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-details">
                            <strong>{{ $order->order_number }}</strong><br>
                            Date: {{ $order->created_at->format('F d, Y') }}<br>
                            @if($order->payment_status === 'succeeded')
                                <span class="status-badge status-paid">PAID</span>
                            @else
                                <span class="status-badge status-pending">PENDING</span>
                            @endif
                            @if($order->delivered_at)
                                <span class="delivered-badge">DELIVERED</span>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Bill To Section -->
        <div class="section">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; width: 50%; vertical-align: top;">
                        <div class="section-title">Bill To</div>
                        <strong>{{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}</strong><br>
                        @if($order->billingAddress->company)
                            {{ $order->billingAddress->company }}<br>
                        @endif
                        {{ $order->billingAddress->address_line1 }}<br>
                        @if($order->billingAddress->address_line2)
                            {{ $order->billingAddress->address_line2 }}<br>
                        @endif
                        {{ $order->billingAddress->city }}, {{ $order->billingAddress->province }} {{ $order->billingAddress->postal_code }}<br>
                        {{ $order->billingAddress->country }}<br>
                        Phone: {{ $order->billingAddress->phone }}
                    </td>
                    <td style="border: none; width: 50%; vertical-align: top;">
                        <div class="section-title">Ship To</div>
                        <strong>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</strong><br>
                        @if($order->shippingAddress->company)
                            {{ $order->shippingAddress->company }}<br>
                        @endif
                        {{ $order->shippingAddress->address_line1 }}<br>
                        @if($order->shippingAddress->address_line2)
                            {{ $order->shippingAddress->address_line2 }}<br>
                        @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->province }} {{ $order->shippingAddress->postal_code }}<br>
                        {{ $order->shippingAddress->country }}<br>
                        Phone: {{ $order->shippingAddress->phone }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Order Details -->
        @if($order->delivered_at)
        <div class="section">
            <div class="info-row">
                <span class="info-label">Delivered On:</span>
                <span style="color: #28c76f; font-weight: bold;">{{ $order->delivered_at->format('F d, Y g:i A') }}</span>
            </div>
        </div>
        @endif

        @if($order->tracking_id)
        <div class="section">
            <div class="info-row">
                <span class="info-label">Shipping Provider:</span>
                {{ $order->shipping_provider }}
            </div>
            <div class="info-row">
                <span class="info-label">Tracking ID:</span>
                <strong>{{ $order->tracking_id }}</strong>
            </div>
        </div>
        @endif

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Product</th>
                    <th class="text-center" style="width: 15%;">Quantity</th>
                    <th class="text-right" style="width: 17.5%;">Unit Price</th>
                    <th class="text-right" style="width: 17.5%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->productModel->name }}</strong>
                        @if($item->productModel->description)
                            <br><small style="color: #666;">{{ Str::limit($item->productModel->description, 80) }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">${{ number_format($order->product_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Shipping ({{ $order->shipping_method }}):</td>
                <td class="text-right">${{ number_format($order->shipping_cost, 2) }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-right">${{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right">-${{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total:</td>
                <td class="text-right">${{ number_format($order->total, 2) }}</td>
            </tr>
        </table>

        <!-- Payment Info -->
        @if($order->payment_status === 'succeeded')
        <div class="section" style="margin-top: 30px;">
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                {{ ucfirst($order->payment_method) }}
            </div>
            <div class="info-row">
                <span class="info-label">Payment Date:</span>
                {{ $order->payment_received_at ? $order->payment_received_at->format('F d, Y g:i A') : 'N/A' }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Thank you for your business!<br>
            This is an automatically generated invoice. For questions, contact us at contact@monsieurwifi.com
        </div>
    </div>
</body>
</html>
