<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = User::where('shop_id', $request->user()->shop_id)
            ->whereIn('role', ['staff', 'cashier'])
            ->latest()
            ->paginate(10);

        return view('users.index', [
            'users' => $staff,
            'staffMode' => true,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'shops' => collect(),
            'staffMode' => true,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:staff,cashier'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'shop_id' => $request->user()->shop_id,
        ]);

        return redirect()->route('staff.index')->with('success', 'បានបង្កើតគណនីបុគ្គលិកដោយជោគជ័យ។');
    }

    public function edit(Request $request, User $staff)
    {
        abort_unless($staff->shop_id === $request->user()->shop_id && in_array($staff->role, ['staff', 'cashier'], true), 404);

        return view('users.edit', [
            'user' => $staff,
            'shops' => collect(),
            'staffMode' => true,
        ]);
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($staff->shop_id === $request->user()->shop_id && in_array($staff->role, ['staff', 'cashier'], true), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'role' => ['required', 'in:staff,cashier'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $staff->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if (!empty($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }

        $staff->save();

        return redirect()->route('staff.index')->with('success', 'បានធ្វើបច្ចុប្បន្នភាពគណនីបុគ្គលិក។');
    }

    public function destroy(Request $request, User $staff)
    {
        abort_unless($staff->shop_id === $request->user()->shop_id && in_array($staff->role, ['staff', 'cashier'], true), 404);

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'បានលុបគណនីបុគ្គលិក។');
    }
}
