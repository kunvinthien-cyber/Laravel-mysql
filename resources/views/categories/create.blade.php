@extends('layouts.admin')

@section('content')

<div class="max-w-2xl p-6 mx-auto bg-white shadow rounded-xl">

    <h1 class="mb-6 text-2xl font-bold">
        Add Category
    </h1>

    @if ($errors->any())
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.store') }}" method="POST">

        @csrf

        <div class="mb-4">
            <label class="block mb-2 font-medium">
                Category Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full p-3 border rounded-lg"
                required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">
                Description
            </label>

            <textarea
                name="description"
                rows="4"
                class="w-full p-3 border rounded-lg">{{ old('description') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block mb-2 font-medium">
                Status
            </label>

            <select
                name="status"
                class="w-full p-3 border rounded-lg">

                <option value="1">Active</option>
                <option value="0">Inactive</option>

            </select>
        </div>

        <div class="flex gap-3">

            <button
                class="px-6 py-2 text-white bg-black rounded-lg">
                Save
            </button>

            <a href="{{ route('categories.index') }}"
               class="px-6 py-2 bg-gray-300 rounded-lg">
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection
