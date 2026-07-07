<!DOCTYPE html>
<html>

<head>

    <style>

        body{
            font-family: DejaVu Sans;
        }

        table{

            width:100%;
            border-collapse:collapse;

        }

        th,td{

            border:1px solid #000;
            padding:8px;
            text-align:left;

        }

        h2{

            text-align:center;

        }

    </style>

</head>

<body>

<h2>Sales Report</h2>

<table>

<thead>

<tr>

<th>ID</th>
<th>Total</th>
<th>Status</th>
<th>Date</th>

</tr>

</thead>

<tbody>

@foreach($orders as $order)

<tr>

<td>{{ $order->id }}</td>

<td>${{ number_format($order->total,2) }}</td>

<td>{{ ucfirst($order->status) }}</td>

<td>{{ $order->created_at->format('d M Y') }}</td>

</tr>

@endforeach

</tbody>

</table>

</body>

</html>
