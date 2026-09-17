<!doctype html>
<html>
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans}h2{text-align:center}table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:8px;text-align:left}</style></head>
<body>
<h2>Customer Debt Report</h2>
<table><thead><tr><th>Customer</th><th>Phone</th><th>Address</th><th>Debt</th></tr></thead><tbody>
@forelse($customers as $customer)
<tr><td>{{ $customer->name }}</td><td>{{ $customer->phone ?: '-' }}</td><td>{{ $customer->address ?: '-' }}</td><td>${{ number_format($customer->debt, 2) }}</td></tr>
@empty
<tr><td colspan="4">No outstanding debts.</td></tr>
@endforelse
</tbody></table>
</body>
</html>
