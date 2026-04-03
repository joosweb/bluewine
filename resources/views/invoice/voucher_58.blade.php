<?php
$medidaTicket = 217;

function payment_method($method)
{
    if ($method == 1) {
        return 'EFECTIVO';
    } else if ($method == 2) {
        return 'CREDITO';
    } else if ($method == 6) {
        return 'DEBITO';
    } else if ($method == 8) {
        return 'TRANSFERENCIA';
    } else {
        return '----------';
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        * {
            font-size: 12px;
            font-family: 'DejaVu Sans', serif;
        }

        h1 {
            font-size: 18px;
        }

        .ticket {
            margin: 0px;
        }

        td,
        th,
        tr,
        table {
            border-top: 1px solid black;
            border-collapse: collapse;
            margin: 0 auto;
        }

        td.precio {
            text-align: right;
            font-size: 11px;
        }

        td.cantidad {
            text-align: center;
            font-size: 11px;
        }

        td.producto {
            text-align: center;
        }

        th {
            text-align: center;
        }


        .centrado {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: <?php echo $medidaTicket ?>px;
            max-width: <?php echo $medidaTicket ?>px;
        }

        img {
            max-width: 100px;
            width: 100px;
        }

        * {
            margin: 0px 0px 0px 2px;
            padding: 0;
        }

        .ticket {
            margin: 0;
            padding: 0;
        }

        body {
            text-align: center;
        }

        .watermark {
            font-size: 28px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.2);
            transform: rotate(-25deg);
            position: fixed;
            top: 45%;
            left: 6%;
            z-index: -1;
        }
    </style>
</head>

<body>
@if(!empty($printMeta['is_reprint']))
    <div class="watermark">REIMPRESION</div>
@endif
<div class="ticket centrado">
        @if($config->voucher_logo)
        <div class="logo">
                    <img src="{{ $photoSrc ?: asset($photo->photo) }}" width="130" alt="">
          <!-- <img src="{{$photo->photo}}" alt=""> -->
        </div>
        @endif
        <h1>{{ mb_strtoupper($shop_name_eccomerce->name_ecommerce) }}</h1>
        @if($sales[0]->folio)
            <h2>{{ $sales[0]->type_document == 1 ? "BOLETA" : "FACTURA" }} Nº : {{ $sales[0]->folio }}</h2>
        @endif
        <h2>{{ mb_strtoupper($shop_data[0]->name)  }} {{ mb_strtoupper($shop_data[0]->last_name)  }}</h2>
        <h2>{{ mb_strtoupper($shop_data[0]->address)  }}</h2>  
        <h2>{{ $shop_data[0]->phone  }}</h2>
        <h2>{{ $sales[0]->created_at }}</h2>
        <br>
        <h4 style="font-size:10px;">METODO DE PAGO: {!! payment_method($sales[0]->payment_method) !!}</h4>
        <table style="margin-top:10px">
            <thead>
                <tr class="centrado">
                    <th class="cantidad">CANT</th>
                    <th class="producto">PRODUCTO</th>
                    <th class="precio">$</th>
                </tr>
            </thead>
            <tbody>                
            @foreach($sales_items as $value)
                    <tr>
                        <td class="cantidad"> {{ $value->quantity }}x</td>
                        <td class="producto">{{ mb_strtoupper($value->name_item) }}</td>
                        <td class="precio">$ {{ number_format($value->price) }}</td>
                    </tr>
              @endforeach
            </tbody>
            @if(intval($sales[0]->discount) !== 0)
            <tr class="tabletitle">
                <td></td>
                <td class="Rate"><h2>DESC</h2></td>
                <td class="payment"><h2>$ {{ number_format(round($sales[0]->discount * $sales[0]->amount / 100 / 10) * 10)  }}</h2></td>
            </tr>
            <tr class="tabletitle">
                <td></td>
                <td class="Rate">**</td>
                <td class="payment">$ {{ (number_format($sales[0]->amount))  }}</td>
            </tr>
            @endif
            <tr>
                 <td class="precio" colspan="3">
                 TOTAL <strong> $ {{ number_format($sales[0]->amount - round($sales[0]->discount * $sales[0]->amount / 100 / 10) * 10) }} </strong>
                </td>
            </tr>
        </table>
        <p class="centrado" style="margin-top:10px">¡GRACIAS POR SU COMPRA!
            <br>www.osan.cl</p>
        @if(!empty($printMeta['is_reprint']))
            <p class="centrado" style="margin-top:5px; font-size:10px;">
                COPIA {{ $printMeta['copy_number'] ?? 0 }}<br>
                IMPRESO POR: {{ mb_strtoupper($printMeta['printed_by'] ?? 'N/A') }}<br>
                FECHA: {{ $printMeta['printed_at'] ?? '' }}<br>
                MOTIVO: {{ mb_strtoupper($printMeta['reason'] ?? '') }}
            </p>
        @endif
         <br>*<br>*<br>
    </div>
</body>

</html>