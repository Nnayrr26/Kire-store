@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="auth-shell">
    @include('partials.navbar')
    <main class="container auth-main">
        <div class="auth-card auth-card-register">
            <div class="auth-icon"><i class="bi bi-stars"></i></div>
            <p class="auth-eyebrow">GABUNG DI KIRE STORE</p>
            <h1 class="auth-title">Mulai petualanganmu.</h1>
            <p class="auth-subtitle">Buat akun baru, lalu siapkan diri untuk naik level.</p>

            @if ($errors->any())
                <div class="alert alert-danger auth-alert" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama lengkap</label>
                    <div class="input-with-icon"><i class="bi bi-person"></i><input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control" placeholder="Nama kamu" autocomplete="name" maxlength="100" required autofocus></div>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat email</label>
                    <div class="input-with-icon"><i class="bi bi-envelope"></i><input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" autocomplete="email" required></div>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata sandi</label>
                    <div class="input-with-icon"><i class="bi bi-lock"></i><input id="password" name="password" type="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required></div>
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
                    <div class="input-with-icon"><i class="bi bi-shield-lock"></i><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Masukkan kembali kata sandi" autocomplete="new-password" minlength="8" required></div>
                </div>
                <button type="submit" class="btn btn-lime w-100 btn-lg">Buat akun <i class="bi bi-arrow-right ms-2"></i></button>
            </form>
            <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </main>
</div>
@endsection
