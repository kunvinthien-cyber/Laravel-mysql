<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * បង្ហាញបញ្ជីគណនីបុគ្គលិកទាំងអស់
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * បង្ហាញទម្រង់ (Form) បង្កើតបុគ្គលិកថ្មី
     */
    public function create()
    {
        $shops = Shop::where('status', 'active')->orderBy('name')->get();

        return view('users.create', compact('shops'));
    }

    /**
     * រក្សាទុកទិន្នន័យបុគ្គលិកថ្មីទៅក្នុង Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:owner,staff,cashier'],
            'shop_name' => ['nullable', 'string', 'max:255'],
            'shop_phone' => ['nullable', 'string', 'max:50'],
            'shop_address' => ['nullable', 'string', 'max:500'],
            'shop_id' => [
                Rule::requiredIf(fn () => in_array($request->role, ['staff', 'cashier'], true)),
                'nullable',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'shop_id' => $request->input('shop_id'),
            ]);

            if ($user->isOwner()) {
                $shop = Shop::create([
                    'owner_id' => $user->id,
                    'name' => $request->input('shop_name') ?: $user->name,
                    'phone' => $request->input('shop_phone'),
                    'address' => $request->input('shop_address'),
                    'status' => 'active',
                ]);

                $user->update(['shop_id' => $shop->id]);

                foreach ([
                    'shop_name' => $shop->name,
                    'shop_phone' => $shop->phone,
                    'shop_email' => $shop->email,
                    'shop_address' => $shop->address,
                    'currency_symbol' => '$',
                    'tax_rate' => '0',
                ] as $key => $value) {
                    Setting::updateOrCreate(
                        ['shop_id' => $shop->id, 'key' => $key],
                        ['value' => $value]
                    );
                }
            }
        });

        return redirect()->route('users.index')->with('success', 'បង្កើតគណនីបុគ្គលិកបានជោគជ័យ។');
    }

    /**
     * បង្ហាញទម្រង់ (Form) កែសម្រួលទិន្នន័យបុគ្គលិក
     */
    public function edit(User $user)
    {
        $shops = Shop::where('status', 'active')->orderBy('name')->get();

        return view('users.edit', compact('user', 'shops'));
    }

    /**
     * កែសម្រួលទិន្នន័យបុគ្គលិកក្នុង Database
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // បើទុកទទេ គឺមិនប្តូរលេខសម្ងាត់ឡើយ
            'role' => ['required', 'string', 'in:owner,staff,cashier'],
            'shop_id' => [
                Rule::requiredIf(fn () => in_array($request->role, ['staff', 'cashier'], true)),
                'nullable',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if (!$user->isAdmin()) {
            $user->role = $request->role;
            if (!$user->isOwner()) {
                $user->shop_id = $request->input('shop_id');
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'កែសម្រួលគណនីបុគ្គលិកបានជោគជ័យ។');
    }

    /**
     * លុបគណនីបុគ្គលិក
     */
    public function destroy(User $user)
    {
        // ការពារមិនឱ្យ Admin លុបគណនីផ្ទាល់ខ្លួនដែលកំពុងប្រើប្រាស់ឡើយ
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'អ្នកមិនអាចលុបគណនីផ្ទាល់ខ្លួនរបស់អ្នកបានទេ។');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'លុបគណនីបុគ្គលិកបានជោគជ័យ។');
    }
}
