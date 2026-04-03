<template>
  <div class="container-fluid">
    
    <div class="ks-page-header" v-if="!hidden">
        <section class="ks-title-and-subtitle mt-2">
            <div class="ks-title-block">
                <h3 class="ks-main-title">App / Ventas</h3>
                <div class="ks-sub-title">Sección reporte de ventas.</div>
            </div>
        </section>
    </div>
        <div class="ks-page-content">
          <div class="ks-page-content-body tables-page">
              <div class="ks-nav-body-wrapper">
                    <div :class="containerC ? '' : 'container'" class="ks-rows-section">
                      <div class="row">
                          <div class="col-md-12" :style="margin">
                              <div class="card panel panel-default panel-table">
                                <div class="card-block" style="padding:15px;">
                                    <form class="form-inline" @submit.prevent="">
                                    <label class="sr-only" for="inlineFormInputName2">Name</label>
                                    <select class="form-control mb-2 mr-sm-2" v-model="filter">
                                      <option value="T2.run" selected>RUN</option>
                                      <!--<option value="T2.name">NOMBRE</option>-->
                                    </select>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <input @keyup="searchCliente" v-model="inputValue" type="text" class="form-control" placeholder="INGRESE RUN" autofocus>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button class="btn btn-default btn-block" @click="getSales"><font-awesome-icon :icon="['fas', 'sync']" /> ACTUALIZAR</button>
                                    </div>
                                  </form>
                                  <hr>
                                  <div class="table-responsive">
                                  <table class="table table-sm table-bordered table-hover">
                                    <thead class="thead-light">
                                      <tr>
                                        <th>RUN CLIENTE</th>
                                        <th>VENDEDOR</th>
                                        <th>ESTADO</th>
                                        <th>T. DOC</th>
                                        <th v-if="!hidden">TOTAL</th>
                                        <th v-if="!hidden">SIN DESCUENTO</th>
                                        <th v-if="!hidden">FECHA</th>
                                        <th>ACCIÓN</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="sale, index in this.sales.data" :title="sale.name + sale.last_name ? sale.name + sale.last_name: '--'">
                                        <td >{{ sale.run ? sale.run : '-----------'}}</button></td>
                                        <td> <div class="badge badge-primary">{{ sale.vname }} {{ sale.vlast_name}}</div></td>
                                        <td v-html="informedSii(sale.informedSii)"></td>
                                        <td v-html="type_document(sale.type_document)"></td>
                                        <td v-if="!hidden">{{ number_format(sale.amount) }}</td>
                                        <td v-if="!hidden">$ {{ TotalwhitOutDiscount(sale.discount_type, sale.discount, sale.amount) }}</td>
                                        <td v-if="!hidden">{{ DifForHumans(sale.created_at) }}</td>
                                        <td>
                                          <div class="btn-group" role="group">
                                          <button title="DETALLES" @click="ModalViewSale(sale.id, sale.fk_cliente_id)" class="btn btn-info btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>
                                          <button title="ELIMINAR" @click="DeleteSale(sale.id, index)" type="button" class="btn btn-danger btn-sm" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                        </div>
                                        </td>
                                      </tr>
                                      <tr v-if="withoutResults">
                                        <td colspan="7">
                                          <div class="alert alert-info alert-dismissible fade show" role="alert">
                                          <strong>Sin resultados!</strong> no se encontraron resultados en la busqueda.
                                        </div>
                                      </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <hr>
                                  <pagination :limit="6" :data="sales" @pagination-change-page="getSales">
                                  </pagination>
                                  </div>
                                </div>
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
    import Vue from 'vue'
    import moment from 'moment'
    import VueSweetalert2 from 'vue-sweetalert2';
    Vue.use(VueSweetalert2);

    export default {
        props: {
        margin: String,
        hidden: Boolean,
        containerC: Boolean,
        },
        data() {
          return {
            sales: {},
            sale: {},
            saleDetails: {},
            filter: 'T2.run',
            inputValue: '',
            withoutResults: false,
            clientName: '',
            clientLast_name: '',
            clientEmail: '',
            clientPhone: '',
            clientView: false,
          }
        },
        created(){
          this.getSales();
        },
        mounted() {
            console.log('component mounted');
        },
        methods: {
          DifForHumans: function (d) {
            moment.locale('es');
            return moment(d).format("MMM Do YY");
          },
          userData(id) {
            if(!id) {
              this.clientName = '';
              this.clientLast_name = '';
              this.clientEmail = '';
              this.clientPhone = '';
            }
            else {
              axios.post('/clients/search/'+id).then(response => {
                  console.log(response);
                  this.clientName = response.data.name;
                  this.clientLast_name = response.data.last_name;
                  this.clientEmail = response.data.email;
                  this.clientPhone = response.data.phone;
              });
            }
          },
          number_format: function(amount, decimals) {
            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

            decimals = decimals || 0; // por si la variable no fue fue pasada

            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0)
                return parseFloat(0).toFixed(decimals);

            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);

            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;

            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

            return amount_parts.join('.');
          },
          getSales(page = 1) {
            axios.post('/sales/get?page='+page).then(response => {
              this.sales = response.data;
            });
          },
          type_document(documentID){
            if(documentID == 1){
              return '<span class="badge badge-info">BOLETA</span>';
            }
            else if(documentID == 2) {
              return '<span class="badge badge-primary">FACTURA</span>';
            }
            else {
              return '<span class="badge badge-warning">- - - - - - -</span>';
            }
          },
          payment_method(method){
            if(method == 1){
              return '<span class="badge badge-success">EFECTIVO</span>';
            }
            else if(method == 2) {
              return '<span class="badge badge-info">CREDITO</span>';
            }
            else if(method == 6) {
              return '<span class="badge badge-primary">DEBITO</span>';
            }
            else if(method == 8) {
              return '<span class="badge badge-warning">TRANSFERENCIA</span>';
            }
            else {
              return '<span class="badge badge-danger">----------</span>';
            }
          },
          informedSii(info){
            if(info == 0){
              return '<span class="badge badge-success">CORRECTO</span>';
            }
            else if(info == 1){
              return '<span class="badge badge-warning">ENVIADO</span>';
            }
            else {
              return '<span class="badge badge-danger">RECHAZADO</span>';
            }
          },
          discountType(type){
            if(type == 0){
              return '<span class="badge badge-danger">-----</span>';
            }
            else if(type == 1){
              return '<span class="badge badge-success">$ PESOS</span>';
            }
            else {
              return '<span class="badge badge-info">% PORCENTAJE</span>';
            }
          },
          discount(type, discount) {
            if(type == 0){
              return '---------';
            }
            else if(type == 1){
              return '$ '+this.number_format(discount)+'';
            }
            else {
              return discount+' %';
            }
          },
          TotalwhitOutDiscount(type, discount, amount) {
            if(type==0) {
              return this.number_format(amount);
            }
            else if(type==1){
              return this.number_format(parseInt(amount) + parseInt(discount));
            }
            else {
              let percentaje = 100;
              let percentajeRest = percentaje - discount;
              let percentajeRemaining = percentaje - percentajeRest;
              let result = amount * percentajeRemaining / percentajeRest;
              return this.number_format(parseInt(result) + parseInt(amount));
            }
          },
          ModalViewSale(saleID, ClientID) {
            jQuery.noConflict();
            axios.post('/sales/details/'+saleID)
            .then(response => {
              //console.log(response);
              this.saleDetails = response.data.sales_item;
              this.sale = response.data.sale;
              this.userData(ClientID);
            });

            $("#saleView").modal('show');
          },
          searchCliente() {
            if(this.inputValue == false){
              this.withoutResults = false;
              return this.getSales();
            }
            else {
              axios.post('/sales/get/'+this.filter+'/'+this.inputValue)
              .then(response => {
                if(response.data.total == 0){
                  this.sales = {};
                  this.withoutResults = true;
                }
                else {
                  this.withoutResults = false;
                  this.sales = response.data;
                }

              });
            }
          },
          DeleteSale(saleID, index) {
            this.$Progress.start()
            this.$swal.fire({
            title: 'Estas seguro?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminar!'
            }).then((result) => {
              if (result.value) {
                axios.delete('sales/delete/'+saleID)
                .then(() => {
                  this.sales.data.splice(index, 1);
                  this.$swal.fire(
                    'Eliminado!',
                    'La venta ha sido eliminada.',
                    'success'
                  )
                });

              }
            })
            this.$Progress.finish();
          }
       }
    }
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
.pagination {
  justify-content: center!important;
}

.tableBodyScroll tbody {
  display: block;
}
.tableBodyScroll th,td {
  padding: 8px 10px;
  width: 117px;
  box-sizing: border-box;
}
.tableBodyScroll tbody {
  height: 300px;
  overflow-y: scroll;
}
::-webkit-scrollbar {
   width: 8px;
}

::-webkit-scrollbar-thumb {
  border-radius: 30px;
  background: -webkit-gradient(linear,left top,left bottom,from(#ff8a00),to(#da1b60));
  box-shadow: inset 2px 2px 2px rgba(255,255,255,.25), inset -2px -2px 2px rgba(0,0,0,.25);
}

</style>
