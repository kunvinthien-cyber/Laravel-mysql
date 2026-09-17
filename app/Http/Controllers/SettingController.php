<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * បង្ហាញទំព័រ Settings
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $shop = $user->shop;
        $settings = Setting::get()->pluck('value', 'key')->all();

        if ($shop) {
            $settings = array_merge($settings, [
                'shop_name' => $shop->name,
                'shop_phone' => $shop->phone,
                'shop_email' => $shop->email,
                'shop_address' => $shop->address,
            ]);
        }

        return view('settings.index', compact('settings'));
    }

    /**
     * កែសម្រួល ឬធ្វើបច្ចុប្បន្នភាពការកំណត់ទាំងអស់
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_phone' => 'nullable|string|max:50',
            'shop_email' => 'nullable|email|max:255',
            'shop_address' => 'nullable|string|max:500',
            'currency_symbol' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $user = $request->user();
        $shop = $user->shop;

        if ($shop) {
            $shop->update([
                'name' => $data['shop_name'],
                'phone' => $data['shop_phone'] ?? null,
                'email' => $data['shop_email'] ?? null,
                'address' => $data['shop_address'] ?? null,
            ]);
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['shop_id' => $user->shop_id, 'key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('success', 'រក្សាទុកការកំណត់ព័ត៌មានហាងបានជោគជ័យ។');
    }
}
