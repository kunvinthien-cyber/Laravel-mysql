<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with('owner')
            ->withCount(['products', 'orders'])
            ->latest()
            ->paginate(10);

        return view('shops.index', compact('shops'));
    }

    public function create()
    {
        return view('shops.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'expires_at' => ['nullable', 'date'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($data) {
            $owner = User::create([
                'name' => $data['owner_name'],
                'email' => $data['owner_email'],
                'password' => Hash::make($data['owner_password']),
                'role' => 'owner',
            ]);

            $shop = Shop::create([
                'owner_id' => $owner->id,
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'active',
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            $owner->update(['shop_id' => $shop->id]);

            foreach ([
                'shop_name' => $shop->name,
                'shop_phone' => $shop->phone,
                'shop_email' => $shop->email,
                'shop_address' => $shop->address,
                'currency_symbol' => '$',
                'tax_rate' => '0',
            ] as $key => $value) {
                Setting::create([
                    'shop_id' => $shop->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        });

        return redirect()->route('shops.index')->with('success', 'បានបង្កើតហាង និងគណនីម្ចាស់ហាងដោយជោគជ័យ។');
    }

    public function edit(Shop $shop)
    {
        $shop->load('owner');

        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'expires_at' => ['nullable', 'date'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($shop->owner_id)],
            'owner_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($data, $shop) {
            $shop->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => $data['status'],
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            $owner = $shop->owner;
            if ($owner) {
                $owner->name = $data['owner_name'];
                $owner->email = $data['owner_email'];

                if (!empty($data['owner_password'])) {
                    $owner->password = Hash::make($data['owner_password']);
                }

                $owner->save();
            }
        });

        return redirect()->route('shops.index')->with('success', 'ព័ត៌មានហាង និងគណនីម្ចាស់ហាងត្រូវបានធ្វើបច្ចុប្បន្នភាព។');
    }

    public function suspend(Shop $shop)
    {
        $shop->update(['status' => $shop->status === 'active' ? 'suspended' : 'active']);

        return back()->with('success', $shop->status === 'active' ? 'បានបើកដំណើរការហាង។' : 'បានផ្អាកហាង។');
    }
}
