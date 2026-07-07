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
            📊 Dashboard
        </a>
  <a href="{{ route('pos.index') }}"
           class="block px-4 py-3 rounded-lg hover:bg-gray-100">
            🕒 POS
        </a>
        <a href="{{ route('products.index') }}"
           class="block px-4 py-3 rounded-lg hover:bg-gray-100
           {{ request()->routeIs('products.*') ? 'bg-black text-white' : '' }}">
            📦 Products
        </a>
<a href="{{ route('categories.index') }}"
class="block px-4 py-3 rounded-lg hover:bg-gray-100
{{ request()->routeIs('categories.*') ? 'bg-black text-white' : '' }}">
    📂 Categories
</a>


       <a href="{{ route('orders.index') }}"
class="block px-4 py-3 rounded-lg hover:bg-gray-100
{{ request()->routeIs('orders.*') ? 'bg-black text-white' : '' }}">
    📦 Orders
</a>

        <li>
    <a href="{{ route('customers.index') }}"
       class="block px-4 py-2 hover:bg-gray-100">
        👥 Customers
    </a>
</li>
        <a href="{{ route('reports.index') }}"
           class="block px-4 py-3 rounded-lg hover:bg-gray-100
           {{ request()->routeIs('reports.*') ? 'bg-black text-white' : '' }}">
            📈 Reports
        </a>
        @if(auth()->user()->isAdmin())
    <a href="{{ route('users.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('users.*') ? 'bg-gray-100 font-bold' : '' }}">
        <span>👥 គ្រប់គ្រងបុគ្គលិក</span>
    </a>


    <a href="{{ route('backups.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('backups.*') ? 'bg-gray-100 font-bold' : '' }}">
        <span>💾 ការពារទិន្នន័យ (Backup)</span>
    </a>

  <a href="{{ route('settings.index') }}" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('settings.*') ? 'bg-gray-100 font-bold' : '' }}">
        <span>⚙️ ការកំណត់ហាង</span>
    </a>
@endif
    </nav>

</aside>
