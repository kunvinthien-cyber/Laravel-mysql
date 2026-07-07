@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- STATISTICS --}}
    <div class="grid grid-cols-4 gap-6">

      @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
    <x-stat-card
        title="Total Revenue"
        value="${{ number_format($revenue, 2) }}"
        badge="+12%"
        color="blue"
        icon="💰"
    />
@endif

        <x-stat-card
            title="Total Orders"
            value="{{ $orders }}"
            badge="+8%"
            color="green"
            icon="📦"
        />

        <x-stat-card
            title="Out of Stock"
            value="{{ $outOfStock }} Items"
            badge="Urgent"
            color="red"
            icon="⚠️"
        />

        <x-stat-card
            title="Customers"
            value="{{ $customers }}"
            badge="+5%"
            color="yellow"
            icon="👥"
        />

        <x-stat-card
            title="Products"
            value="{{ $products }}"
            badge="Inventory"
            color="purple"
            icon="🛍️"
        />
    </div>

    {{-- CHART + ORDERS --}}
    <div class="grid grid-cols-4 gap-6">

        {{-- CHART --}}
        <div class="col-span-2 p-6 bg-white shadow-sm rounded-2xl">

            <div class="flex justify-between mb-6">

                <h2 class="text-lg font-bold">
                    Sales Analytics
                </h2>

                <div class="space-x-2">

                    <button class="px-3 py-1 text-sm text-white bg-black rounded-lg">
                        Weekly
                    </button>

                    <button class="px-3 py-1 text-sm bg-gray-100 rounded-lg">
                        Monthly
                    </button>

                    <button class="px-3 py-1 text-sm bg-gray-100 rounded-lg">
                        Yearly
                    </button>

                </div>

            </div>

            <canvas id="salesChart" height="120"></canvas>

        </div>

        <div class="p-6 bg-white shadow-sm rounded-2xl">

            <h2 class="mb-5 text-lg font-bold">
                Order Status
            </h2>

            <canvas id="statusChart"></canvas>

        </div>

        {{-- RECENT ORDERS --}}
        <div class="p-6 bg-white shadow-sm rounded-2xl">

            <div class="flex justify-between mb-5">

                <h2 class="font-bold">
                    Recent Orders
                </h2>

                <a href="#" class="text-sm text-blue-500">
                    View All
                </a>

            </div>

            @foreach($recentOrders as $order)

                <div class="py-4 border-b">

                    <div class="flex justify-between">

                        <div>

                            <h4 class="font-semibold">
                                #ORD-{{ $order->id }}
                            </h4>

                            <p class="text-sm text-gray-500">
                                {{ $order->created_at->diffForHumans() }}
                            </p>

                        </div>

                        <div class="text-right">

                            <span class="font-semibold text-green-600">
                                ${{ $order->total }}
                            </span>

                            <div>

                                <span class="px-2 py-1 text-xs text-green-700 bg-green-100 rounded-full">
                                    {{ $order->status ?? 'Completed' }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>

{{-- 🔴 LOW STOCK ALERT SECTION (បន្ថែមថ្មី) --}}
<div class="p-6 mt-6 bg-white border border-red-200 shadow-sm rounded-2xl">

    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center space-x-2">
            <span class="text-xl">⚠️</span>
            <h2 class="text-lg font-bold text-red-600">
                Low Stock Alert (Stock &le; 5)
            </h2>
        </div>
        <span class="px-3 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
            {{ $lowStockProducts->count() }} Items
        </span>
    </div>

    <table class="w-full">

        <thead>

            <tr class="text-sm text-gray-500 border-b">

                <th class="py-2 text-left">Product Name</th>
                <th class="py-2 text-center">Price</th>
                <th class="py-2 text-right">Stock Remaining</th>

            </tr>

        </thead>

        <tbody>

            @forelse($lowStockProducts as $product)

                <tr class="transition border-b hover:bg-red-50/50">

                    <td class="py-3 font-semibold text-gray-800">
                        {{ $product->name }}
                    </td>

                    <td class="py-3 text-center text-gray-600">
                        ${{ number_format($product->price, 2) }}
                    </td>

                    <td class="py-3 text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $product->stock == 0 ? 'bg-red-600 text-white' : 'bg-red-100 text-red-800' }}">
                            @if($product->stock == 0)
                                Out of Stock (0)
                            @else
                                {{ $product->stock }} Left
                            @endif
                        </span>
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="3" class="py-4 text-center text-gray-500">
                        All products have sufficient stock.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="p-6 mt-6 bg-white shadow-sm rounded-2xl">

    <h2 class="mb-5 text-lg font-bold">
        🔥 Best Selling Products
    </h2>

    <table class="w-full">

        <thead>

            <tr class="border-b">

                <th class="py-2 text-left">Product</th>
                <th class="py-2 text-right">Sold</th>

            </tr>

        </thead>

        <tbody>

            @forelse($bestSellingProducts as $item)

                <tr class="border-b">

                    <td class="py-3">
                        {{ $item->product?->name ?? '-' }}
                    </td>

                    <td class="text-right">
                        {{ $item->total_qty }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="2" class="py-4 text-center text-gray-500">
                        No sales yet.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>
@if(auth()->user()->isAdmin() || auth()->user()->isStaff())
<div class="p-6 mt-6 bg-white shadow-sm rounded-2xl">

    <h2 class="mb-5 text-lg font-bold">
        👑 Top Customers
    </h2>

    <table class="w-full">

        <thead>

            <tr class="border-b">

                <th class="py-2 text-left">Customer</th>
                <th class="py-2 text-center">Orders</th>
                <th class="py-2 text-right">Spent</th>

            </tr>

        </thead>

        <tbody>

            @forelse($topCustomers as $customer)

                <tr class="border-b">

                    <td class="py-3">
                        {{ $customer->customer?->name ?? 'Guest' }}
                    </td>

                    <td class="text-center">
                        {{ $customer->total_orders }}
                    </td>

                    <td class="font-semibold text-right text-green-600">
                        ${{ number_format($customer->total_spent,2) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="3" class="py-4 text-center text-gray-500">
                        No customer data.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>
@endif
@endsection

@push('scripts')

<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels:[
            '6 Days',
            '5 Days',
            '4 Days',
            '3 Days',
            '2 Days',
            'Yesterday',
            'Today'
        ],
        datasets: [{
            label: 'Sales ($)',
            data: @json($chartData),
            backgroundColor: '#3B82F6',
            borderRadius: 8
        }]
    },
    plugins:{
        legend:{
            display:false
        },
        tooltip:{
            callbacks:{
                label:function(context){
                    return '$'+context.raw;
                }
            }
        }
    },
});

const statusCtx = document.getElementById('statusChart');

new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: [
            'Completed',
            'Pending',
            'Cancelled'
        ],
        datasets: [{
            data: [
                {{ $completedOrders }},
                {{ $pendingOrders }},
                {{ $cancelledOrders }}
            ],
            backgroundColor: [
                '#22C55E',
                '#F59E0B',
                '#EF4444'
            ]
        }]
    }
});
</script>

@endpush
