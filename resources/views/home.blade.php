@extends('layouts.app')

@section('title', 'Top up lebih mudah')

@section('content')
<div class="landing-shell">
    @include('partials.navbar')
    <main class="container landing-main">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="eyebrow"><span class="eyebrow-dot"></span> TEMAN TOP UP ANDALANMU</div>
                <h1 class="hero-title">Main terus.<br><span>Top up</span> tanpa ribet.</h1>
                <p class="hero-copy">Semua kebutuhan top up game favoritmu, jadi satu tempat. Cepat, nyaman, dan selalu siap menemani setiap permainan.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('register') }}" class="btn btn-lime btn-lg">Mulai sekarang <i class="bi bi-arrow-right ms-2"></i></a>
                    <a href="{{ route('login') }}" class="btn btn-quiet btn-lg">Saya sudah punya akun</a>
                </div>
                <div class="trust-row">
                    <span><i class="bi bi-shield-check"></i> Aman & terpercaya</span>
                    <span><i class="bi bi-lightning-charge"></i> Proses praktis</span>
                    <span><i class="bi bi-headset"></i> Siap membantu</span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-art">
                    <div class="art-glow"></div>
                    <div class="floating-tag tag-top"><i class="bi bi-stars"></i> Temukan serunya</div>
                    <div class="game-card">
                        <div class="game-card-top"><span class="game-orbit"><i class="bi bi-controller"></i></span><span class="live-pill"><span></span> YOUR GAME HUB</span></div>
                        <div class="game-card-title">Waktunya<br>naik level.</div>
                        <div class="game-card-bottom"><span>Top up favoritmu</span><i class="bi bi-arrow-up-right-circle-fill"></i></div>
                    </div>
                    <div class="floating-tag tag-bottom"><i class="bi bi-lightning-fill"></i> Cepat. Mudah. Kire.</div>
                    <div class="art-spark spark-one">✦</div><div class="art-spark spark-two">✧</div>
                </div>
            </div>
        </div>
    </main>
    <footer class="landing-footer container"><span>© {{ date('Y') }} Kire Store</span><span>Dibuat untuk pengalaman bermain yang lebih seru <i class="bi bi-heart-fill ms-1"></i></span></footer>
</div>
@endsection
