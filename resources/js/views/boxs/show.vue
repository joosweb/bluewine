<template>
  <div class="ks-column ks-page mt-4">
    <div class="ks-page-header">
      <section class="ks-title-and-subtitle">
        <div class="ks-title-block">
          <h3 class="ks-main-title">App / Caja</h3>
          <div class="ks-sub-title">
            Sección ver el estado de cajas diarias.
          </div>
        </div>
      </section>
    </div>
    <div class="ks-page-content">
      <div class="ks-page-content-body tables-page">
        <div class="ks-nav-body-wrapper">
          <div class="container-fluid ks-rows-section">
            <div class="row">
              <div class="col-md-3">
                <div class="card panel panel-default panel-table">
                  <div class="card-block" style="padding: 15px">
                    <form class="form-inline" @submit.prevent="() => {}">
                      <div class="input-group mb-2 mr-sm-2">
                        <a
                          type="button"
                          class="btn btn-default btn-block"
                          name="button"
                          href="javascript:history.back()"
                        >
                         VOLVER
                        </a>
                      </div>
                    </form>
                    <hr />
                    <div class="table-responsive">
                      <table class="table table-sm">
                         
                        <tr>
                            <th>FECHA</th>
                            <td> {{ formatDate(this.box.created_at).toUpperCase() }}</td>
                        </tr>
                        <tr>
                            <th>HORA DE INICIO</th>
                            <td>{{ formatTime(this.box.time_start)}}</td>
                        </tr>
                        <tr>
                            <th>HORA DE TERMINO</th>
                            <td>{{ formatTime(this.box.time_end) }}</td>
                        </tr>
                         <tr>
                            <th>CAJA INICIAL</th>
                            <td>$ {{ number_format(this.box.initial_balance) }}</td>
                        </tr>
                         <tr>
                            <th>GASTOS</th>
                            <td>$ {{ number_format(this.box.expenses) }}</td>
                        </tr>
                         <tr>
                            <th>EFECTIVO</th>
                            <td>$ {{ number_format(this.box.cash) }}</td>
                        </tr>
                        <tr>
                            <th>DEBITO</th>
                            <td>$ {{ number_format(this.box.debit) }}</td>
                        </tr>
                        <tr>
                            <th>CREDITO</th>
                            <td>$ {{ number_format(this.box.credit) }}</td>
                        </tr>
                        <tr>
                            <th>TRANSFERENCIAS</th>
                            <td>$ {{ number_format(this.box.transfer) }}</td>
                        </tr>
                        <tr>
                            <th>TOTAL</th>
                            <td>$ {{ number_format(this.box.total) }}</td>
                        </tr>
                        <tr>
                            <th>TOTAL GENERAL</th>
                            <td>$ {{ number_format(this.box.total_general) }}</td>
                        </tr>
                        <tr>
                            <th>EFECTIVO EN CAJA</th>
                            <td>$ {{ number_format(this.box.cash_on_hand) }}</td>
                        </tr>
                        <tr>
                            <th>GANANCIA</th>
                            <td>$ {{ number_format(this.box.total_profit) }}</td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <div class="panel panel-default">
                <div class="panel-heading">VENTAS DEL PERIODO
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <th>VENDEDOR</th>
                            <th>T. DOC</th>                            
                            <th>DESC</th>
                            <th>TOTAL</th>
                            <th>FECHA</th>
                        </thead>
                        <tbody>
                            <tr :key="index" v-for="sale, index in this.sales.data">
                                <td>
                                    <div class="badge badge-secondary">
                                        {{ sale.name }} <br><br> {{ sale.last_name }}
                                    </div>
                                </td>
                                <td v-html="payment_method(sale.payment_method)"></td>                                
                                <td>$ {{ number_format(sale.discount) }}</td>
                                <td>$ {{ number_format(sale.amount) }}</td>
                                <td>{{ formatDate(sale.created_at) }}</td> 
                            </tr>
                        </tbody>
                    </table>
                    <pagination :limit="6" :data="sales" @pagination-change-page="getBox"></pagination>
                </div>
                </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";

export default {
  data() {
    return {
      box: {},
      sales: {}
    };
  },
  mounted() {
    //alert(this.$route.params.id)
    moment.locale('es')
    this.getBox();
  },
  methods: {
    formatTime(date){
        return moment(date).format('hh:mm A')
    },
    formatDate(date){
        return moment(date).format('ll');
    },
    getBox(page = 1) {
      axios.post(`/show/box/${this.$route.params.id}?page=${page}`, { date: this.$route.params.date }).then((response) => {
        console.log(response)
        if (!response.data.box.id) {
          this.$swal
            .fire({
              text: "La caja seleccionada no existe, seras redirigido a la pagina anterior.!",
              icon: "warning",
              showCancelButton: false,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "OK",
            })
            .then((result) => {
              if (result.isConfirmed) {
                return (window.location.href = "/boxs");
              }
            });
        } else {
          this.box = response.data.box;
          this.sales = response.data.sales;
        }
      });
    },
    payment_method: function payment_method(method) {
      if (method == 1) {
        return '<span class="badge badge-success">EFECTIVO</span>';
      } else if (method == 2) {
        return '<span class="badge badge-info">CREDITO</span>';
      } else if (method == 6) {
        return '<span class="badge badge-primary">DEBITO</span>';
      } else if (method == 8) {
        return '<span class="badge badge-warning">TRANSFERENCIA</span>';
      } else {
        return '<span class="badge badge-danger">----------</span>';
      }
    },
    number_format: function (amount, decimals) {
      amount += '' // por si pasan un numero en vez de un string
      amount = parseFloat(amount.replace(/[^0-9\.]/g, '')) // elimino cualquier cosa que no sea numero o punto

      decimals = decimals || 0 // por si la variable no fue fue pasada

      // si no es un numero o es igual a cero retorno el mismo cero
      if (isNaN(amount) || amount === 0) return parseFloat(0).toFixed(decimals)

      // si es mayor o menor que cero retorno el valor formateado como numero
      amount = '' + amount.toFixed(decimals)

      var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/

      while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2')

      return amount_parts.join('.')
    }
  },
};
</script>
