@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: '<span style="font-size:22px;font-weight:600;">Términos y Condiciones</span>',
        width: 700,
        padding: '1.5rem',
        backdrop: 'rgba(0,0,0,0.65)',
        html: `
            <div style="
                text-align:left;
                max-height:420px;
                overflow-y:auto;
                padding-right:10px;
                line-height:1.6;
                font-size:14px;
                color:#333;
            ">

                <div style="margin-bottom:16px;">
                    {!! nl2br(e($policy->privacy_text)) !!}
                </div>

                <hr style="margin:16px 0">

            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Rechazar',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#dc3545',
        reverseButtons: true,
        focusConfirm: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            popup: 'privacy-modal',
            confirmButton: 'privacy-btn-confirm',
            cancelButton: 'privacy-btn-cancel'
        }
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