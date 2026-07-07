@extends('layouts.admin')

@section('content')

<div class="p-6 bg-white shadow rounded-xl">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">
            Categories
        </h1>

        <a href="{{ route('categories.create') }}"
           class="px-4 py-2 text-white bg-black rounded-lg">
            + Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border-collapse">

        <thead>

            <tr class="bg-gray-100">

                <th class="p-3 text-left">ID</th>

                <th class="p-3 text-left">Name</th>

                <th class="p-3 text-left">Description</th>

                <th class="p-3 text-center">Status</th>

                <th class="p-3 text-center">Action</th>

            </tr>

        </thead>

        <tbody>

        @forelse($categories as $category)

            <tr class="border-b">

                <td class="p-3">{{ $category->id }}</td>

                <td class="p-3 font-semibold">
                    {{ $category->name }}
                </td>

                <td class="p-3">
                    {{ $category->description }}
                </td>

                <td class="p-3 text-center">

                    @if($category->status)

                        <span class="px-3 py-1 text-sm text-green-700 bg-green-100 rounded-full">
                            Active
                        </span>

                    @else

                        <span class="px-3 py-1 text-sm text-red-700 bg-red-100 rounded-full">
                            Inactive
                        </span>

                    @endif

                </td>
<td class="p-3">
    <div class="flex justify-center gap-2">

        <a href="{{ route('categories.edit', $category->id) }}"
           class="px-3 py-1 text-sm bg-yellow-700 rounded text-zinc-500">
            Edit
        </a>

        <form action="{{ route('categories.destroy', $category->id) }}"
              method="POST"
              onsubmit="return confirm('Are you sure?')">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="px-3 py-1 text-sm bg-red-500 rounded text-red">
                Delete
            </button>

        </form>

    </div>
</td>

            </tr>

        @empty

            <tr>

                <td colspan="5"
                    class="py-6 text-center text-gray-500">
                    No Categories Found
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    <div class="mt-6">

        {{ $categories->links() }}

    </div>

</div>

@endsection
