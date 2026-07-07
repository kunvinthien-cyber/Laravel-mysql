<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * បង្ហាញទំព័រ Settings
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
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

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('success', 'រក្សាទុកការកំណត់ព័ត៌មានហាងបានជោគជ័យ។');
    }
}
