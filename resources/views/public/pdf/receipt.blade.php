<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h2 { text-align: center; }
        .section { margin-top: 15px; }
        .box { border: 1px solid #ccc; padding: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>

    <h2>COMPROBANTE DE PAGO</h2>

    <div class="section box">
        <p><span class="label">ID Anuncio:</span> {{ $ad->id }}</p>
        <p><span class="label">Usuario:</span> {{ $user->full_name }}</p>
        <p><span class="label">Tipo de comprobante:</span> {{ strtoupper($ad->receipt_type) }}</p>
    </div>

    <div class="section box">
        @if($ad->receipt_type === 'boleta')
            <p><span class="label">DNI:</span> {{ $ad->dni }}</p>
            <p><span class="label">Nombre completo:</span> {{ $ad->full_name }}</p>
        @endif

        @if($ad->receipt_type === 'factura')
            <p><span class="label">RUC:</span> {{ $ad->ruc }}</p>
            <p><span class="label">Razón social:</span> {{ $ad->company_name }}</p>
            <p><span class="label">Dirección:</span> {{ $ad->address }}</p>
        @endif

        @if($ad->receipt_type === 'nota_venta')
            <p><span class="label">Nombre completo:</span> {{ $ad->full_name }}</p>
        @endif
    </div>

    <div class="section box">
        <p><span class="label">Total pagado:</span> S/. {{ number_format($finalPrice, 2) }}</p>
        <p><span class="label">Fecha:</span> {{ now() }}</p>
    </div>

</body>
</html>
