@extends('layouts.app')

@section('title', 'Top Up Mobile Legends')

@section('content')
<div class="dashboard-shell">
    @include('partials.navbar')
    <main class="container dashboard-main">
        <div class="dashboard-heading">
            <div>
                <p class="dashboard-kicker">TOP UP GAME</p>
                <h1>Mobile Legends <span class="wave">✦</span></h1>
                <p class="dashboard-description">Pilih paket diamond dan masukkan data akun Mobile Legends-mu.</p>
            </div>
            <a href="{{ route('dashboard.user') }}" class="btn btn-quiet btn-sm"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger order-alert mb-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('topup.mobile-legends.store') }}">
            @csrf
            <section class="admin-table-card topup-form-card">
                <div class="section-heading">
                    <div><p class="dashboard-kicker">LANGKAH 01</p><h2>Pilih jumlah diamond</h2></div>
                </div>
                <div class="row g-3">
                    @foreach ($packages as $key => $package)
                        <div class="col-sm-6 col-lg-4">
                            <label class="package-option">
                                <input type="radio" name="package_key" value="{{ $key }}" @checked(old('package_key', array_key_first($packages)) === $key) required>
                                <span class="package-card">
                                    <span class="package-icon"><i class="bi bi-gem"></i></span>
                                    <span class="package-info"><strong>{{ $package['name'] }}</strong><small>Diamond Mobile Legends</small></span>
                                    <span class="package-price">Rp{{ number_format($package['price'], 0, ',', '.') }}</span>
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-table-card topup-form-card mt-4">
                <div class="section-heading">
                    <div><p class="dashboard-kicker">LANGKAH 02</p><h2>Masukkan data akun game</h2></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="player_id" class="form-label topup-label">User ID</label>
                        <input id="player_id" name="player_id" type="text" inputmode="numeric" pattern="[0-9]{1,20}" maxlength="20" value="{{ old('player_id') }}" class="form-control topup-input" placeholder="Contoh: 123456789" required>
                    </div>
                    <div class="col-md-6">
                        <label for="zone_id" class="form-label topup-label">Zone ID</label>
                        <input id="zone_id" name="zone_id" type="text" inputmode="numeric" pattern="[0-9]{1,10}" maxlength="10" value="{{ old('zone_id') }}" class="form-control topup-input" placeholder="Contoh: 1234" required>
                    </div>
                </div>
                <div class="topup-note mt-3"><i class="bi bi-info-circle me-2"></i>User ID dan Zone ID dapat dilihat di profil Mobile Legends. Pastikan keduanya benar sebelum membuat pesanan.</div>
                <div class="topup-note topup-note-manual mt-2"><i class="bi bi-clock-history me-2"></i>Pesanan akan menunggu konfirmasi admin. Pembayaran dan pengiriman diamond diproses manual; checkout/payment otomatis belum tersedia.</div>
                <button type="submit" class="btn btn-lime btn-lg mt-4">Buat pesanan <i class="bi bi-arrow-right ms-2"></i></button>
            </section>
        </form>
    </main>
</div>
@endsection
