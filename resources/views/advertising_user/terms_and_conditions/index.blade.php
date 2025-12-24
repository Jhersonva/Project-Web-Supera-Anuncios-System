<!--views/advertising_user/terms_and_conditions/index.blade.php-->
@extends('layouts.app')

@section('title', 'Términos y Condiciones')

@section('content')
<div class="container mt-5 mb-5">

    <h3 class="fw-bold text-center mb-3">
        Términos y Condiciones – Contenido Adulto
    </h3>

    <p class="text-secondary text-center mb-5">
        Antes de acceder o buscar contenido para adultos, revisa las siguientes condiciones.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            @foreach($terms as $term)
                <div class="card shadow-sm border-0 p-4 mb-4"
                     style="border-radius:16px;">

                    @if($term->icon)
                        <div class="text-center mb-3">
                            <img src="{{ asset($term->icon) }}"
                                 alt="icon"
                                 style="max-height:120px;">
                        </div>
                    @endif

                    <h5 class="fw-bold text-center mb-3">
                        {{ $term->title }}
                    </h5>

                    <p class="text-secondary">
                        {{ $term->description }}
                    </p>

                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection
