<nav class="navbar navbar-expand-lg site-navbar">
    <div class="container">
        <a class="navbar-brand brand-lockup" href="{{ route('home') }}">
            <span class="brand-mark"><i class="bi bi-lightning-charge-fill"></i></span>
            <span>KIRE<span class="brand-light">STORE</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    @if (auth()->user()->role === 'user')
                        <a class="nav-link" href="{{ route('topup.mobile-legends') }}">Top Up ML</a>
                        <a class="nav-link" href="{{ route('joki.mobile-legends') }}">Joki ML</a>
                    @elseif (auth()->user()->role === 'worker')
                        <span class="member-chip worker-nav-chip"><i class="bi bi-controller"></i> Worker Joki</span>
                    @endif
                    <span class="nav-user d-none d-lg-inline-flex">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm nav-logout" type="submit">Keluar</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-lime btn-sm px-3" href="{{ route('register') }}">Buat akun <i class="bi bi-arrow-up-right ms-1"></i></a>
                @endauth
            </div>
        </div>
    </div>
</nav>
