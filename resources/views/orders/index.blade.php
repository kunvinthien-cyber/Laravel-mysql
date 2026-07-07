@extends('layouts.admin')

@section('content')
@push('scripts')

@if(session('success'))

<script>

Swal.fire({

    icon:'success',

    title:'Success',

    text:'{{ session("success") }}',

    timer:2000,

    showConfirmButton:false

});

</script>

@endif

@endpush
<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-2xl font-bold">
            Orders
        </h1>

    </div>
<div class="flex justify-between items-center mb-6">

    <form method="GET" class="flex gap-3">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search Order ID..."
            class="border rounded-lg px-4 py-2">

        <select
            name="status"
            class="border rounded-lg px-4 py-2">

            <option value="">All Status</option>

            <option value="pending"
                {{ request('status')=='pending'?'selected':'' }}>
                Pending
            </option>

            <option value="completed"
                {{ request('status')=='completed'?'selected':'' }}>
                Completed
            </option>

            <option value="cancelled"
                {{ request('status')=='cancelled'?'selected':'' }}>
                Cancelled
            </option>

        </select>

        <button
            class="bg-blue-600 text-white px-5 py-2 rounded-lg">

            Search

        </button>

    </form>

    <a href="{{ route('orders.create') }}"
       class="bg-black text-white px-5 py-2 rounded-lg">

        + Add Order

    </a>
<a href="{{ route('orders.export.excel') }}"
    class="px-4 py-2 bg-green-600 text-white rounded-lg">
    📊 Export Excel
</a>
</div>
<p class="text-gray-500 mb-4">

    Total Orders :
    <strong>{{ $orders->total() }}</strong>

</p>
    <table class="w-full">

        <thead>

            <tr class="bg-gray-100">

                <th class="p-3">ID</th>
                <th class="p-3">Total</th>
                <th class="p-3">Status</th>
                <th class="p-3">Date</th>
                <th>Product</th>
<th>Customer</th>
<th>Action</th>
            </tr>

        </thead>

        <tbody>

        @foreach($orders as $order)

            <tr class="border-b">

                <td class="p-3">
                    #{{ $order->id }}
                </td>

                <td class="p-3">
                    ${{ number_format($order->total,2) }}
                </td>

                <td class="p-3">

                    @if($order->status == 'completed')

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                            Completed
                        </span>

                    @elseif($order->status == 'pending')

                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">
                            Pending
                        </span>

                    @else

                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                            Cancelled
                        </span>

                    @endif

                </td>

                <td class="p-3">
                    {{ $order->created_at->format('d M Y') }}
                </td>
                <td>{{ $item->product?->name ?? '-' }}</td>

<td>{{ $order->customer?->name ?? '-' }}</td>

<td class="space-x-3">

    <a href="{{ route('orders.edit',$order) }}"
       class="bg-blue-500 text-red-400 px-3 py-1 rounded">
        Edit
    </a>

  <form action="{{ route('orders.destroy',$order) }}"
      method="POST"
      class="delete-form inline">

    @csrf
    @method('DELETE')

    <button
        type="button"
        class="delete-btn bg-red-500 hover:bg-red-600 text-red-400 px-3 py-1 rounded">

        Delete

    </button>

</form>
<a href="{{ route('orders.invoice',$order) }}"
    class="bg-green-600 hover:bg-green-700 text-red-400 px-3 py-1 rounded">

    Invoice

</a>
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
@push('scripts')

<script>

document.querySelectorAll('.delete-btn').forEach(button => {

    button.addEventListener('click', function () {

        const form = this.closest('.delete-form');

        Swal.fire({

            title: 'Delete Order?',

            text: "You won't be able to recover this order.",

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc2626',

            cancelButtonColor: '#6b7280',

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                form.submit();

            }

        });

    });

});

</script>

@endpush
