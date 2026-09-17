@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow-sm">
    <h2 class="text-xl font-bold text-gray-800 mb-6">{{ ($staffMode ?? false) ? 'បង្កើតគណនីបុគ្គលិក' : 'បង្កើតគណនីម្ចាស់ហាង ឬបុគ្គលិកថ្មី' }}</h2>

    <form action="{{ route(($staffMode ?? false) ? 'staff.store' : 'users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ឈ្មោះ</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">អុីមែល (Email)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" required>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">តួនាទី (User Role)</label>
            <select name="role" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                @if(!($staffMode ?? false))
                <option value="owner">Owner (ម្ចាស់ហាង)</option>
                @endif
                <option value="cashier">Cashier (អ្នកគិតលុយ)</option>
                <option value="staff">Staff (បុគ្គលិកឃ្លាំង)</option>
            </select>
            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        @if(!($staffMode ?? false))
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ហាងសម្រាប់ Staff/Cashier</label>
            <select name="shop_id" class="w-full border-gray-300 rounded-lg p-2.5">
                <option value="">ជ្រើសរើសហាង</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" @selected(old('shop_id') == $shop->id)>{{ $shop->name }}</option>
                @endforeach
            </select>
            @error('shop_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        @endif

        @if(!($staffMode ?? false))
        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-800 mb-3">ព័ត៌មានហាង (សម្រាប់ Owner)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="shop_name" value="{{ old('shop_name') }}" placeholder="ឈ្មោះហាង" class="border-gray-300 rounded-lg p-2.5">
                <input type="text" name="shop_phone" value="{{ old('shop_phone') }}" placeholder="លេខទូរស័ព្ទហាង" class="border-gray-300 rounded-lg p-2.5">
            </div>
            <textarea name="shop_address" rows="2" placeholder="អាសយដ្ឋានហាង" class="w-full border-gray-300 rounded-lg p-2.5 mt-3">{{ old('shop_address') }}</textarea>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">លេខសម្ងាត់ (Password)</label>
                <input type="password" name="password" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">ផ្ទៀងផ្ទាត់លេខសម្ងាត់ (Confirm Password)</label>
                <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
            <a href="{{ route(($staffMode ?? false) ? 'staff.index' : 'users.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                ត្រឡប់ក្រោយ
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                រក្សាទុក
            </button>
        </div>
    </form>
</div>
@endsection
