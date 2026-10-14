@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="dashboard-shell">
    @include('partials.navbar')
    <main class="container dashboard-main">
        <div class="dashboard-heading">
            <div>
                <p class="dashboard-kicker">PANEL ADMINISTRATOR</p>
                <h1>Selamat datang, {{ auth()->user()->name }} <span class="wave">✦</span></h1>
                <p class="dashboard-description">Pantau pengguna, top up, dan daftar pemain yang memesan jasa joki.</p>
            </div>
            <div class="member-chip admin-chip"><i class="bi bi-shield-lock-fill"></i> Admin</div>
        </div>

        <section class="row g-3 admin-stats">
            <div class="col-md-6 col-xl-3">
                <article class="stat-card"><span class="summary-icon summary-icon-lime"><i class="bi bi-people"></i></span><div><span class="summary-label">TOTAL PENGGUNA</span><strong>{{ number_format($userCount) }}</strong><p>Akun pengguna terdaftar</p></div><i class="bi bi-arrow-up-right stat-watermark"></i></article>
            </div>
            <div class="col-md-6 col-xl-3">
                <article class="stat-card"><span class="summary-icon summary-icon-purple"><i class="bi bi-person-gear"></i></span><div><span class="summary-label">ADMIN AKTIF</span><strong>{{ number_format($adminCount) }}</strong><p>Akun dengan akses administrator</p></div><i class="bi bi-shield-check stat-watermark"></i></article>
            </div>
            <div class="col-md-6 col-xl-3">
                <article class="stat-card"><span class="summary-icon summary-icon-lime"><i class="bi bi-hourglass-split"></i></span><div><span class="summary-label">PESANAN MENUNGGU</span><strong>{{ number_format($pendingOrderCount) }}</strong><p>Perlu konfirmasi admin</p></div><i class="bi bi-receipt stat-watermark"></i></article>
            </div>
            <div class="col-md-6 col-xl-3">
                <article class="stat-card"><span class="summary-icon summary-icon-purple"><i class="bi bi-controller"></i></span><div><span class="summary-label">ORDER JOKI MENUNGGU</span><strong>{{ number_format($pendingJokiOrderCount) }}</strong><p>Menunggu penawaran harga</p></div><i class="bi bi-trophy stat-watermark"></i></article>
            </div>
            <div class="col-md-6 col-xl-3">
                <article class="stat-card"><span class="summary-icon summary-icon-lime"><i class="bi bi-person-workspace"></i></span><div><span class="summary-label">WORKER AKTIF</span><strong>{{ number_format($workers->count()) }}</strong><p>Akun worker terdaftar</p></div><i class="bi bi-controller stat-watermark"></i></article>
            </div>
        </section>

        <section class="admin-table-card orders-admin-card">
            <div class="section-heading">
                <div><p class="dashboard-kicker">TIM JOKI</p><h2>Daftar worker</h2></div>
                <span class="table-count">{{ $workers->count() }} worker</span>
            </div>
            @if (session('success'))
                <div class="alert alert-success order-alert" role="status">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('dashboard.admin.workers.store') }}" class="worker-create-form">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-lg-3 col-sm-6">
                        <label for="worker-name" class="form-label topup-label">Nama worker</label>
                        <input id="worker-name" name="name" class="form-control topup-input" value="{{ old('name') }}" maxlength="100" required>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <label for="worker-email" class="form-label topup-label">Email login</label>
                        <input id="worker-email" name="email" type="email" class="form-control topup-input" value="{{ old('email') }}" maxlength="255" required>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <label for="worker-password" class="form-label topup-label">Kata sandi awal</label>
                        <input id="worker-password" name="password" type="password" class="form-control topup-input" minlength="8" required>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <label for="worker-password-confirmation" class="form-label topup-label">Ulangi kata sandi</label>
                        <input id="worker-password-confirmation" name="password_confirmation" type="password" class="form-control topup-input" minlength="8" required>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-lime w-100">Tambah worker</button>
                    </div>
                </div>
                @if ($errors->worker->any())
                    <div class="worker-form-errors mt-2">{{ $errors->worker->first() }}</div>
                @endif
            </form>
            @if ($workers->isEmpty())
                <div class="empty-state"><span><i class="bi bi-person-workspace"></i></span><strong>Belum ada worker joki</strong><p>Buat akun worker di formulir atas untuk mulai membagikan tugas.</p></div>
            @else
                <div class="table-responsive mt-3">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Worker</th><th>Email login</th><th>Tugas ditugaskan</th><th>Status akun</th></tr></thead>
                        <tbody>
                            @foreach ($workers as $worker)
                                <tr>
                                    <td><span class="table-avatar">{{ mb_strtoupper(mb_substr($worker->name, 0, 1)) }}</span><strong>{{ $worker->name }}</strong></td>
                                    <td>{{ $worker->email }}</td>
                                    <td>{{ $worker->assigned_joki_orders_count }}</td>
                                    <td><span class="status-pill"><span></span> Aktif</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin-table-card orders-admin-card">
            <div class="section-heading">
                <div><p class="dashboard-kicker">ROSTER PEMAIN</p><h2>Daftar pemain Mobile Legends</h2></div>
                <span class="table-count">{{ $recentJokiOrders->count() }} pemain terbaru</span>
            </div>
            @if ($recentJokiOrders->isEmpty())
                <div class="empty-state"><span><i class="bi bi-controller"></i></span><strong>Belum ada pemain terdaftar</strong><p>Pemain dari order joki pelanggan akan tampil di sini.</p></div>
            @else
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Pemain / pelanggan</th><th>ID game</th><th>Jasa & rank</th><th>Catatan</th><th>Worker</th><th>Harga penawaran</th><th>Status & tindakan</th></tr></thead>
                        <tbody>
                            @foreach ($recentJokiOrders as $jokiOrder)
                                <tr>
                                    <td><strong>{{ $jokiOrder->player_nickname }}</strong><div class="order-date">{{ $jokiOrder->user->name }} · {{ $jokiOrder->order_code }}</div></td>
                                    <td>{{ $jokiOrder->player_id }} <span class="muted-cell">({{ $jokiOrder->zone_id }})</span></td>
                                    <td>{{ $jokiOrder->service_label }}<div class="order-date">{{ $jokiOrder->current_rank }} <i class="bi bi-arrow-right"></i> {{ $jokiOrder->target_rank }}</div></td>
                                    <td class="joki-admin-notes">{{ $jokiOrder->notes ?: '—' }}</td>
                                    <td>
                                        @if (in_array($jokiOrder->status, ['quoted', 'processing'], true))
                                            <form method="POST" action="{{ route('dashboard.admin.joki-orders.assign', $jokiOrder) }}" class="worker-assign-form">
                                                @csrf
                                                @method('PATCH')
                                                <label class="visually-hidden" for="worker-order-{{ $jokiOrder->id }}">Tugaskan worker untuk {{ $jokiOrder->order_code }}</label>
                                                <select id="worker-order-{{ $jokiOrder->id }}" name="worker_id" class="form-select form-select-sm admin-status-select">
                                                    <option value="">Belum ditugaskan</option>
                                                    @foreach ($workers as $worker)
                                                        <option value="{{ $worker->id }}" @selected($jokiOrder->worker_id === $worker->id)>{{ $worker->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-quiet btn-sm">Tugaskan</button>
                                            </form>
                                            @if ($errors->assignment->any())
                                                <div class="joki-form-error">{{ $errors->assignment->first() }}</div>
                                            @endif
                                        @else
                                            <span class="muted-cell">{{ $jokiOrder->worker?->name ?? 'Tunggu harga dikirim' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $jokiOrder->quoted_price ? 'Rp'.number_format($jokiOrder->quoted_price, 0, ',', '.') : 'Belum ditentukan' }}</td>
                                    <td>
                                        <span class="order-status status-{{ $jokiOrder->status }}">{{ $jokiOrder->status_label }}</span>
                                        <form method="POST" action="{{ route('dashboard.admin.joki-orders.update', $jokiOrder) }}" class="joki-admin-form mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="visually-hidden" for="joki-status-{{ $jokiOrder->id }}">Status order {{ $jokiOrder->order_code }}</label>
                                            <select id="joki-status-{{ $jokiOrder->id }}" class="form-select form-select-sm admin-status-select" name="status">
                                                @foreach (\App\Models\JokiOrder::STATUSES as $status => $label)
                                                    <option value="{{ $status }}" @selected($jokiOrder->status === $status)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <label class="visually-hidden" for="joki-price-{{ $jokiOrder->id }}">Harga penawaran dalam rupiah</label>
                                            <input id="joki-price-{{ $jokiOrder->id }}" class="form-control form-control-sm admin-status-select joki-price-input" name="quoted_price" type="number" min="1000" step="1000" value="{{ $jokiOrder->quoted_price }}" placeholder="Harga Rp">
                                            <button type="submit" class="btn btn-lime btn-sm">Simpan</button>
                                        </form>
                                        @if ($errors->any())<div class="joki-form-error">{{ $errors->first() }}</div>@endif
                                    </td>
                                </tr>
                                @if ($jokiOrder->worker)
                                    <tr class="joki-notes-row"><td colspan="7"><span class="muted-cell">Worker ditugaskan:</span> {{ $jokiOrder->worker->name }} · {{ $jokiOrder->worker->email }}</td></tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin-table-card orders-admin-card">
            <div class="section-heading">
                <div><p class="dashboard-kicker">MOBILE LEGENDS</p><h2>Pesanan top up</h2></div>
                <span class="table-count">{{ $recentOrders->count() }} pesanan terbaru</span>
            </div>
            @if (session('success'))
                <div class="alert alert-success order-alert" role="status">{{ session('success') }}</div>
            @endif
            @if ($recentOrders->isEmpty())
                <div class="empty-state"><span><i class="bi bi-receipt"></i></span><strong>Belum ada pesanan top up</strong><p>Pesanan pengguna akan muncul di sini.</p></div>
            @else
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Pesanan / Pengguna</th><th>ID game</th><th>Paket</th><th>Total</th><th>Status</th><th>Perbarui</th></tr></thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_code }}</strong><div class="order-date">{{ $order->user->name }} · {{ $order->created_at->format('d M Y, H:i') }}</div></td>
                                    <td>{{ $order->player_id }} <span class="muted-cell">({{ $order->zone_id }})</span></td>
                                    <td>{{ $order->package_name }}</td>
                                    <td>Rp{{ number_format($order->price, 0, ',', '.') }}</td>
                                    <td><span class="order-status status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                                    <td>
                                        <form method="POST" action="{{ route('dashboard.admin.orders.update', $order) }}" class="order-status-form">
                                            @csrf
                                            @method('PATCH')
                                            <label class="visually-hidden" for="order-status-{{ $order->id }}">Status pesanan {{ $order->order_code }}</label>
                                            <select id="order-status-{{ $order->id }}" class="form-select form-select-sm admin-status-select" name="status">
                                                @foreach (\App\Models\TopUpOrder::STATUSES as $status => $label)
                                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-lime btn-sm">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin-table-card">
            <div class="section-heading">
                <div><p class="dashboard-kicker">MEMBERSHIP</p><h2>Pengguna terbaru</h2></div>
                <span class="table-count">{{ $userCount }} pengguna</span>
            </div>
            @if ($recentUsers->isEmpty())
                <div class="empty-state"><span><i class="bi bi-people"></i></span><strong>Belum ada pengguna</strong><p>Akun baru akan muncul di daftar ini setelah mendaftar.</p></div>
            @else
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Nama</th><th>Email</th><th>Bergabung</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($recentUsers as $user)
                                <tr>
                                    <td><span class="table-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td><span class="status-pill"><span></span> Aktif</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </main>
</div>
@endsection
