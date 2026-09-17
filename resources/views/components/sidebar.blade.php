<aside class="w-64 bg-white shadow-lg">

    <div class="p-6 border-b">

       <div class="text-xl font-bold text-gray-800">
    {{ $shopSettings['shop_name'] ?? 'POS System' }}
</div>

    </div>

    <nav class="p-4 space-y-2">

        <a href="{{ route('dashboard') }}"
           class="block px-4 py-3 rounded-lg hover:bg-gray-100
           {{ request()->routeIs('dashboard') ? 'bg-black text-white' : '' }}">
            <i class="fa-solid fa-gauge-high mr-2"></i>Dashboard
        </a>
  <a href="{{ route('pos.index') }}"
           class="block px-4 py-3 rounded-lg hover:bg-gray-100">
            <i class="fa-solid fa-cash-register mr-2"></i>POS
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
            <a href="{{ route('products.index') }}"
               class="block px-4 py-3 rounded-lg hover:bg-gray-100
               {{ request()->routeIs('products.*') ? 'bg-black text-white' : '' }}">
                <i class="fa-solid fa-box mr-2"></i>Products
            </a>
            <a href="{{ route('categories.index') }}"
               class="block px-4 py-3 rounded-lg hover:bg-gray-100
               {{ request()->routeIs('categories.*') ? 'bg-black text-white' : '' }}">
                <i class="fa-solid fa-folder-open mr-2"></i>Categories
            </a>
        @endif


       <a href="{{ route('orders.index') }}"
class="block px-4 py-3 rounded-lg hover:bg-gray-100
{{ request()->routeIs('orders.*') ? 'bg-black text-white' : '' }}">
    <i class="fa-solid fa-clipboard-list mr-2"></i>Orders
</a>

        <li>
    <a href="{{ route('customers.index') }}"
       class="block px-4 py-2 hover:bg-gray-100">
        <i class="fa-solid fa-users mr-2"></i>Customers
    </a>
</li>
        @if(auth()->user()->isAdmin() || auth()->user()->isOwner() || auth()->user()->isStaff())
            <a href="{{ route('reports.index') }}"
               class="block px-4 py-3 rounded-lg hover:bg-gray-100
               {{ request()->routeIs('reports.*') ? 'bg-black text-white' : '' }}">
                <i class="fa-solid fa-chart-column mr-2"></i>Reports
            </a>
        @endif
        @if(auth()->user()->isAdmin())
            <a href="{{ route('shops.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('shops.*') ? 'bg-gray-100 font-bold' : '' }}">
                <i class="fa-solid fa-store"></i><span>គ្រប់គ្រងហាង</span>
            </a>

    <a href="{{ route('users.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('users.*') ? 'bg-gray-100 font-bold' : '' }}">
        <i class="fa-solid fa-user-gear"></i><span>គ្រប់គ្រងបុគ្គលិក</span>
    </a>


    <a href="{{ route('backups.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('backups.*') ? 'bg-gray-100 font-bold' : '' }}">
        <i class="fa-solid fa-database"></i><span>ការពារទិន្នន័យ (Backup)</span>
    </a>

@endif
    @if(auth()->user()->isOwner())
        <a href="{{ route('staff.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('staff.*') ? 'bg-gray-100 font-bold' : '' }}">
            <i class="fa-solid fa-user-group"></i><span>គ្រប់គ្រងបុគ្គលិក</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('settings.*') ? 'bg-gray-100 font-bold' : '' }}">
            <i class="fa-solid fa-gear"></i><span>ការកំណត់ហាង</span>
        </a>
    @endif
    </nav>

</aside>
