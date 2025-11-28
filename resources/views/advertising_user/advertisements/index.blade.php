@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/crear-anuncio.css') }}">

<nav class="navbar bg-white px-3 py-2 shadow-sm fixed-top">
    <a href="{{ url()->previous() }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>
    <span class="mx-auto fw-semibold">Crea la solicitud de su Anuncio</span>
</nav>

<div class="container mt-5 pt-4">
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <strong>Mi Saldo:</strong>
        <span class="fw-bold text-success">S/. {{ auth()->user()->balance ?? 0 }}</span>
    </div>
</div>

<div class="container crear-container mt-5 pt-4 mb-5">

<form method="POST" action="{{ route('advertisements.store') }}" enctype="multipart/form-data">
@csrf

{{-- =========================
   LISTA DE CATEGORÍAS
   ========================= --}}
<div class="card p-3 mb-3">
    <h5 class="fw-bold mb-2">Categoría del Anuncio</h5>

    <select name="ad_categories_id" id="categoria" class="form-select" required>
        <option value="">Seleccione una categoría</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>
</div>

{{-- =========================
   SUBCATEGORÍAS
   ========================= --}}
<div class="card p-3 mb-3 d-none" id="cardSubcategoria">
    <label class="fw-semibold mb-2">Subcategoría</label>
    <select name="ad_subcategories_id" id="subcategoria" class="form-select" required></select>

    <div id="camposDinamicos"></div>
</div>

<button class="btn btn-primary w-100 py-2 fw-bold mb-5">
    Publicar Anuncio
</button>

</form>
</div>


{{-- =========================
   JS
   ========================= --}}
<script>
document.getElementById("categoria").addEventListener("change", async function() {
    const categoryId = this.value;
    const subcatCard = document.getElementById("cardSubcategoria");

    if (!categoryId) return;

    // Pedir subcategorías vía AJAX
    const response = await fetch(`/api/subcategories/${categoryId}`);
    const subcats = await response.json();

    const select = document.getElementById("subcategoria");
    select.innerHTML = `<option value="">Seleccione</option>`;

    subcats.forEach(s => {
        select.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });

    subcatCard.classList.remove("d-none");
});

document.getElementById("subcategoria").addEventListener("change", async function() {
    const subcategoryId = this.value;
    const contenedor = document.getElementById("camposDinamicos");

    if (!subcategoryId) return;
    contenedor.innerHTML = "";

    // Pedir campos dinámicos
    const res = await fetch(`/api/subcategory-fields/${subcategoryId}`);
    const campos = await res.json();

    campos.forEach(c => {
        contenedor.innerHTML += `
            <label class="fw-semibold mb-1">${c.name}</label>
            <input name="fields[${c.id}]" class="form-control mb-3">
        `;
    });

    // Campos base del anuncio
    contenedor.innerHTML += `
        <hr>

        <label class="fw-semibold mb-1">Título</label>
        <input name="title" class="form-control mb-3" required>

        <label class="fw-semibold mb-1">Precio</label>
        <input type="number" name="price" class="form-control mb-3">

        <label class="fw-semibold mb-1">Descripción</label>
        <textarea name="description" rows="4" class="form-control mb-3"></textarea>

        <label class="fw-semibold mb-1">Imágenes</label>
        <input type="file" name="images[]" multiple class="form-control mb-3">
    `;
});
</script>

@endsection
