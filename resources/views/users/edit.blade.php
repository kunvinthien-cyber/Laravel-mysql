@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-2xl shadow-sm">
    <h2 class="text-xl font-bold text-gray-800 mb-6">កែសម្រួលគណនីបុគ្គលិក</h2>

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ឈ្មោះបុគ្គលិក</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">អុីមែល (Email)</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" required>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">តួនាទី (User Role)</label>
            <select name="role" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="cashier" {{ $user->role === 'cashier' || $user->role === 'user' ? 'selected' : '' }}>💰 Cashier (អ្នកគិតលុយ)</option>
                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>👨‍💼 Staff (បុគ្គលិកឃ្លាំង)</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>👑 Admin (អ្នកគ្រប់គ្រង)</option>
            </select>
            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg text-xs mb-2">
            ⚠️ <strong>ចំណាំ៖</strong> ទុកប្រអប់លេខសម្ងាត់ខាងក្រោមឱ្យនៅទទេ ប្រសិនបើអ្នកមិនចង់ផ្លាស់ប្តូរលេខសម្ងាត់របស់បុគ្គលិកម្នាក់នេះឡើយ។
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">លេខសម្ងាត់ថ្មី (Password)</label>
                <input type="password" name="password" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">ផ្ទៀងផ្ទាត់លេខសម្ងាត់ថ្មី (Confirm Password)</label>
                <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                ត្រឡប់ក្រោយ
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                ធ្វើបច្ចុប្បន្នភាព
            </button>
        </div>
    </form>
</div>
@endsection
