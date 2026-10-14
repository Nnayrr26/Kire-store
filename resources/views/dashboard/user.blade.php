@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-shell">
    @include('partials.navbar')
    <main class="container dashboard-main">
        <div class="dashboard-heading">
            <div>
                <p class="dashboard-kicker">DASHBOARD PENGGUNA</p>
                <h1>Hai, {{ auth()->user()->name }} <span class="wave">✦</span></h1>
                <p class="dashboard-description">Senang melihatmu kembali. Siap lanjut bermain hari ini?</p>
            </div>
            <div class="member-chip"><i class="bi bi-patch-check-fill"></i> Member Kire</div>
        </div>

        <section class="balance-card">
            <div class="balance-copy">
                <span class="balance-label"><i class="bi bi-wallet2 me-2"></i> SALDO AKUN</span>
                <h2>Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}<span>,00</span></h2>
                <p>Saldo Kire Store kamu siap digunakan.</p>
            </div>
            <div class="balance-decoration"><i class="bi bi-wallet2"></i><span class="balance-orbit"></span></div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div><p class="dashboard-kicker">TOP UP GAME</p><h2>Mobile Legends</h2></div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('topup.mobile-legends') }}" class="btn btn-quiet btn-sm">Top up diamond</a>
                    <a href="{{ route('joki.mobile-legends') }}" class="btn btn-lime btn-sm">Order jasa joki <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <article class="summary-card">
                        <span class="summary-icon summary-icon-lime"><i class="bi bi-controller"></i></span>
                        <div><span class="summary-label">DIAMOND MOBILE LEGENDS</span><h3>Top up cepat & mudah</h3><p>Masukkan User ID dan Zone ID, lalu pilih paket diamond.</p></div>
                    </article>
                </div>
                <div class="col-md-6">
                    <article class="summary-card">
                        <span class="summary-icon summary-icon-purple"><i class="bi bi-person-badge"></i></span>
                        <div><span class="summary-label">AKUN TERDAFTAR</span><h3>{{ auth()->user()->email }}</h3><p>Terima kasih sudah menjadi bagian dari Kire Store.</p></div>
                    </article>
                </div>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div><p class="dashboard-kicker">AKUN PEMAIN</p><h2>Daftar pemain joki</h2></div>
                <a href="{{ route('joki.mobile-legends') }}" class="btn btn-quiet btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah pemain</a>
            </div>
            <div class="admin-table-card">
                @if (session('success'))
                    <div class="alert alert-success order-alert" role="status">{{ session('success') }}</div>
                @endif
                @if ($jokiOrders->isEmpty())
                    <div class="empty-state"><span><i class="bi bi-controller"></i></span><strong>Belum ada pemain terdaftar</strong><p>Kirim order joki untuk menambahkan akun pemain ke daftar ini.</p></div>
                @else
                    <div class="table-responsive">
                        <table class="table admin-table align-middle mb-0">
                            <thead><tr><th>Pemain / akun</th><th>Jenis jasa</th><th>Rank</th><th>Worker</th><th>Harga penawaran</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach ($jokiOrders as $jokiOrder)
                                    <tr>
                                        <td><strong>{{ $jokiOrder->player_nickname }}</strong><div class="order-date">ID {{ $jokiOrder->player_id }} ({{ $jokiOrder->zone_id }}) · {{ $jokiOrder->order_code }}</div></td>
                                        <td>{{ $jokiOrder->service_label }}</td>
                                        <td>{{ $jokiOrder->current_rank }} <i class="bi bi-arrow-right muted-cell mx-1"></i> {{ $jokiOrder->target_rank }}</td>
                                        <td>{{ $jokiOrder->worker?->name ?? 'Belum ditugaskan' }}</td>
                                        <td>{{ $jokiOrder->quoted_price ? 'Rp'.number_format($jokiOrder->quoted_price, 0, ',', '.') : 'Menunggu admin' }}</td>
                                        <td><span class="order-status status-{{ $jokiOrder->status }}">{{ $jokiOrder->status_label }}</span></td>
                                    </tr>
                                    @if ($jokiOrder->notes)
                                        <tr class="joki-notes-row"><td colspan="6"><span class="muted-cell">Catatan:</span> {{ $jokiOrder->notes }}</td></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div><p class="dashboard-kicker">PESANANMU</p><h2>Riwayat top up</h2></div>
            </div>
            <div class="admin-table-card">
                @if (session('success'))
                    <div class="alert alert-success order-alert" role="status">{{ session('success') }}</div>
                @endif
                @if ($orders->isEmpty())
                    <div class="empty-state"><span><i class="bi bi-receipt"></i></span><strong>Belum ada pesanan</strong><p>Pesanan Mobile Legends yang kamu buat akan muncul di sini.</p></div>
                @else
                    <div class="table-responsive">
                        <table class="table admin-table align-middle mb-0">
                            <thead><tr><th>No. pesanan</th><th>Paket</th><th>ID game</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_code }}</strong><div class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</div></td>
                                        <td>{{ $order->package_name }}</td>
                                        <td>{{ $order->player_id }} <span class="muted-cell">({{ $order->zone_id }})</span></td>
                                        <td>Rp{{ number_format($order->price, 0, ',', '.') }}</td>
                                        <td><span class="order-status status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <section class="welcome-strip">
            <span class="welcome-strip-icon"><i class="bi bi-lightning-charge-fill"></i></span>
            <div><strong>Pesanan diproses manual oleh admin.</strong><p>Setelah membuat pesanan, tunggu admin mengonfirmasi pembayaran dan proses top up-mu.</p></div>
        </section>
    </main>
</div>
@endsection
