<header class="flex items-center justify-between px-8 py-4 bg-white shadow">

    <h2 class="text-xl font-bold">
        Admin Dashboard
    </h2>

    <div class="flex items-center gap-4">

        <span class="font-medium">
            {{ Auth::user()->name }}
        </span>

       <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="flex items-center space-x-2 p-2 w-full text-left text-red-600 hover:bg-red-50 rounded-lg">
        <span>🚪 ចាកចេញ (Logout)</span>
    </button>
</form>

    </div>

</header>
