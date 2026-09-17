<!doctype html>
<html>
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans}.labels{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.label{border:1px solid #000;padding:12px;text-align:center;min-height:70px}.barcode{font-family:monospace;font-size:18px;letter-spacing:2px}</style></head>
<body>
<h2>Product Price Labels</h2>
<div class="labels">
@foreach($products as $product)
<div class="label"><strong>{{ $product->name }}</strong><br><span>${{ number_format($product->price, 2) }}</span>@if($product->barcode)<br><span class="barcode">{{ $product->barcode }}</span>@endif</div>
@endforeach
</div>
</body>
</html>
