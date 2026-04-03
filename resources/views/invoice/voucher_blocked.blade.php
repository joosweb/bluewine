<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Voucher bloqueado</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            color: #222;
        }

        .box {
            max-width: 420px;
            margin: 40px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 22px;
            text-align: center;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 22px;
        }

        p {
            margin: 10px 0;
            font-size: 15px;
            line-height: 1.4;
        }

        .id {
            font-weight: 700;
            margin-top: 14px;
        }

        .time {
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Voucher ya impreso</h1>
        <p>Esta venta ya tiene un comprobante emitido y no permite reimpresion.</p>
        @if(!empty($sale))
            <p class="id">Venta #{{ $sale->id }}</p>
            @if(!empty($sale->voucher_first_printed_at))
                <p class="time">Primera impresion: {{ $sale->voucher_first_printed_at }}</p>
            @endif
        @endif
    </div>

    @if(!empty($autoPrint))
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.close();
            }, 1200);
        });
    </script>
    @endif
</body>
</html>
