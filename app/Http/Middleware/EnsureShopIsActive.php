<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        if ($request->routeIs('profile.*', 'password.*', 'verification.*', 'logout')) {
            return $next($request);
        }

        if (!$user->shop_id) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'គណនីរបស់អ្នកមិនទាន់បានភ្ជាប់ទៅហាងទេ។ សូមទាក់ទង Admin។',
            ]);
        }

        $shop = $user->shop;

        if ($shop && $shop->isActive()) {
            $settings = Setting::get()->pluck('value', 'key')->all();
            config(['settings' => $settings]);
            View::share('shopSettings', $settings);

            return $next($request);
        }

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'គណនីហាងរបស់អ្នកត្រូវបានផ្អាក ឬផុតកំណត់។ សូមទាក់ទង Admin។',
        ]);
    }
}
