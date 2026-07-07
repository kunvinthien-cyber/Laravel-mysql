@extends('layouts.admin')

@section('content')

<div class="max-w-2xl p-6 mx-auto bg-white shadow rounded-xl">

    <h1 class="mb-6 text-2xl font-bold">
        Edit Category
    </h1>

    <form method="POST"
          action="{{ route('categories.update',$category->id) }}">

        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-2 font-medium">
                Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name',$category->name) }}"
                class="w-full p-3 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">
                Description
            </label>

            <textarea
                name="description"
                rows="4"
                class="w-full p-3 border rounded-lg">{{ old('description',$category->description) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block mb-2 font-medium">
                Status
            </label>

            <select
                name="status"
                class="w-full p-3 border rounded-lg">

                <option value="1"
                    {{ $category->status ? 'selected' : '' }}>
                    Active
                </option>

                <option value="0"
                    {{ !$category->status ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>
        </div>

        <button
            class="px-6 py-2 text-white bg-black rounded-lg">
            Update
        </button>

    </form>

</div>

@endsection
