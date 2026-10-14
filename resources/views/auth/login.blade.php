@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-shell">
    @include('partials.navbar')
    <main class="container auth-main">
        <div class="auth-card">
            <div class="auth-icon"><i class="bi bi-person-check"></i></div>
            <p class="auth-eyebrow">SENANG MELIHATMU LAGI</p>
            <h1 class="auth-title">Selamat datang<br>kembali.</h1>
            <p class="auth-subtitle">Masuk untuk lanjut ke dunia game favoritmu.</p>

            @if ($errors->any())
                <div class="alert alert-danger auth-alert" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat email</label>
                    <div class="input-with-icon"><i class="bi bi-envelope"></i><input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" autocomplete="email" required autofocus></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata sandi</label>
                    <div class="input-with-icon"><i class="bi bi-lock"></i><input id="password" name="password" type="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="remember-label"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    <span class="form-hint">Minimal 8 karakter</span>
                </div>
                <button type="submit" class="btn btn-lime w-100 btn-lg">Masuk ke akun <i class="bi bi-arrow-right ms-2"></i></button>
            </form>
            <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
        </div>
    </main>
</div>
@endsection
