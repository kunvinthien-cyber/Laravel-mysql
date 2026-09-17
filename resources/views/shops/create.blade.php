@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl shadow-sm">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">បង្កើតហាងថ្មី</h2>
        <p class="text-sm text-gray-500">បង្កើតព័ត៌មានហាង និងគណនី Login របស់ម្ចាស់ហាងក្នុងពេលតែមួយ។</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('shops.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <h3 class="font-bold text-gray-800 mb-3">ព័ត៌មានហាង</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">ឈ្មោះហាង</label>
                    <input name="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">លេខទូរស័ព្ទហាង</label>
                    <input name="phone" value="{{ old('phone') }}" class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Email ហាង</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">ថ្ងៃផុតកំណត់</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
            </div>
            <textarea name="address" rows="2" placeholder="អាសយដ្ឋានហាង" class="w-full border-gray-300 rounded-lg p-2.5 mt-3">{{ old('address') }}</textarea>
        </div>

        <div class="border-t pt-5">
            <h3 class="font-bold text-gray-800 mb-3">គណនីម្ចាស់ហាង</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">ឈ្មោះម្ចាស់ហាង</label>
                    <input name="owner_name" value="{{ old('owner_name') }}" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Email Login</label>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Password</label>
                    <input type="password" name="owner_password" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">បញ្ជាក់ Password</label>
                    <input type="password" name="owner_password_confirmation" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-5">
            <a href="{{ route('shops.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg">ត្រឡប់ក្រោយ</a>
            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold">បង្កើតហាង</button>
        </div>
    </form>
</div>
@endsection
