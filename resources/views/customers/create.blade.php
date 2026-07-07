@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white shadow rounded-lg p-6">

        <h2 class="text-2xl font-bold mb-6">

            Add Customer

        </h2>

        <form action="{{ route('customers.store') }}" method="POST">

            @csrf

            <div class="mb-4">

                <label class="block mb-2 font-medium">

                    Name

                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border rounded-lg px-4 py-2">

                @error('name')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="mb-4">

                <label class="block mb-2 font-medium">

                    Email

                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full border rounded-lg px-4 py-2">

                @error('email')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="mb-4">

                <label class="block mb-2 font-medium">

                    Phone

                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="w-full border rounded-lg px-4 py-2">

                @error('phone')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="mb-6">

                <label class="block mb-2 font-medium">

                    Address

                </label>

                <textarea
                    name="address"
                    rows="4"
                    class="w-full border rounded-lg px-4 py-2">{{ old('address') }}</textarea>

                @error('address')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="flex gap-3">

                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">

                    Save

                </button>

                <a
                    href="{{ route('customers.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">

                    Cancel

                </a>

            </div>

        </form>

    </div>

</div>

@endsection
