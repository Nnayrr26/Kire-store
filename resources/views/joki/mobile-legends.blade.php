@extends('layouts.app')

@section('title', 'Joki Mobile Legends')

@section('content')
<div class="dashboard-shell">
    @include('partials.navbar')
    <main class="container dashboard-main">
        <div class="dashboard-heading">
            <div>
                <p class="dashboard-kicker">JASA JOKI GAME</p>
                <h1>Joki Mobile Legends <span class="wave">✦</span></h1>
                <p class="dashboard-description">Isi detail akun dan target permainan. Admin akan mengirimkan harga sebelum pengerjaan dimulai.</p>
            </div>
            <a href="{{ route('dashboard.user') }}" class="btn btn-quiet btn-sm"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger order-alert mb-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('joki.mobile-legends.store') }}">
            @csrf
            <section class="admin-table-card joki-form-card">
                <div class="section-heading">
                    <div><p class="dashboard-kicker">LAYANAN</p><h2>Pilih jenis jasa joki</h2></div>
                </div>
                <div class="row g-3">
                    @foreach ($services as $key => $label)
                        <div class="col-md-4">
                            <label class="joki-service-option">
                                <input type="radio" name="service_type" value="{{ $key }}" @checked(old('service_type', 'rank') === $key) required>
                                <span class="joki-service-card">
                                    <span class="summary-icon summary-icon-lime"><i class="bi {{ $key === 'rank' ? 'bi-trophy' : ($key === 'winrate' ? 'bi-graph-up-arrow' : 'bi-stars') }}"></i></span>
                                    <strong>{{ $label }}</strong>
                                    <small>{{ $key === 'rank' ? 'Naikkan rank akun ke target yang kamu pilih.' : ($key === 'winrate' ? 'Bantu tingkatkan persentase kemenangan akun.' : 'Bantuan menyelesaikan pertandingan placement.') }}</small>
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-table-card joki-form-card mt-4">
                <div class="section-heading">
                    <div><p class="dashboard-kicker">DATA PEMAIN</p><h2>Daftarkan akun Mobile Legends</h2></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="player_nickname" class="form-label topup-label">Nickname pemain</label>
                        <input id="player_nickname" name="player_nickname" type="text" maxlength="60" value="{{ old('player_nickname') }}" class="form-control topup-input" placeholder="Nickname di dalam game" required>
                    </div>
                    <div class="col-md-3">
                        <label for="player_id" class="form-label topup-label">User ID</label>
                        <input id="player_id" name="player_id" type="text" inputmode="numeric" pattern="[0-9]{1,20}" maxlength="20" value="{{ old('player_id') }}" class="form-control topup-input" placeholder="Contoh: 123456789" required>
                    </div>
                    <div class="col-md-3">
                        <label for="zone_id" class="form-label topup-label">Zone ID</label>
                        <input id="zone_id" name="zone_id" type="text" inputmode="numeric" pattern="[0-9]{1,10}" maxlength="10" value="{{ old('zone_id') }}" class="form-control topup-input" placeholder="Contoh: 1234" required>
                    </div>
                    <div class="col-md-6">
                        <label for="current_rank" class="form-label topup-label">Rank saat ini</label>
                        <select id="current_rank" name="current_rank" class="form-select topup-input" required>
                            <option value="">Pilih rank sekarang</option>
                            @foreach ($ranks as $rank)
                                <option value="{{ $rank }}" @selected(old('current_rank') === $rank)>{{ $rank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="target_rank" class="form-label topup-label">Rank tujuan</label>
                        <select id="target_rank" name="target_rank" class="form-select topup-input" required>
                            <option value="">Pilih rank yang dituju</option>
                            @foreach ($ranks as $rank)
                                <option value="{{ $rank }}" @selected(old('target_rank') === $rank)>{{ $rank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label topup-label">Catatan untuk admin <span class="muted-cell">(opsional)</span></label>
                        <textarea id="notes" name="notes" class="form-control topup-input joki-notes" maxlength="500" rows="3" placeholder="Contoh: waktu yang diinginkan atau informasi tambahan">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="topup-note mt-3"><i class="bi bi-shield-lock me-2"></i>Jangan masukkan password atau kode OTP akun di formulir ini. Admin akan menghubungi untuk membahas proses pengerjaan dan penawaran harga.</div>
                <button type="submit" class="btn btn-lime btn-lg mt-4">Kirim permintaan joki <i class="bi bi-arrow-right ms-2"></i></button>
            </section>
        </form>
    </main>
</div>
@endsection
