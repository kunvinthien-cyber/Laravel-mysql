@extends('layouts.admin')

@section('content')

<h1 class="mb-6 text-2xl font-bold">Add Product</h1>

<form method="POST"
      action="{{ route('products.store') }}"
      enctype="multipart/form-data"
      class="p-6 space-y-4 bg-white rounded-xl">

    @csrf

    {{-- Product Name --}}
    <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        placeholder="Product Name"
        class="w-full p-2 border rounded-lg">

    @error('name')
        <p class="text-sm text-red-500">{{ $message }}</p>
    @enderror

  <div class="mb-4">
    <label class="block mb-2 font-medium">
        Category
    </label>

    <select
        name="category_id"
        class="w-full border rounded-lg p-3"
        required>

        <option value="">
            Select Category
        </option>

        @foreach($categories as $category)

            <option
                value="{{ $category->id }}">

                {{ $category->name }}

            </option>

        @endforeach

    </select>
</div>
    {{-- Price --}}
    <input
        type="number"
        name="price"
        value="{{ old('price') }}"
        placeholder="Price"
        class="w-full p-2 border rounded-lg">

    @error('price')
        <p class="text-sm text-red-500">{{ $message }}</p>
    @enderror


    {{-- Stock --}}
    <input
        type="number"
        name="stock"
        value="{{ old('stock') }}"
        placeholder="Stock"
        class="w-full p-2 border rounded-lg">

    @error('stock')
        <p class="text-sm text-red-500">{{ $message }}</p>
    @enderror


    {{-- Image --}}
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
        <img id="preview"
             src="https://placehold.co/250x250?text=No+Image"
             class="w-48 h-48 object-cover rounded-lg border">
    </div>

</div>

    @error('image')
        <p class="text-sm text-red-500">{{ $message }}</p>
    @enderror


    <button class="px-4 py-2 text-white bg-black rounded-lg">
        Save Product
    </button>

</form>
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
