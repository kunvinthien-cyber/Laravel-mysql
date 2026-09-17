@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-2xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">បញ្ជីហាងទាំងអស់</h2>
            <p class="text-sm text-gray-500">ចំនួនហាងកំពុងប្រើប្រាស់៖ {{ $shops->total() }}</p>
        </div>
        <a href="{{ route('shops.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">
            <i class="fa-solid fa-plus mr-1"></i> បង្កើតហាងថ្មី
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3 text-sm font-semibold text-gray-600">ID</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">ហាង</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">ម្ចាស់ហាង</th>
                    <th class="p-3 text-sm font-semibold text-gray-600 text-center">ទំនិញ</th>
                    <th class="p-3 text-sm font-semibold text-gray-600 text-center">វិក្កយបត្រ</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">ស្ថានភាព</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">ផុតកំណត់</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">បង្កើតនៅ</th>
                    <th class="p-3 text-sm font-semibold text-gray-600 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm">#{{ $shop->id }}</td>
                        <td class="p-3">
                            <div class="font-semibold text-gray-800">{{ $shop->name }}</div>
                            <div class="text-xs text-gray-500">{{ $shop->phone ?: 'មិនមានលេខទូរស័ព្ទ' }}</div>
                        </td>
                        <td class="p-3">
                            <div class="text-sm font-medium">{{ $shop->owner?->name ?: 'មិនទាន់មាន' }}</div>
                            <div class="text-xs text-gray-500">{{ $shop->owner?->email }}</div>
                        </td>
                        <td class="p-3 text-center text-sm">{{ $shop->products_count }}</td>
                        <td class="p-3 text-center text-sm">{{ $shop->orders_count }}</td>
                        <td class="p-3">
                            @if($shop->isActive())
                                <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Active</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Suspended / Expired</span>
                            @endif
                        </td>
                        <td class="p-3 text-sm text-gray-600">{{ $shop->expires_at?->format('d/m/Y') ?: 'មិនកំណត់' }}</td>
                        <td class="p-3 text-sm text-gray-600">{{ $shop->created_at?->format('d/m/Y') }}</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <a href="{{ route('shops.edit', $shop) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-bold">Edit</a>
                            <form action="{{ route('shops.suspend', $shop) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1 {{ $shop->status === 'active' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded text-xs font-bold">
                                    {{ $shop->status === 'active' ? 'Suspend' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-6 text-center text-gray-500">មិនទាន់មានហាងទេ។</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $shops->links() }}</div>
</div>
@endsection
