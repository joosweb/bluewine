<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/fonts/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/fonts/line-awesome/css/line-awesome_5.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12  mt-2" style="margin-right:30px;">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>CIERRE DE CAJA</h3>
                    </div>
                </div>
                <div class="card-body">
                <table class="table table-sm table-bordered table-hover">
                    <tbody>
                        <tr class="table-success">
                            <th>INICIO</th>
                            <td><i style="margin-top: -5px" class="las la-stopwatch"></i> {{ $time_start }}</td>
                        </tr>
                        <tr class="table-danger">
                            <th>TERMINO</th>
                            <td><i style="margin-top: -5px" class="las la-stopwatch"></i> {{ $time_end }}</td>
                        </tr>
                        <tr class="table-warning">
                            <th>SALDO INICIAL</th>
                            <td>$ {{ @number_format($daily_box) }}</td>
                        </tr>
                        <tr class="table-primary">
                            <th>GASTOS</th>
                            <td>$ {{ @number_format($expenses) }}</td>
                        </tr>
                    <tr>
                        <td colspan="2">
                        <table class="table table-sm table-hover">
                                <tr>
                                    <td><button style="width:100%;">EFECTIVO</button></td>
                                    <td><button style="width:100%;">$ {{ @number_format($sales_for_type_payment['efectivo']) }}</button></td>
                                </tr>
                                <tr>
                                    <td><button style="width:100%;">CREDITO</button></td>
                                    <td><button style="width:100%;">$ {{ @number_format($sales_for_type_payment['credit']) }}</button></td>
                                </tr>
                                <tr>
                                    <td><button style="width:100%;">DEBITO</button></td>
                                    <td><button style="width:100%;">$ {{ @number_format($sales_for_type_payment['debito']) }}</button></td>
                                </tr>
                                <tr>
                                    <td><button style="width:100%;">TRANSFERENCIA</button></td>
                                    <td><button style="width:100%;">$ {{ @number_format($sales_for_type_payment['transferencia']) }}</button></td>
                                </tr>
                                <tr>
                                    <th><button class="text-right" style="width:100%;">TOTAL</button></th>
                                    <th><button style="width:100%;">$ {{ @number_format($sales_for_type_payment['total']) }}</button></th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                        <tr class="table-active">
                            <th>GANANCIA TOTAL</th>
                            <td>$ {{ @number_format($revenue) }}</td>
                        </tr>
                        <tr class="table-active">
                            <th>TOTAL GENERAL</th>
                            <td>$ {{ @number_format($daily_box - $expenses + $sales_for_type_payment['total']) }}</td>
                        </tr>
                        <tr class="table-info">
                            <th>EFECTIVO EN CAJA</th>
                            <td>$ {{ @number_format($daily_box - $expenses + $sales_for_type_payment['efectivo']) }}</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>