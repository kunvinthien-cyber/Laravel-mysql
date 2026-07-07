<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        return view('users.create');
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
            'role' => ['required', 'string', 'in:admin,staff,cashier'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'បង្កើតគណនីបុគ្គលិកបានជោគជ័យ។');
    }

    /**
     * បង្ហាញទម្រង់ (Form) កែសម្រួលទិន្នន័យបុគ្គលិក
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
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
            'role' => ['required', 'string', 'in:admin,staff,cashier'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

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
