@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl shadow-sm">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">កែសម្រួលព័ត៌មានហាង</h2>
        <p class="text-sm text-gray-500">កែព័ត៌មានហាង, ស្ថានភាព, email និង reset password របស់ម្ចាស់ហាង។</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('shops.update', $shop) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">ឈ្មោះហាង</label>
                <input name="name" value="{{ old('name', $shop->name) }}" required class="w-full border-gray-300 rounded-lg p-2.5">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">លេខទូរស័ព្ទហាង</label>
                <input name="phone" value="{{ old('phone', $shop->phone) }}" class="w-full border-gray-300 rounded-lg p-2.5">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email ហាង</label>
                <input type="email" name="email" value="{{ old('email', $shop->email) }}" class="w-full border-gray-300 rounded-lg p-2.5">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">ស្ថានភាព</label>
                <select name="status" class="w-full border-gray-300 rounded-lg p-2.5">
                    <option value="active" @selected(old('status', $shop->status) === 'active')>Active</option>
                    <option value="suspended" @selected(old('status', $shop->status) === 'suspended')>Suspended</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">ថ្ងៃផុតកំណត់</label>
                <input type="date" name="expires_at" value="{{ old('expires_at', $shop->expires_at?->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg p-2.5">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">អាសយដ្ឋាន</label>
            <textarea name="address" rows="2" class="w-full border-gray-300 rounded-lg p-2.5">{{ old('address', $shop->address) }}</textarea>
        </div>

        <div class="border-t pt-5">
            <h3 class="font-bold text-gray-800 mb-3">គណនីម្ចាស់ហាង</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">ឈ្មោះម្ចាស់ហាង</label>
                    <input name="owner_name" value="{{ old('owner_name', $shop->owner?->name) }}" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Email Login</label>
                    <input type="email" name="owner_email" value="{{ old('owner_email', $shop->owner?->email) }}" required class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Password ថ្មី</label>
                    <input type="password" name="owner_password" class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">បញ្ជាក់ Password ថ្មី</label>
                    <input type="password" name="owner_password_confirmation" class="w-full border-gray-300 rounded-lg p-2.5">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-5">
            <a href="{{ route('shops.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg">ត្រឡប់ក្រោយ</a>
            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold">រក្សាទុក</button>
        </div>
    </form>
</div>
@endsection
