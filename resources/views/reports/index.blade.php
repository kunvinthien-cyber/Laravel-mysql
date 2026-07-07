@extends('layouts.admin')

@section('content')

<div class="p-6 bg-white shadow rounded-xl">

    <h2 class="mb-6 text-2xl font-bold">
        Sales Report
    </h2>


    <div class="mb-5 text-xl font-bold">

        Total Sales :
        ${{ number_format($totalSales,2) }}

    </div>
<div class="flex gap-3 mb-5">

    <a
        href="{{ route('reports.pdf') }}"
        class="px-5 py-2 text-white bg-red-600 rounded-lg">

        Export PDF

    </a>

  <a href="{{ route('reports.export.excel') }}"
   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">

    📊 Export Excel

</a>

</div>
<form method="GET" class="flex gap-3 mb-6">

    <input
        type="date"
        name="from"
        value="{{ request('from') }}"
        class="border rounded-lg px-3 py-2">

    <input
        type="date"
        name="to"
        value="{{ request('to') }}"
        class="border rounded-lg px-3 py-2">

    <button
        class="bg-blue-600 text-white px-5 rounded-lg">

        Filter

    </button>

    <a href="{{ route('reports.index') }}"
       class="bg-gray-500 text-white px-5 py-2 rounded-lg">

        Reset

    </a>

</form>
    <table class="w-full border">

        <thead class="bg-gray-100">
        <tr>

            <th class="p-3">Order</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>

        </tr>

        </thead>

        <tbody>

        @foreach($orders as $order)

            <tr class="border-t">

                <td class="p-3">
                    #{{ $order->id }}
                </td>

                <td>
                    ${{ number_format($order->total,2) }}
                </td>

                <td>
                    {{ ucfirst($order->status) }}
                </td>

                <td>
                    {{ $order->created_at->format('d M Y') }}
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    <div class="mt-6">

        {{ $orders->links() }}

    </div>

</div>

@endsection
