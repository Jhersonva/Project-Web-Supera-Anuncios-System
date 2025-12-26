<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 25px;
        }

        /* HEADER */
        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
        }

        .header-subtitle {
            font-size: 11px;
            color: #666;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 8px;
        }

        .badge-nota {
            background: #f5f5f5;
            border: 1px dashed #333;
        }

        /* SECTION */
        .section {
            margin-top: 18px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 35%;
            color: #555;
        }

        .value {
            width: 65%;
        }

        /* TOTAL */
        .total-box {
            margin-top: 20px;
            border: 2px solid #000;
            padding: 12px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="header-title">COMPROBANTE DE PAGO</div>
        <div class="header-subtitle">
            Plataforma de anuncios · Documento generado automáticamente
        </div>

        @if($ad->receipt_type === 'nota_venta')
            <div class="badge badge-nota">
                NOTA DE VENTA · Código {{ $ad->receipt_code }}
            </div>
        @endif
    </div>

    <!-- DATOS GENERALES -->
    <div class="section">
        <div class="section-title">Información General</div>
        <table>
            <tr>
                <td class="label">ID Anuncio</td>
                <td class="value">#{{ $ad->id }}</td>
            </tr>
            <tr>
                <td class="label">Usuario</td>
                <td class="value">{{ $user->full_name }}</td>
            </tr>
            <tr>
                <td class="label">Tipo de Comprobante</td>
                <td class="value">{{ strtoupper(str_replace('_', ' ', $ad->receipt_type)) }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de emisión</td>
                <td class="value">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section">
        <div class="section-title">Datos del Cliente</div>
        <table>

            @if($ad->receipt_type === 'boleta')
                <tr>
                    <td class="label">DNI</td>
                    <td class="value">{{ $ad->dni }}</td>
                </tr>
                <tr>
                    <td class="label">Nombre completo</td>
                    <td class="value">{{ $ad->full_name }}</td>
                </tr>
            @endif

            @if($ad->receipt_type === 'factura')
                <tr>
                    <td class="label">RUC</td>
                    <td class="value">{{ $ad->ruc }}</td>
                </tr>
                <tr>
                    <td class="label">Razón social</td>
                    <td class="value">{{ $ad->company_name }}</td>
                </tr>
                <tr>
                    <td class="label">Dirección</td>
                    <td class="value">{{ $ad->address }}</td>
                </tr>
            @endif

            @if($ad->receipt_type === 'nota_venta')
                <tr>
                    <td class="label">Cliente</td>
                    <td class="value">{{ $ad->full_name }}</td>
                </tr>
            @endif

        </table>
    </div>

    <!-- TOTAL -->
    <div class="total-box">
        Total pagado: S/. {{ number_format($finalPrice, 2) }}
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Este comprobante es válido como constancia de pago.<br>
        Conserva este documento para cualquier consulta.
    </div>

</div>

</body>
</html>
