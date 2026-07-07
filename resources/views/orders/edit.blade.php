@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h1 class="text-2xl font-bold mb-6">
        Edit Order
    </h1>

    <form
        action="{{ route('orders.update',$order) }}"
        method="POST">

        @csrf
        @method('PUT')

        {{-- Product --}}
        <div class="mb-5">

            <label class="block mb-2">
                Product
            </label>

            <select
                name="product_id"
                class="w-full border rounded-lg p-3">

                @foreach($products as $product)

                    <option
                        value="{{ $product->id }}"
                        {{ $order->product_id==$product->id ? 'selected':'' }}>

                        {{ $product->name }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- Customer --}}
        <div class="mb-5">

            <label class="block mb-2">
                Customer
            </label>

            <select
                name="customer_id"
                class="w-full border rounded-lg p-3">

                @foreach($customers as $customer)

                    <option
                        value="{{ $customer->id }}"
                        {{ $order->customer_id==$customer->id ? 'selected':'' }}>

                        {{ $customer->name }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- Status --}}
        <div class="mb-5">

            <label class="block mb-2">
                Status
            </label>

            <select
                name="status"
                class="w-full border rounded-lg p-3">

                <option value="pending"
                    {{ $order->status=='pending'?'selected':'' }}>
                    Pending
                </option>

                <option value="completed"
                    {{ $order->status=='completed'?'selected':'' }}>
                    Completed
                </option>

                <option value="cancelled"
                    {{ $order->status=='cancelled'?'selected':'' }}>
                    Cancelled
                </option>

            </select>

        </div>

        <button
            class="bg-blue-600 text-white px-6 py-3 rounded-lg">

            Update Order

        </button>

    </form>

</div>

@endsection
