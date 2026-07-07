@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl shadow-sm">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">ការកំណត់ព័ត៌មានហាង (System Settings)</h2>
        <p class="text-sm text-gray-500">កែសម្រួលព័ត៌មានហាង អត្រាពន្ធ និងនិមិត្តសញ្ញារូបិយប័ណ្ណដែលត្រូវប្រើប្រាស់លើវិក្កយបត្រ</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- ឈ្មោះហាង -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">ឈ្មោះហាង</label>
                <input type="text" name="shop_name" value="{{ old('shop_name', $settings['shop_name'] ?? '') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- លេខទូរស័ព្ទហាង -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">លេខទូរស័ព្ទហាង</label>
                <input type="text" name="shop_phone" value="{{ old('shop_phone', $settings['shop_phone'] ?? '') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- អុីមែលហាង -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">អុីមែលហាង</label>
                <input type="email" name="shop_email" value="{{ old('shop_email', $settings['shop_email'] ?? '') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- និមិត្តសញ្ញារូបិយប័ណ្ណ (ឧ. $, ៛) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">និមិត្តសញ្ញារូបិយប័ណ្ណ (Currency Symbol)</label>
                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- អត្រាពន្ធ (VAT %) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">អត្រាពន្ធ / VAT (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate'] ?? '0') }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>

        <!-- អាសយដ្ឋានហាង -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">អាសយដ្ឋានហាង</label>
            <textarea name="shop_address" rows="3" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">{{ old('shop_address', $settings['shop_address'] ?? '') }}</textarea>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                💾 រក្សាទុកការកំណត់
            </button>
        </div>
    </form>
</div>
@endsection
