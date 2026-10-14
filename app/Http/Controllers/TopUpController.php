<?php

namespace App\Http\Controllers;

use App\Models\TopUpOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TopUpController extends Controller
{
    public function create(): View
    {
        return view('topup.mobile-legends', [
            'packages' => config('kirestore.topup.mobile_legends.packages'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $packages = config('kirestore.topup.mobile_legends.packages');

        $validated = $request->validate([
            'player_id' => ['required', 'string', 'regex:/^[0-9]{1,20}$/'],
            'zone_id' => ['required', 'string', 'regex:/^[0-9]{1,10}$/'],
            'package_key' => ['required', 'string', Rule::in(array_keys($packages))],
        ], [
            'player_id.regex' => 'User ID harus berupa angka.',
            'zone_id.regex' => 'Zone ID harus berupa angka.',
            'package_key.in' => 'Paket yang dipilih tidak tersedia.',
        ]);

        $package = $packages[$validated['package_key']];

        $order = $request->user()->topUpOrders()->create([
            'order_code' => 'KS'.strtoupper(str()->random(10)),
            'game' => 'Mobile Legends',
            'player_id' => $validated['player_id'],
            'zone_id' => $validated['zone_id'],
            'package_key' => $validated['package_key'],
            'package_name' => $package['name'],
            'diamonds' => $package['diamonds'],
            'price' => $package['price'],
        ]);

        return redirect()
            ->route('dashboard.user')
            ->with('success', "Pesanan {$order->order_code} berhasil dibuat dan menunggu konfirmasi admin.");
    }

    public function updateStatus(Request $request, TopUpOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(TopUpOrder::STATUSES))],
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', "Status pesanan {$order->order_code} diperbarui.");
    }
}
