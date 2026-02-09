<!-- views/layouts/app.blade.php -->
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#dc3545">
    <title>Supera Anuncios</title>
    <link rel="icon" href="{{ system_logo() }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <!-- Manifest para PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3a68d6">
</head>

<body class="bg-light d-flex flex-column min-vh-100">


    {{-- SI LA VISTA DEFINE UN NAV PERSONALIZADO, SE USA ESE --}}
    @if(View::hasSection('custom-navbar'))
        @yield('custom-navbar')
    @else
         {{-- NAV SUPERIOR POR DEFECTO --}}
        @include('components.navbar-top')
    @endif

    <main class="flex-grow-1 pt-5 mt-4"
        style="padding-bottom: calc(var(--bottom-nav-height) + 1rem);">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- SI LA VISTA TIENE UN NAV INFERIOR PERSONALIZADO, MOSTRARLO --}}
    @if(View::hasSection('custom-bottom-nav'))
        @yield('custom-bottom-nav')

    {{-- NAV INFERIOR POR ROLES --}}
    @elseif(auth()->check() && in_array(auth()->user()->role->name, ['admin', 'employee']))
        @include('components.navbar-bottom-admin')

    {{-- NAV NORMAL --}}
    @else
        @include('components.navbar-bottom')
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>

    @auth
        window.PROFILE_COMPLETE = @json(auth()->user()->is_profile_complete);
        window.PROFILE_CONFIG_URL = "{{ route('profile.index') }}";
    @endauth

    document.addEventListener('DOMContentLoaded', function () {

        @if(auth()->check() && in_array(auth()->user()->role->name, ['admin', 'employee']))

            /* BADGE RECARGAS */
            const badgeRecargas = document.getElementById('badge-recargas');

            async function actualizarRecargas() {
                if (!badgeRecargas) return;

                try {
                    const res = await axios.get('{{ route('admin.reload-request.pending-count') }}');
                    const count = res.data.count;

                    badgeRecargas.textContent = count;
                    badgeRecargas.style.display = count > 0 ? 'inline-block' : 'none';

                } catch (e) {
                    console.warn('Error recargas');
                }
            }

            /* BADGE ANUNCIOS */
            const badgeAds = document.getElementById('badge-ads');

            async function actualizarAnuncios() {
                if (!badgeAds) return;

                try {
                    const res = await axios.get('{{ route('admin.ads.pending-count') }}');
                    const count = res.data.count;

                    badgeAds.textContent = count;
                    badgeAds.style.display = count > 0 ? 'inline-block' : 'none';

                } catch (e) {
                    console.warn('Error anuncios');
                }
            }

            actualizarRecargas();
            actualizarAnuncios();

            setInterval(() => {
                actualizarRecargas();
                actualizarAnuncios();
            }, 10000);

        @endif

    });

    document.addEventListener('DOMContentLoaded', function () {

        @if(auth()->check())

            const badgeChat = document.getElementById('badge-chat');

            async function actualizarChat() {
                if (!badgeChat) return;

                try {
                    const res = await axios.get('{{ route('chat.unread-count') }}');
                    const count = res.data.count;

                    badgeChat.textContent = count;
                    badgeChat.style.display = count > 0 ? 'inline-block' : 'none';

                } catch (e) {
                    console.warn('Error chat badge');
                }
            }

            actualizarChat();

            setInterval(() => {
                actualizarChat();
            }, 5000);

        @endif

    });

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: "{{ session('success') }}",
            confirmButtonColor: '#3085d6'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Corrige los errores',
            html: `
                <ul style="text-align:left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `
        });
    @endif

    // Registrar el Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
        .then(reg => console.log('Service Worker registrado', reg))
        .catch(err => console.error('SW error', err));
    }

    document.addEventListener('DOMContentLoaded', function () {

    if (!window.IS_AUTHENTICATED) return;
    if (window.PROFILE_COMPLETE) return;

    const STORAGE_KEY = 'profile_alert_last_shown';
    const MINUTES_DELAY = 1; // 

    const now = Date.now();
    const lastShown = localStorage.getItem(STORAGE_KEY);

    if (lastShown && (now - lastShown) < MINUTES_DELAY * 60 * 1000) {
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Verifica tus datos',
        html: `
            <p class="mb-2">
                Tu perfil no está completamente configurado.
            </p>
            <p class="small text-muted">
                Es necesario completar tus datos para poder publicar anuncios
                y generar confianza con otros usuarios.
            </p>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ir a configurar',
        cancelButtonText: 'Más tarde',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {

        localStorage.setItem(STORAGE_KEY, Date.now());

        if (result.isConfirmed) {
            window.location.href = window.PROFILE_CONFIG_URL;
        }
    });

});


    </script>


    @stack('scripts')

</body>
</html>
