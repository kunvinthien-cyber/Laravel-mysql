@extends('layouts.admin')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-2xl font-bold">
            Customers
        </h1>

        <a href="{{ route('customers.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">

            + Add Customer

        </a>

    </div>

    <form method="GET" class="mb-5">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search customer..."
            class="border rounded-lg px-4 py-2 w-72">

        <button
            class="bg-gray-800 text-white px-4 py-2 rounded-lg">

            Search

        </button>

    </form>

    @if(session('success'))

        <div class="bg-green-100 text-green-700 p-3 rounded mb-5">

            {{ session('success') }}

        </div>

    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Phone</th>
                    <th class="p-3 text-left">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($customers as $customer)

                    <tr class="border-t">

                        <td class="p-3">
                            {{ $customer->id }}
                        </td>

                        <td class="p-3">
                            {{ $customer->name }}
                        </td>

                        <td class="p-3">
                            {{ $customer->email }}
                        </td>

                        <td class="p-3">
                            {{ $customer->phone }}
                        </td>

                        <!-- ប៊ូតុងសកម្មភាពក្នុងតារាងអតិថិជន -->
<td class="p-3 text-sm text-right flex justify-end space-x-2">
    <!-- គ្រប់គ្នា (រួមទាំង Cashier) អាចមើលឃើញប៊ូតុង កែប្រែ -->
    <a href="{{ route('customers.edit', $customer->id) }}" class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-bold rounded transition">
        កែប្រែ
    </a>

    <!-- លាក់ប៊ូតុង "លុប" មិនឱ្យ Cashier មើលឃើញឡើយ (បង្ហាញតែ Admin និង Staff) -->
    @if(!auth()->user()->isCashier())
        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបអតិថិជននេះមែនទេ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-bold rounded transition">
                លុប
            </button>
        </form>
    @endif
</td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="text-center py-6 text-gray-500">

                            No customers found.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-6">

        {{ $customers->links() }}

    </div>

</div>

@push('scripts')

<script>

document.querySelectorAll('.deleteForm').forEach(form=>{

    form.addEventListener('submit',function(e){

        e.preventDefault();

        Swal.fire({

            title:'Delete Customer?',

            text:'This action cannot be undone.',

            icon:'warning',

            showCancelButton:true,

            confirmButtonColor:'#dc2626',

            confirmButtonText:'Yes, Delete'

        }).then((result)=>{

            if(result.isConfirmed){

                form.submit();

            }

        });

    });

});

</script>

@endpush

@endsection
