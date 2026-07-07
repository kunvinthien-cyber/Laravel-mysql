@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-2xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">គ្រប់គ្រងគណនីបុគ្គលិក</h2>
            <p class="text-sm text-gray-500">មើល បង្កើត កែសម្រួល ឬលុបគណនីបុគ្គលិកក្នុងប្រព័ន្ធ</p>
        </div>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
            + បន្ថែមបុគ្គលិកថ្មី
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3 text-sm font-semibold text-gray-600">ឈ្មោះ</th>
                    <th class="p-3 text-sm font-semibold text-gray-600">អុីមែល</th>
                    <th class="p-3 text-sm font-semibold text-gray-600 text-center">តួនាទី</th>
                    <th class="p-3 text-sm font-semibold text-gray-600 text-right">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3 text-sm font-semibold text-gray-800">{{ $user->name }}</td>
                        <td class="p-3 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="p-3 text-sm text-center">
                            @if($user->isAdmin())
                                <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full">👑 Admin</span>
                            @elseif($user->isStaff())
                                <span class="px-2.5 py-1 text-xs font-bold bg-purple-100 text-purple-800 rounded-full">👨‍💼 Staff</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold bg-orange-100 text-orange-800 rounded-full">💰 Cashier</span>
                            @endif
                        </td>
                        <td class="p-3 text-sm text-right flex justify-end space-x-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-bold rounded transition">
                                កែសម្រួល
                            </a>
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបគណនីបុគ្គលិកនេះមែនទេ?')">
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
                        <td colspan="4" class="p-6 text-center text-gray-500">មិនមានគណនីបុគ្គលិកឡើយ។</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
