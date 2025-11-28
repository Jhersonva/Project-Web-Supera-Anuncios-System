@extends('admin.layouts.app')

@section('title', 'Publicaciones Administrador')

@section('content')

{{-- NAV SUPERIOR --}}
@include('admin.partials.nav-superior')

<link rel="stylesheet" href="{{ asset('css/admin-publications.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container mt-5 pt-4 mb-5">

    <h4 class="fw-bold mb-3">Publicaciones Generales Administrador</h4>

    {{-- LISTA DE PUBLICACIONES --}}
    <div id="listaPublicaciones" class="d-flex flex-column gap-3">
        {{-- Aquí se cargarán vía JS --}}
    </div>

</div>

{{-- NAV INFERIOR --}}
@include('admin.partials.nav-inferior')

<script src="{{ asset('js/admin-publications.js') }}"></script>

@endsection
