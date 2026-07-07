@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h1 class="text-2xl font-bold mb-6">
        Create Order
    </h1>

    <form action="{{ route('orders.store') }}" method="POST">

        @csrf

        {{-- Product --}}
        <div class="mb-5">

            <label class="block mb-2 font-semibold">
                Product
            </label>

            <select
                name="product_id"
                class="w-full border rounded-lg p-3">

                @foreach($products as $product)

                    <option value="{{ $product->id }}">

                        {{ $product->name }}
                        (${{ $product->price }})

                    </option>

                @endforeach

            </select>

        </div>

        {{-- Customer --}}
        <div class="mb-5">

            <label class="block mb-2 font-semibold">
                Customer
            </label>

            <select
                name="customer_id"
                class="w-full border rounded-lg p-3">

                @foreach($customers as $customer)

                    <option value="{{ $customer->id }}">

                        {{ $customer->name }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- Status --}}
        <div class="mb-5">

            <label class="block mb-2 font-semibold">
                Status
            </label>

            <select
                name="status"
                class="w-full border rounded-lg p-3">

                <option value="pending">
                    Pending
                </option>

                <option value="completed">
                    Completed
                </option>

                <option value="cancelled">
                    Cancelled
                </option>

            </select>

        </div>

        <button
            class="bg-black text-white px-6 py-3 rounded-lg">

            Save Order

        </button>

    </form>

</div>

@endsection
