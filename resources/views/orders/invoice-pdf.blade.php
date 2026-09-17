<!doctype html>
<html>
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans;font-size:12px}h1,h2{text-align:center}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #000;padding:7px;text-align:left}.total{text-align:right;font-size:16px;font-weight:bold;margin-top:20px}</style></head>
<body>
<h1>{{ $shopSettings['shop_name'] ?? 'POS System' }}</h1>
<p style="text-align:center">Invoice #{{ $order->id }} | {{ $order->created_at?->format('d M Y H:i') }}</p>
<p>Customer: {{ $order->customer?->name ?: 'Walk-in customer' }}</p>
<table><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>
@foreach($order->items as $item)
<tr><td>{{ $item->product?->name ?: '-' }}</td><td>{{ $item->quantity }}</td><td>${{ number_format($item->price, 2) }}</td><td>${{ number_format($item->subtotal, 2) }}</td></tr>
@endforeach
</tbody></table>
<p class="total">Total: ${{ number_format($order->total, 2) }}</p>
</body>
</html>
