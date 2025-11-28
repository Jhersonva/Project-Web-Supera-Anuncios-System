<nav class="navbar bg-white px-3 shadow-sm fixed-top d-flex justify-content-between">

    {{-- IZQUIERDA --}}
    <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('assets/icons/logo.jpg') }}" width="26">
        <strong>Supera A.</strong>
    </div>

    {{-- DERECHA --}}
    <div class="d-flex align-items-center gap-3">

        @auth
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-circle-user"></i> {{ auth()->user()->full_name }}

                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Salir
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
