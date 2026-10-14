<?php

namespace App\Http\Controllers;

use App\Models\JokiOrder;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('worker', [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $worker = new User;
        $worker->name = $validated['name'];
        $worker->email = $validated['email'];
        $worker->password = $validated['password'];
        $worker->role = 'worker';
        $worker->save();

        return back()->with('success', "Akun worker {$worker->name} berhasil dibuat. Berikan email dan kata sandi awal secara aman.");
    }

    public function assign(Request $request, JokiOrder $order): RedirectResponse
    {
        $validated = $request->validateWithBag('assignment', [
            'worker_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'worker')),
            ],
        ]);

        if (! in_array($order->status, ['quoted', 'processing'], true)) {
            return back()->withErrors(
                ['worker_id' => 'Worker hanya dapat ditugaskan setelah harga order dikirim ke pelanggan.'],
                'assignment'
            );
        }

        $order->worker_id = $validated['worker_id'] ?? null;
        $order->save();

        $message = $order->worker_id
            ? "Order {$order->order_code} ditugaskan kepada {$order->worker->name}."
            : "Worker untuk order {$order->order_code} berhasil dilepas.";

        return back()->with('success', $message);
    }

    public function updateTask(Request $request, JokiOrder $order): RedirectResponse
    {
        abort_unless($order->worker_id === $request->user()->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['processing', 'completed'])],
        ]);

        $allowedTransition = match ($order->status) {
            'quoted' => $validated['status'] === 'processing',
            'processing' => $validated['status'] === 'completed',
            default => false,
        };

        if (! $allowedTransition) {
            return back()->withErrors([
                'status' => 'Status tugas hanya bisa diubah dari harga dikirim ke sedang dikerjakan, lalu selesai.',
            ]);
        }

        $order->update(['status' => $validated['status']]);

        return back()->with('success', "Progres order {$order->order_code} diperbarui: {$order->status_label}.");
    }
}
