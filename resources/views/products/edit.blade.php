@extends('layouts.admin')

@section('content')

<h1 class="mb-6 text-2xl font-bold">Edit Product</h1>

<form method="POST"
      action="{{ route('products.update', $product->id) }}"
      enctype="multipart/form-data"
      class="p-6 space-y-4 bg-white rounded-xl">

@csrf
@method('PUT')

{{-- NAME --}}
<input
    name="name"
    value="{{ old('name', $product->name) }}"
    class="w-full p-2 border rounded-lg"
    placeholder="Product Name">

@error('name')
<p class="text-sm text-red-500">{{ $message }}</p>
@enderror

<div class="mb-4">

    <label class="block mb-2 font-medium">
        Category
    </label>

    <select
        name="category_id"
        class="w-full border rounded-lg p-3">

        @foreach($categories as $category)

            <option
                value="{{ $category->id }}"
                {{ $product->category_id == $category->id ? 'selected' : '' }}>

                {{ $category->name }}

            </option>

        @endforeach

    </select>

</div>
{{-- PRICE --}}
<input
    name="price"
    value="{{ old('price', $product->price) }}"
    class="w-full p-2 border rounded-lg"
    placeholder="Price">

@error('price')
<p class="text-sm text-red-500">{{ $message }}</p>
@enderror


{{-- STOCK --}}
<input
    name="stock"
    value="{{ old('stock', $product->stock) }}"
    class="w-full p-2 border rounded-lg"
    placeholder="Stock">

@error('stock')
<p class="text-sm text-red-500">{{ $message }}</p>
@enderror


{{-- CURRENT IMAGE --}}
<div class="mb-2">

    <p class="mb-2 text-sm text-gray-500">Current Image</p>

    @if($product->image)
        <img src="{{ asset('storage/'.$product->image) }}"
             class="object-cover w-20 h-20 rounded-lg">
    @else
        <p class="text-sm text-gray-400">No image</p>
    @endif

</div>


{{-- NEW IMAGE UPLOAD --}}
<div class="mb-6">

    <label class="block font-medium mb-2">
        Product Image
    </label>

    <input
        type="file"
        name="image"
        id="image"
        accept="image/*"
        class="w-full border rounded-lg p-2">

    <div class="mt-4">

        @if($product->image)

            <img id="preview"
                 src="{{ asset('storage/'.$product->image) }}"
                 class="w-48 h-48 object-cover rounded-lg border">

        @else

            <img id="preview"
                 src="https://placehold.co/250x250?text=No+Image"
                 class="w-48 h-48 object-cover rounded-lg border">

        @endif

    </div>

</div>
@error('image')
<p class="text-sm text-red-500">{{ $message }}</p>
@enderror


{{-- BUTTON --}}
<button class="px-4 py-2 text-white bg-black rounded-lg">
    Update Product
</button>

</form>
<select name="category_id" required>
    @foreach($categories as $category)
        <option value="{{ $category->id }}">
            {{ $category->name }}
        </option>
    @endforeach
</select>
@push('scripts')

<script>

document.getElementById('image').addEventListener('change', function(e){

    const file = e.target.files[0];

    if(file){

        document.getElementById('preview').src =
            URL.createObjectURL(file);

    }

});

</script>

@endpush
@endsection
