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
            font-size: 13px;
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
            font-size: 14px;
        }

        td.cantidad {
            text-align: center;
            font-size: 14px;
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
    </style>
</head>

<body>
<div class="ticket centrado">
        <div style="width:330px;">
            @if($config->voucher_logo)
            <div class="logo">
            <img src="{{asset($photo->photo)}}" alt="">
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
        </div>
        <table style="margin-top:10px; width:330px;">
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
        <p  style="margin-top:10px; width:330px; ">¡GRACIAS POR SU COMPRA!
            <br>www.osan.cl
            <br>*<br>*<br>
        </p>
         
    </div>
@if(!empty($auto_print))
<script>
    window.addEventListener('load', function () {
        window.print();
    });

    window.onafterprint = function () {
        window.close();
    };

    setTimeout(function () {
        window.close();
    }, 1200);
</script>
@endif
</body>

</html>