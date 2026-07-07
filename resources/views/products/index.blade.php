@extends('layouts.admin')

@section('content')
@push('scripts')


<script>

function confirmDelete(id){

    Swal.fire({
        title: 'Delete Product?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result)=>{

        if(result.isConfirmed){

            document.getElementById('delete-form-'+id).submit();

        }

    });

}

</script>

@endpush

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <th>Category</th>
        <h1 class="text-2xl font-bold">
            Products
        </h1>

        <a href="{{ route('products.create') }}"
           class="px-4 py-2 text-white bg-black rounded-xl">
            + Add Product
        </a>

    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-3 gap-6">

        <div class="p-5 bg-white shadow-sm rounded-2xl">
            <p class="text-gray-500">Total Products</p>
            <h2 class="text-2xl font-bold">{{ $products->count() }}</h2>
        </div>

        <div class="p-5 bg-white shadow-sm rounded-2xl">
            <p class="text-gray-500">Out of Stock</p>
            <h2 class="text-2xl font-bold">
                {{ $products->where('stock',0)->count() }}
            </h2>
        </div>

        <div class="p-5 bg-white shadow-sm rounded-2xl">
            <p class="text-gray-500">Total Value</p>
            <h2 class="text-2xl font-bold">
                ${{ number_format($products->sum('price'),2) }}
            </h2>
        </div>

    </div>
<div class="flex justify-between items-center mb-6">

    <form method="GET" class="flex gap-3">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search Product..."
            class="border rounded-lg px-4 py-2">

        <select
            name="category"
            class="border rounded-lg px-4 py-2">

            <option value="">
                All Categories
            </option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    {{ request('category') == $category->id ? 'selected' : '' }}>

                    {{ $category->name }}

                </option>

            @endforeach

        </select>

        <button
            class="bg-blue-600 text-white px-5 rounded-lg">

            Search

        </button>

    </form>

    <a href="{{ route('products.create') }}"
       class="bg-black text-white px-5 py-2 rounded-lg">

        + Add Product

    </a>

</div>
    {{-- TABLE --}}
    <div class="overflow-hidden bg-white shadow-sm rounded-2xl">

<p class="text-gray-500 mb-3">
    Total Products :
    <strong>{{ $products->total() }}</strong>
</p>
        <table class="w-full">

            <thead class="text-left bg-gray-100">
                <tr>
                    <th class="p-4">Name</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Stock</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>
            <tbody>

                @foreach($products as $product)
               <td class="p-3">
    {{ $product->category?->name ?? '-' }}
</td>
                <tr class="border-b">

                    <td class="p-4 font-semibold">
                        {{ $product->name }}
                    </td>

                    <td class="p-4">
                        ${{ $product->price }}
                    </td>

                    <td class="p-4">
                        {{ $product->stock }}
                    </td>

                    <td class="p-4">

                        @if($product->stock == 0)
                            <span class="px-3 py-1 text-sm text-red-600 bg-red-100 rounded-full">
                                Out of Stock
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm text-green-600 bg-green-100 rounded-full">
                                In Stock
                            </span>
                        @endif

                    </td>

                    <td class="flex gap-3 p-4">

                        <a href="{{ route('products.edit',$product->id) }}"
                           class="text-blue-500">
                            Edit
                        </a>

                     <form id="delete-form-{{ $product->id }}"
      action="{{ route('products.destroy',$product->id) }}"
      method="POST">

    @csrf
    @method('DELETE')

    <button
        type="button"
        onclick="confirmDelete({{ $product->id }})"
        class="text-red-500">
        Delete
    </button>

</form>

                    </td>
                    <td class="p-3">

@if($product->image)

<img
    src="{{ asset('storage/'.$product->image) }}"
    class="w-16 h-16 rounded-lg object-cover">

@else

<span class="text-gray-400">
    No Image
</span>

@endif

</td>
                </tr>

                @endforeach

            </tbody>

        </table>
<div class="mt-6">
    {{ $products->links() }}
</div>
    </div>

</div>

@endsection
