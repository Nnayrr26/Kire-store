<?php

namespace App\Http\Controllers;

use App\Models\JokiOrder;
use App\Models\TopUpOrder;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function user(): View
    {
        return view('dashboard.user', [
            'orders' => auth()->user()->topUpOrders()->latest()->take(5)->get(),
            'jokiOrders' => auth()->user()->jokiOrders()->with('worker')->latest()->take(10)->get(),
        ]);
    }

    public function admin(): View
    {
        return view('dashboard.admin', [
            'userCount' => User::where('role', 'user')->count(),
            'adminCount' => User::where('role', 'admin')->count(),
            'recentUsers' => User::where('role', 'user')->latest()->take(8)->get(),
            'workers' => User::where('role', 'worker')->withCount('assignedJokiOrders')->orderBy('name')->get(),
            'pendingOrderCount' => TopUpOrder::where('status', 'pending')->count(),
            'recentOrders' => TopUpOrder::with('user')->latest()->take(12)->get(),
            'pendingJokiOrderCount' => JokiOrder::where('status', 'pending')->count(),
            'recentJokiOrders' => JokiOrder::with(['user', 'worker'])->latest()->take(20)->get(),
        ]);
    }

    public function worker(): View
    {
        return view('dashboard.worker', [
            'jokiOrders' => auth()->user()->assignedJokiOrders()->with('user')->latest()->get(),
        ]);
    }
}
