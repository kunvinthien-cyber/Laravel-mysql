@extends('layouts.admin')

@section('content')

<div class="grid grid-cols-12 gap-6">

    {{-- Products --}}
    <div class="col-span-8">

        <div class="p-5 bg-white shadow rounded-xl">

            <input
                id="search"
                type="text"
                placeholder="🔍 Search product..."
                class="w-full p-3 mb-6 border rounded-lg">

            <div class="grid grid-cols-4 gap-4">

                @foreach($products as $product)

                   <div
    class="p-4 transition border product-card rounded-xl hover:shadow"
    data-name="{{ $product->name }}">
                        @if($product->image)

                            <img
                                src="{{ asset('storage/'.$product->image) }}"
                                class="object-cover w-full h-32 rounded">

                        @endif

                        <h3 class="mt-3 font-bold">

                            {{ $product->name }}

                        </h3>

                        <p class="text-gray-500">

                            {{ $product->category?->name }}

                        </p>

                        <p class="mt-2 text-xl font-bold">

                            ${{ number_format($product->price,2) }}

                        </p>

                        <button
                            class="w-full py-2 mt-3 text-white bg-blue-600 rounded addCart"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}">

                            + Add

                        </button>

                    </div>

                @endforeach

            </div>

        </div>

    </div>
    {{-- Cart --}}
    <div class="col-span-4">

        <div class="p-5 bg-white shadow rounded-xl">

            <h2 class="mb-4 text-xl font-bold">

                🛒 Cart

            </h2>
<div class="mb-4">
    <label class="block mb-2 font-semibold">
        Customer
    </label>

    <select
        id="customer_id"
        class="w-full p-2 border rounded-lg">

        <option value="">
            Select Customer
        </option>

        @foreach($customers as $customer)

            <option value="{{ $customer->id }}">
                {{ $customer->name }}
            </option>

        @endforeach

    </select>
</div>
            <table class="w-full">

                <thead>

                    <tr>

                        <th>Name</th>

                        <th>Qty</th>

                        <th>Total</th>

                    </tr>

                </thead>

                <tbody id="cartBody">

                </tbody>

            </table>

            <hr class="my-5">

            <div class="flex justify-between">

                <span class="font-bold">

                    Grand Total

                </span>

                <span id="grandTotal">

                    $0.00

                </span>

            </div>

        <button
    id="checkoutBtn"
    class="w-full py-3 mt-5 text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:bg-gray-400">

    Checkout

</button>

        </div>

    </div>

</div>
@push('scripts')
<script>

let cart = [];

function renderCart() {

    let tbody = document.getElementById('cartBody');
    let total = 0;
  document.getElementById('checkoutBtn').disabled = cart.length === 0;
    tbody.innerHTML = '';
if(cart.length === 0){

    tbody.innerHTML = `
        <tr>
            <td colspan="4"
                class="py-8 text-center text-gray-400">

                Cart is Empty

            </td>
        </tr>
    `;

}
    cart.forEach((item, index) => {

        let subtotal = item.price * item.qty;

        total += subtotal;

        tbody.innerHTML += `
            <tr class="border-b">
                <td class="py-2">${item.name}</td>

                <td>

                    <button onclick="decreaseQty(${index})"
                        class="px-2 bg-gray-200 rounded">
                        -
                    </button>

                    ${item.qty}

                    <button onclick="increaseQty(${index})"
                        class="px-2 bg-gray-200 rounded">
                        +
                    </button>

                </td>

                <td>$${subtotal.toFixed(2)}</td>

                <td>

                    <button
                        onclick="removeItem(${index})"
                        class="text-red-500">

                        ✕
                    </button>

                </td>

            </tr>
        `;

    });

    document.getElementById('grandTotal').innerHTML =
        '$' + total.toFixed(2);

}

document.querySelectorAll('.addCart').forEach(button => {

    button.addEventListener('click', function(){

        let id = this.dataset.id;

        let name = this.dataset.name;

        let price = parseFloat(this.dataset.price);

        let exist = cart.find(item => item.id == id);

        if(exist){

            exist.qty++;

        }else{

            cart.push({

                id:id,
                name:name,
                price:price,
                qty:1

            });

        }

        renderCart();

    });

});

function increaseQty(index){

    cart[index].qty++;

    renderCart();

}

function decreaseQty(index){

    if(cart[index].qty > 1){

        cart[index].qty--;

    }else{

        cart.splice(index,1);

    }

    renderCart();

}

function removeItem(index){

    cart.splice(index,1);

    renderCart();

}
document.getElementById('checkoutBtn').addEventListener('click', function () {

    if (cart.length === 0) {
        Swal.fire(
            'Warning',
            'Cart is empty!',
            'warning'
        );
        return;
    }

    let customer = document.getElementById('customer_id').value;

    if (!customer) {
        Swal.fire(
            'Warning',
            'Please select a customer.',
            'warning'
        );
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!csrfToken) {
        Swal.fire(
            'Error',
            'Security token is missing. Please refresh the page.',
            'error'
        );
        return;
    }

    fetch("{{ route('pos.checkout') }}", {

        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },

        body: JSON.stringify({

            customer_id: customer,
            cart: cart

        })

    })

    .then(async res => {
        const data = await res.json();

        if (!res.ok) {
            const message = data.message || Object.values(data.errors || {})[0]?.[0] || 'Checkout failed.';

            throw new Error(message);
        }

        return data;
    })

    .then(data => {

        if (data.success) {

            cart = [];

            renderCart();

            document.getElementById('customer_id').selectedIndex = 0;

            Swal.fire(
                'Success',
                data.message,
                'success'
            ).then(() => {
                window.location.href = data.redirect;
            });

        } else {

            Swal.fire(
                'Error',
                data.message,
                'error'
            );

        }

    })

    .catch(error => {

        console.error(error);

        Swal.fire(
            'Error',
            error.message || 'Checkout failed.',
            'error'
        );

    });

});
const search = document.getElementById('search');

search.addEventListener('keyup', function () {

    let keyword = this.value.toLowerCase();

    document.querySelectorAll('.product-card').forEach(card => {

        let name = card.dataset.name.toLowerCase();

        if (name.includes(keyword)) {

            card.style.display = '';

        } else {

            card.style.display = 'none';

        }

    });

});
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

fetch("{{ route('pos.checkout') }}", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": csrfToken
    },
    body: JSON.stringify({
        customer_id: customer,
        cart: cart
    })
})
renderCart();

</script>
@endpush
@endsection
