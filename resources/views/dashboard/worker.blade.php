@extends('layouts.app')

@section('title', 'Dashboard Worker Joki')

@section('content')
<div class="dashboard-shell">
    @include('partials.navbar')
    <main class="container dashboard-main">
        <div class="dashboard-heading">
            <div>
                <p class="dashboard-kicker">PANEL WORKER JOKI</p>
                <h1>Halo, {{ auth()->user()->name }} <span class="wave">✦</span></h1>
                <p class="dashboard-description">Lihat akun pemain yang ditugaskan kepadamu dan perbarui progres pengerjaan.</p>
            </div>
            <div class="member-chip worker-chip"><i class="bi bi-controller"></i> Worker Joki</div>
        </div>

        @if (session('success'))
            <div class="alert alert-success order-alert mb-4" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger worker-error-alert mb-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <section class="row g-3 worker-stats">
            <div class="col-md-4">
                <article class="stat-card"><span class="summary-icon summary-icon-purple"><i class="bi bi-list-task"></i></span><div><span class="summary-label">TOTAL TUGAS</span><strong>{{ $jokiOrders->count() }}</strong><p>Order yang ditugaskan kepadamu</p></div></article>
            </div>
            <div class="col-md-4">
                <article class="stat-card"><span class="summary-icon summary-icon-lime"><i class="bi bi-hourglass-split"></i></span><div><span class="summary-label">SIAP DIKERJAKAN</span><strong>{{ $jokiOrders->where('status', 'quoted')->count() }}</strong><p>Harga sudah dikirim admin</p></div></article>
            </div>
            <div class="col-md-4">
                <article class="stat-card"><span class="summary-icon summary-icon-purple"><i class="bi bi-controller"></i></span><div><span class="summary-label">SEDANG DIKERJAKAN</span><strong>{{ $jokiOrders->where('status', 'processing')->count() }}</strong><p>Tugas yang sedang berjalan</p></div></article>
            </div>
        </section>

        <section class="admin-table-card">
            <div class="section-heading">
                <div><p class="dashboard-kicker">DAFTAR PEMAIN</p><h2>Tugas joki Mobile Legends</h2></div>
                <span class="table-count">{{ $jokiOrders->count() }} order</span>
            </div>
            @if ($jokiOrders->isEmpty())
                <div class="empty-state"><span><i class="bi bi-controller"></i></span><strong>Belum ada tugas untukmu</strong><p>Order pemain yang ditugaskan admin akan muncul di sini.</p></div>
            @else
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Pemain / pelanggan</th><th>Jasa & rank</th><th>Harga</th><th>Status / tindakan</th></tr></thead>
                        <tbody>
                            @foreach ($jokiOrders as $jokiOrder)
                                <tr>
                                    <td><strong>{{ $jokiOrder->player_nickname }}</strong><div class="order-date">ID {{ $jokiOrder->player_id }} ({{ $jokiOrder->zone_id }}) · {{ $jokiOrder->order_code }}</div><div class="order-date">Pelanggan: {{ $jokiOrder->user->name }}</div></td>
                                    <td>{{ $jokiOrder->service_label }}<div class="order-date">{{ $jokiOrder->current_rank }} <i class="bi bi-arrow-right"></i> {{ $jokiOrder->target_rank }}</div></td>
                                    <td>{{ $jokiOrder->quoted_price ? 'Rp'.number_format($jokiOrder->quoted_price, 0, ',', '.') : 'Belum ditentukan' }}</td>
                                    <td>
                                        <span class="order-status status-{{ $jokiOrder->status }}">{{ $jokiOrder->status_label }}</span>
                                        @if ($jokiOrder->status === 'quoted' || $jokiOrder->status === 'processing')
                                            <form method="POST" action="{{ route('dashboard.worker.joki-orders.update', $jokiOrder) }}" class="worker-task-form mt-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $jokiOrder->status === 'quoted' ? 'processing' : 'completed' }}">
                                                <button type="submit" class="btn btn-lime btn-sm">
                                                    {{ $jokiOrder->status === 'quoted' ? 'Mulai pengerjaan' : 'Tandai selesai' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @if ($jokiOrder->notes)
                                    <tr class="joki-notes-row"><td colspan="4"><span class="muted-cell">Catatan pelanggan:</span> {{ $jokiOrder->notes }}</td></tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="topup-note mt-3"><i class="bi bi-shield-lock me-2"></i>Informasi ini hanya menampilkan akun yang ditugaskan kepadamu. Jangan meminta atau menyimpan password maupun kode OTP pemain.</div>
        </section>
    </main>
</div>
@endsection
