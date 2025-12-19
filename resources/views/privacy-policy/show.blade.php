@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Políticas de Privacidad',
        html: `
            <div style="text-align:left; max-height:300px; overflow:auto;">
                {!! nl2br(e($policy->privacy_text)) !!}
                <hr>
                @if($policy->contains_explicit_content)
                    <p><strong>⚠ Contenido explícito</strong></p>
                @endif
                @if($policy->requires_adult)
                    <p><strong>🔞 Solo mayores de edad</strong></p>
                @endif
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Acepto',
        cancelButtonText: 'Rechazo',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('acceptForm').submit();
        } else {
            document.getElementById('rejectForm').submit();
        }
    });
});
</script>

<form id="acceptForm" method="POST" action="{{ route('privacy-policy.accept') }}">
    @csrf
</form>

<form id="rejectForm" method="POST" action="{{ route('privacy-policy.reject') }}">
    @csrf
</form>

@endsection