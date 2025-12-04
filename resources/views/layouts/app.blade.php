<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Supera Anuncios</title>
    <link rel="icon" href="{{ asset('assets/img/logo/logo-supera-anuncios.jpeg') }}" type="image/png">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body class="bg-light">

    {{-- SI LA VISTA DEFINE UN NAV PERSONALIZADO, SE USA ESE --}}
    @if(View::hasSection('custom-navbar'))
        @yield('custom-navbar')
    @else
         {{-- NAV SUPERIOR POR DEFECTO --}}
        @include('components.navbar-top')
    @endif

    <main class="pt-5 mt-4 mb-5">
        @yield('content')
    </main>

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



    @stack('scripts')

</body>
</html>
