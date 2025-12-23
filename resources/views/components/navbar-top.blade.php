<nav class="navbar bg-white px-3 shadow-sm fixed-top d-flex justify-content-between">

    {{-- IZQUIERDA --}}
    <div class="d-flex align-items-center gap-2">
        <strong>{{ system_company_name() }}</strong>
    </div>

    {{-- DERECHA --}}
    <div class="d-flex align-items-center gap-3">

        @auth
        <div class="d-flex align-items-center gap-2">

            <img
                src="{{ auth()->user()->profile_image
                    ? asset(auth()->user()->profile_image)
                    : asset('assets/img/profile-image/default-user.png') }}"
                class="rounded-circle border border-2 border-danger"
                style="width:32px; height:32px; object-fit:cover;"
                alt="Perfil">

            <span class="fw-semibold">
                {{ auth()->user()->full_name }}
            </span>

            <form action="{{ route('auth.logout') }}" method="POST" class="ms-2">
                @csrf
                <button class="btn btn-danger btn-sm">
                    <i class="fa-solid fa-right-to-bracket me-1"></i>Salir
                </button>
            </form>
        </div>

        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar
            </a>
        @endauth

    </div>
</nav>
