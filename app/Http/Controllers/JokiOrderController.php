<?php

namespace App\Http\Controllers;

use App\Models\JokiOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JokiOrderController extends Controller
{
    public function create(): View
    {
        return view('joki.mobile-legends', [
            'services' => config('kirestore.joki.mobile_legends.services'),
            'ranks' => config('kirestore.joki.mobile_legends.ranks'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_type' => ['required', 'string', Rule::in(array_keys(config('kirestore.joki.mobile_legends.services')))],
            'player_nickname' => ['required', 'string', 'max:60'],
            'player_id' => ['required', 'string', 'regex:/^[0-9]{1,20}$/'],
            'zone_id' => ['required', 'string', 'regex:/^[0-9]{1,10}$/'],
            'current_rank' => ['required', 'string', Rule::in(config('kirestore.joki.mobile_legends.ranks'))],
            'target_rank' => ['required', 'string', Rule::in(config('kirestore.joki.mobile_legends.ranks'))],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'player_id.regex' => 'User ID harus berupa angka.',
            'zone_id.regex' => 'Zone ID harus berupa angka.',
            'current_rank.in' => 'Rank saat ini tidak valid.',
            'target_rank.in' => 'Rank tujuan tidak valid.',
        ]);

        if ($validated['current_rank'] === $validated['target_rank']) {
            return back()
                ->withErrors(['target_rank' => 'Rank tujuan harus berbeda dari rank saat ini.'])
                ->withInput();
        }

        $order = $request->user()->jokiOrders()->create([
            ...$validated,
            'order_code' => 'JK'.strtoupper(str()->random(10)),
            'game' => 'Mobile Legends',
        ]);

        return redirect()
            ->route('dashboard.user')
            ->with('success', "Order joki {$order->order_code} terkirim. Admin akan meninjau akun dan mengirim penawaran harga.");
    }

    public function update(Request $request, JokiOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(JokiOrder::STATUSES))],
            'quoted_price' => ['nullable', 'integer', 'min:1000', 'max:100000000'],
        ]);
        $quotedPrice = $validated['quoted_price'] ?? null;

        if ($validated['status'] === 'quoted' && empty($quotedPrice)) {
            return back()
                ->withErrors(['quoted_price' => 'Masukkan harga penawaran minimal Rp1.000 sebelum mengirim penawaran.'])
                ->withInput();
        }

        if ($validated['status'] !== 'pending' && $quotedPrice === null && $order->quoted_price !== null) {
            $quotedPrice = $order->quoted_price;
        }

        $order->update([
            'status' => $validated['status'],
            'quoted_price' => $quotedPrice,
        ]);

        return back()->with('success', "Order joki {$order->order_code} berhasil diperbarui.");
    }
}
