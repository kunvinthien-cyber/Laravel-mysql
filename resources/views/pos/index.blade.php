@extends('layouts.admin')

@section('content')

<div class="grid grid-cols-12 gap-6">

    {{-- Products --}}
    <div class="col-span-8">

        <div class="p-5 bg-white shadow rounded-xl">

            <div class="relative mb-6">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input
                    id="search"
                    type="text"
                    placeholder="Search product..."
                    class="w-full p-3 pl-10 border rounded-lg">
            </div>

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

            <h2 class="mb-4 text-xl font-bold flex items-center gap-2">

                <i class="fa-solid fa-cart-shopping"></i>Cart

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
                {{ $customer->name }} - {{ $customer->phone ?? 'No phone' }} ({{ $customer->points ?? 0 }} pts)
            </option>

        @endforeach

    </select>
</div>

<div class="mb-4 rounded-lg border border-dashed border-gray-300 p-3 bg-gray-50">
    <div class="flex items-center justify-between mb-3">
        <label class="font-semibold text-gray-700">New customer</label>
        <button type="button" id="toggleCustomerForm" class="text-sm text-blue-600 font-semibold">Add customer</button>
    </div>

    <div id="customerForm" class="hidden space-y-3">
        <input type="text" id="new_customer_name" placeholder="Customer name" class="w-full p-2 border rounded-lg">
        <input type="text" id="new_customer_phone" placeholder="Phone number" class="w-full p-2 border rounded-lg">
        <input type="number" id="new_customer_points" min="0" value="0" placeholder="Points" class="w-full p-2 border rounded-lg">
        <button type="button" id="createCustomerBtn" class="w-full bg-blue-600 text-white py-2 rounded-lg">Save customer</button>
    </div>
</div>

<div class="mb-4">
    <label class="block mb-2 font-semibold">
        Payment Method
    </label>

    <select
        id="payment_method"
        class="w-full p-2 border rounded-lg">

        <option value="cash">Cash</option>
        <option value="aba">ABA</option>
        <option value="aceleda">ACLEDA</option>
        <option value="machine">Card / Machine</option>

    </select>
</div>

<div class="mb-4">
    <label class="block mb-2 font-semibold">
        Receipt No
    </label>

    <input
        type="text"
        id="receipt_no"
        placeholder="Receipt number"
        class="w-full p-2 border rounded-lg">
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

                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </td>

            </tr>
        `;

    });

    document.getElementById('grandTotal').innerHTML =
        '$' + total.toFixed(2);

}

document.getElementById('toggleCustomerForm').addEventListener('click', function () {
    const form = document.getElementById('customerForm');
    form.classList.toggle('hidden');
});

document.getElementById('createCustomerBtn').addEventListener('click', function () {
    const name = document.getElementById('new_customer_name').value.trim();
    const phone = document.getElementById('new_customer_phone').value.trim();
    const points = document.getElementById('new_customer_points').value || 0;

    const customerName = name || 'Walk-in customer';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch("{{ route('customers.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            name: customerName,
            phone,
            email: null,
            points: Number(points),
            address: ''
        })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || 'Customer could not be created.');
        }
        return data;
    })
    .then(data => {
        const select = document.getElementById('customer_id');
        const option = document.createElement('option');
        option.value = data.customer_id || data.id;
        option.textContent = `${customerName} - ${phone || 'No phone'} (${Number(points) || 0} pts)`;
        option.selected = true;
        select.appendChild(option);

        document.getElementById('new_customer_name').value = '';
        document.getElementById('new_customer_phone').value = '';
        document.getElementById('new_customer_points').value = 0;
        document.getElementById('customerForm').classList.add('hidden');

        Swal.fire('Success', 'Customer created successfully.', 'success');
    })
    .catch(error => {
        Swal.fire('Error', error.message || 'Customer create failed.', 'error');
    });
});

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
    let paymentMethod = document.getElementById('payment_method').value;
    let receiptNo = document.getElementById('receipt_no').value.trim();

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

            customer_id: customer || null,
            payment_method: paymentMethod,
            receipt_no: receiptNo || null,
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
