@extends('layouts.app')

@section('content')
<div class="text-center mb-6">
    <!-- បង្ហាញឈ្មោះហាងជាអក្សរធំ និងដិត -->
       <div class="text-xl font-bold text-gray-800">
    {{ $shopSettings['shop_name'] ?? 'POS System' }}
</div>

    <!-- បង្ហាញអាសយដ្ឋាន និងលេខទូរស័ព្ទ -->
    <p class="text-sm text-gray-600">
        អាសយដ្ឋាន៖ {{ $shopSettings['shop_address'] ?? 'មិនទាន់កំណត់' }}
    </p>
    <p class="text-sm text-gray-600">
        លេខទូរស័ព្ទ៖ {{ $shopSettings['shop_phone'] ?? 'N/A' }} | អុីមែល៖ {{ $shopSettings['shop_email'] ?? 'N/A' }}
    </p>
</div>
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">

    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-3xl font-bold">
                Invoice
            </h1>

            <p class="text-gray-500">
                Order #{{ $order->id }}
            </p>

        </div>

        <button
            id="printInvoiceBtn"
            class="bg-blue-600 text-white px-5 py-2 rounded-lg">

            🖨 Print

        </button>

    </div>

    <div class="grid grid-cols-2 gap-8 mb-8">

        <div>
       @if($order->customer)
    <!-- បង្ហាញព័ត៌មានពិតប្រាកដ ប្រសិនបើអតិថិជនមានគណនីក្នុងប្រព័ន្ធ -->
    <div class="text-sm text-gray-600">
        <p><strong>អតិថិជន៖</strong> {{ $order->customer->name }}</p>
        <p><strong>អុីមែល៖</strong> {{ $order->customer->email }}</p>
    </div>
@else
    <!-- បង្ហាញព័ត៌មានជំនួស ក្នុងករណីលក់ជូនភ្ញៀវទូទៅ (Guest Checkout) -->
    <div class="text-sm text-gray-600">
        <p><strong>អតិថិជន៖</strong> ភ្ញៀវទូទៅ (Guest)</p>
        <p><strong>អុីមែល៖</strong> N/A</p>
    </div>
@endif
 </div>

        <div class="text-right">

            <h3 class="font-bold">
                Status
            </h3>

            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">

                {{ ucfirst($order->status) }}

            </span>

        </div>

    </div>

    <table class="w-full border">

        <thead class="bg-gray-100">

            <tr>

                <th class="p-3 text-left">Product</th>

                <th class="p-3">Qty</th>

                <th class="p-3">Price</th>

                <th class="p-3">Subtotal</th>

            </tr>

        </thead>

        <tbody>

            @foreach($order->items as $item)

            <tr class="border-t">

                <td class="p-3">
                    {{ $item->product->name }}
                </td>

                <td class="text-center">
                    {{ $item->quantity }}
                </td>

                <td class="text-center">
                    ${{ number_format($item->price,2) }}
                </td>

                <td class="text-center">
                    ${{ number_format($item->subtotal,2) }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    <div class="text-right mt-8">

        <h2 class="text-2xl font-bold">

            Total :
            ${{ number_format($order->total,2) }}

        </h2>

    </div>

</div>

@push('scripts')
<script>
let shouldReturnToPos = false;

document.getElementById('printInvoiceBtn').addEventListener('click', function () {
    shouldReturnToPos = true;
    window.print();
});

window.addEventListener('afterprint', function () {
    if (shouldReturnToPos) {
        window.location.href = "{{ route('pos.index') }}";
    }
});
</script>
@endpush

@endsection
