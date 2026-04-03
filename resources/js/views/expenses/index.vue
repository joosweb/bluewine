<template>
  <div class="ks-column ks-page mt-4">
    <!-- Modal -->
    <div class="modal fade" id="NewExpenseModal" tabindex="-1" role="dialog" aria-labelledby="NewExpenseModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" ref="titleExpenseModal" id="NewExpenseModalLabel">NUEVO GASTO</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <NewExpense ref="expense" @updatingScore="getExpenses(page = 1)"></NewExpense>
          </div>
        </div>
      </div>
    </div>

    <div class="ks-page-header">
        <section class="ks-title-and-subtitle">
            <div class="ks-title-block">
                <h3 class="ks-main-title"></h3>
                <div class="ks-sub-title">App / Gastos - Sección para crear, editar y eliminar gastos.</div>
            </div>
        </section>
    </div>
        <div class="ks-page-content">
          <div class="ks-page-content-body tables-page">
              <div class="ks-nav-body-wrapper">
                  <div class="container ks-rows-section">
                      <div class="row">
                          <div class="col-md-12" >
                              <div class="card panel panel-default panel-table">
                                <div class="card-block" style="padding:15px;">
                                    <form class="form-inline" @submit.prevent="formSearch">
                                    <select v-if="IsAdmin" v-tooltip="'FILTRAR POR:'" class="form-control mb-2 mr-sm-2" v-model="filter">
                                      <option value="T1.fk_user_run" selected>RUN</option>
                                      <option value="T2.name">NOMBRE</option>
                                      <option value="T1.amount">MONTO</option>
                                    </select>
                                    <div class="input-group" v-if="IsAdmin">
                                      <input v-tooltip="'TEXTO A BUSCAR:'"  type="text" v-model="text" class="form-control mb-2 mr-sm-2" placeholder="" autofocus>
                                    </div>
                                    <div class="input-group">
                                      <input  type="date" v-tooltip="'DESDE :'" v-model="from" class="form-control mb-2 mr-sm-2" placeholder="DESDE" autofocus>
                                    </div>
                                    <div class="input-group">
                                      <input  type="date" v-tooltip="'HASTA :'" v-model="to" class="form-control mb-2 mr-sm-2" placeholder="HASTA" autofocus>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button v-tooltip="'BUSCAR :'" type="submit" class="btn btn-default btn-block" id="searchBtn"><font-awesome-icon :icon="['fas', 'search']"  /></button>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button v-tooltip="'LIMPIAR FORMULARIO'" @click="thisFormReset()" type="reset" class="btn btn-default btn-block"><font-awesome-icon :icon="['fas', 'recycle']"  /></button>
                                    </div>
                                    <!-- <div class="input-group mb-2 mr-sm-2">
                                      <button v-tooltip="'NUEVO GASTO'" @click="NewExpenseModalOpen()" type="button" class="btn btn-default btn-block" name="button"><font-awesome-icon :icon="['fas', 'plus']"  /></button>
                                    </div> -->
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button v-tooltip="'RECARGAR TABLA'"  type="button" @click="getExpenses" class="btn btn-default btn-block" name="button"><font-awesome-icon :icon="['fas', 'redo-alt']"  /></button>
                                    </div>
                                    <div v-tooltip="'DESCARGAR EXCEL'" v-if="isExcel" class="input-group mb-2 mr-sm-2">
                                      <button @click="getEXCEL(filter,text,from,to)"  type="button" class="btn btn-default btn-block" name="button">
                                        <font-awesome-icon :icon="['fas', 'file-excel']"  />
                                      </button>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2" v-if="isPrint">
                                      <button title="IMPRIMIR"  type="button" @click="print()" class="btn btn-default btn-block" name="button"><font-awesome-icon :icon="['fas', 'print']"  /></button>
                                    </div>
                                  </form>
                                  <hr>
                                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                      <a class="nav-link active" @click="iconExport" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">TABLA</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                      <a class="nav-link" @click="iconExport" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">GRAFICOS Y REPORTES</a>
                                    </li>
                                  </ul>
                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                      <div class="table table-responsive mt-3">
                                        <table class="table table-bordered table-sm table-hover">
                                          <thead class="thead-light">
                                            <tr>
                                            <th>RUN</th>
                                            <th>INGRESADO POR</th>
                                            <th>MONTO</th>
                                            <th>COMENTARIO</th>
                                            <th>FECHA</th>
                                            <th v-show="!printing" v-if="IsAdmin">ACCIÓN</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                            <tr v-for="e in this.expenses.data">
                                              <td>
                                                <div class="badge badge-default">
                                                  {{ e.fk_user_run }}
                                                </div>
                                              </td>
                                              <td v-if="e.userID == adminID">
                                                <div class="badge badge-secondary">
                                                  {{ e.name }} {{ e.last_name }}
                                                </div>
                                              </td>
                                              <td v-else-if="e.userID != adminID">
                                                <div class="badge badge-info" >
                                                {{ e.name }} {{ e.last_name }}
                                                </div>
                                              </td>
                                              <td>
                                                <div class="badge badge-success">
                                                  $ {{ number_format(e.amount) }}
                                                </div>
                                              <td>
                                                <span v-tooltip.right-end="e.commentary">{{ e.commentary.substr(0,16) }}...</span>
                                              </td>
                                              <td>
                                                <div class="badge badge-primary">
                                                  {{ e.created_at }}
                                                </div>
                                              </td>
                                              <td v-show="!printing">
                                                <div class="btn-group" role="group" v-if="IsAdmin">
                                                <button  @click="EditExpense(e)" v-tooltip="'EDITAR'" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                                <!--<button title="DETALLES" @click="" class="mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>-->
                                                <button  @click="DeleteExpense(e.id)" v-tooltip="'ELIMINAR'" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                              </div>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                          <hr>
                                          <div class="float-left">
                                            <p>Mostrando datos de un total de <b>{{ this.expenses.total }}</b> registros</p>
                                          </div>
                                          <div class="float-right">
                                            <pagination v-if="searchMode" :limit="4" :data="expenses" @pagination-change-page="formSearch">
                                            </pagination>
                                            <pagination v-if="!searchMode" :limit="4" :data="expenses" @pagination-change-page="getExpenses">
                                            </pagination>
                                          </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                      <div v-if="!checked" class="mt-3">
                                        <div class="alert alert-info" role="alert">
                                          Genere un gráfico con los filtros de busqueda en el formulario.
                                        </div>
                                      </div>
                                      <div class="chart" v-else-if="checked">
                                        <hr>
                                        <div class="row">
                                          <div class="col-md-12">
                                            <div class="card">
                                              <div class="card-header">
                                                <div class="floa-left">
                                                  PERIODO : {{ this.from }}  /  {{ this.to}}
                                                </div>
                                                <div class="float-right">
                                                  TOTAL : $ {{ this.total }}
                                                </div>
                                              </div>
                                              <div class="card-body">
                                                <line-chart id="chart" :options="options" :chart-data="datacollection" :height="110"></line-chart>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-7 mt-3">
                                            <div class="card">
                                              <div class="card-header">
                                                MAYORES GASTOS EFECTUADOS
                                              </div>
                                              <div class="card-body">
                                                <div class="table-responsive">
                                                <table class="table table-sm">
                                                  <thead>
                                                    <tr>
                                                      <th>MONTO</th>
                                                      <th>USUARIO</th>
                                                      <th>FECHA</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody>
                                                    <tr v-for="ge in this.top_five">
                                                      <td>$ {{ number_format(ge.amount) }}</td>
                                                      <td v-if="ge.userID == adminID">
                                                        <div class="badge badge-success">
                                                          {{ ge.name }} {{ ge.last_name }}
                                                        </div>
                                                      </td>
                                                      <td v-else-if="ge.userID != adminID">
                                                        <div class="badge badge-info">
                                                        {{ ge.name }} {{ ge.last_name }}
                                                        </div>
                                                      </td>
                                                      <td>{{ dateFormat(ge.created_at) }}</td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </div>
                                             </div>
                                            </div>
                                          </div>
                                          <div class="col-md-5 mt-3">
                                            <div class="card">
                                              <div class="card-header">
                                                GASTOS POR USUARIO
                                              </div>
                                              <div class="card-body">
                                                <div class="table-responsive">
                                                  <table class="table table-sm">
                                                    <thead>
                                                      <tr>
                                                        <th>USUARIO</th>
                                                        <th>TOTAL</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <tr v-for="e in this.expenses_for_user">
                                                        <td v-if="e.fk_user_id == adminID">
                                                          <div class="badge badge-success">
                                                          {{ e.name }}  {{ e.last_name }}
                                                          </div>
                                                        </td>
                                                        <td v-else-if="e.fk_user_id != adminID">
                                                          <div class="badge badge-info">
                                                          {{ e.name }}  {{ e.last_name }}
                                                          </div>
                                                        </td>
                                                        <td>$ {{ number_format(e.amount) }}</td>
                                                      </tr>
                                                    </tbody>
                                                  </table>
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
                         </div>
                      </div>
                  </div>
              </div>
          </div>
          <br><br><br><br>
      </div>
  </div>
</template>

<script>
    import LineChart from '../../components/LineChartComponent';
    import NewExpense from './new-expense';
    import moment from 'moment';
    import $ from 'jquery'

    export default {
        components: {
          LineChart,
          NewExpense
        },
        data() {
          return  {
            // ADVANCED REPORTS
            top_five:{},
            expenses_for_user: {},
            total: 0,
            ////
            datacollection: {},
            expense:{},
            expenses: {},
            adminID: 0,
            checked:false,
            isPrint:false,
            isExcel:true,
            filter:'T1.fk_user_run',
            searchMode: false,
            text:'',
            from:'',
            to:'',
            printing: false,
            options: {
              responsive: true,
    				  tooltips: {
    				    callbacks: {
    				      label: function(tooltipItem, data) {
    				      return ' $ ' + tooltipItem.yLabel.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    				      }
    				    }
    				  }
    				},
          }
        },
        mounted() {
            //console.log('Component mounted.')
            this.getExpenses();
            jQuery.noConflict();
        },
        computed: {
          IsAdmin(){ //final output from here
            if(this.$store.getters.getUserType == 1) {
              return true;
            }
            else {
              return false;
            }
          },
        },
        methods: {
          DeleteExpense(id) {
            this.$swal.fire({
              title: 'Esta seguro?',
              text: "No podra revertir esto!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Si, Eliminar!'
              }).then((result) => {
                if (result.value) {
                  axios.delete('/expenses/'+id+'/delete').then(response=> {
                    //console.log(response);
                    this.$swal.fire(
                      'Eliminado!',
                      'El gasto ha sido eliminado.',
                      'success'
                    )
                  });
                  this.getExpenses();
                }
             })
          },
          EditExpense(e){
            this.expense = e;
            this.$refs.titleExpenseModal.innerHTML = 'EDITAR GASTO';
            this.$refs.expense.setProps(e.id, e.amount, e.commentary, e.created_at);
            $("#NewExpenseModal").modal("show");
          },
          NewExpenseModalOpen() {
            this.$refs.expense.ResetForm();
            this.$refs.titleExpenseModal.innerHTML = 'NUEVO GASTO';
            $("#NewExpenseModal").modal("show");
          },
          iconExport(){
            this.isPrint = !this.isPrint;
            this.isExcel = !this.isExcel;
          },
          dateFormat: function (d) {
            moment.locale('es');
            return moment(d).format('LLLL');
          },
          thisFormReset() {
            this.filter = 'T1.fk_user_run';
            this.text = '';
            this.from = '';
            this.to = '';
          },
          print: async function() {
            if(!this.from && !this.to){
              this.$swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: 'Debe ingresar un rango de fechas<br> antes de generar el documento!',
              })
              return;
            }
            $("#searchBtn").trigger("click");
            let timerInterval
              this.$swal.fire({
                title: 'Generando el reporte!',
                html: 'Generando en <b></b> milisegundos.',
                timer: 2000,
                timerProgressBar: true,
                onBeforeOpen: () => {
                  this.$swal.showLoading()
                  timerInterval = setInterval(() => {
                    const content = this.$swal.getContent()
                    if (content) {
                      const b = content.querySelector('b')
                      if (b) {
                        b.textContent = this.$swal.getTimerLeft()
                      }
                    }
                  }, 100)
                },
                onClose: () => {
                  printJS({printable: 'profile', css: '/libs/bootstrap/css/bootstrap.min.css', type: 'html', properties: ['prop1', 'prop2', 'prop3']});
                  clearInterval(timerInterval)
                }
              }).then((result) => {
                /* Read more about handling dismissals below */
            })
          },
          getEXCEL(filter,text,from,to) {
            if(!text){
              text = 0;
            }
            if(!from && !to){
              this.$swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: 'Debe ingresar un rango de fechas<br> antes de generar el documento!',
              })
              return;
            }
            window.open('/excel/'+filter+'/'+text+'/'+from+'/'+to);
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
          getExpenses(page = 1){
            this.searchMode = false;
            axios.post('/expenses?page='+page).then(response=> {
              //console.log(response);
              this.expenses = response.data.data;
              this.adminID = response.data.admin;
            });
          },
          formSearch(page = 1){
            let formdata = new FormData();
            formdata.append('filter', this.filter);
            formdata.append('text', this.text);
            formdata.append('from', this.from);
            formdata.append('to', this.to);
            axios.post('/expenses/search?page='+page, formdata).then(response=> {
              //console.log(response);
              if(response.data.search == false) {
                this.$swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  html: 'Debe ingresar un rango de fechas<br> antes de generar el reporte!',
                })
              }
              else {
                this.checked = true;
                this.searchMode = true;
                this.expenses = response.data.data;
                this.top_five = response.data.advanced_reports[0];
                this.total = response.data.advanced_reports[1];
                this.expenses_for_user = response.data.advanced_reports[2];
                let grafic = response.data.grafic;
                if(grafic) {
                  let dataDate = [];
        					let amount = [];
        					grafic.forEach(miFunction);
        					function miFunction(elemento, indice) {
        						 	dataDate.push(elemento.date);
        							amount.push(elemento.amount);
        					}
        					this.datacollection = {
        		        labels: dataDate,
        		        datasets: [
        		          {
        		            label: 'Gastos por dia',
                        borderColor: '#D62121',
                        pointBackgroundColor: 'white',
        		            data: amount
        		          },
        		        ]
        		      }
                }
              }
            }).catch((error) => {

              if (error.response) {
                  /*
                   * The request was made and the server responded with a
                   * status code that falls out of the range of 2xx
                   */
                  console.log(error.response.data);
                  console.log(error.response.status);
                  console.log(error.response.headers);
              } else if (error.request) {
                  /*
                   * The request was made but no response was received, `error.request`
                   * is an instance of XMLHttpRequest in the browser and an instance
                   * of http.ClientRequest in Node.js
                   */
                  console.log(error.request);
              } else {
                  // Something happened in setting up the request and triggered an Error
                  console.log('Error', error.message);
              }
              console.log(error.config);
            });
          }
        }
    }
</script>
<style media="screen">
.pagination {
  justify-content: left !important;
}
.tooltip {
  display: block !important;
  z-index: 10000;
}

.tooltip .tooltip-inner {
  background: #49304c;
  color: white;
  border-radius: 16px;
  padding: 5px 10px 4px;
}

.tooltip .tooltip-arrow {
  width: 0;
  height: 0;
  border-style: solid;
  position: absolute;
  margin: 5px;
  border-color: black;
  z-index: 1;
}

.tooltip[x-placement^="top"] {
  margin-bottom: 5px;
}

.tooltip[x-placement^="top"] .tooltip-arrow {
  border-width: 5px 5px 0 5px;
  border-left-color: transparent !important;
  border-right-color: transparent !important;
  border-bottom-color: transparent !important;
  bottom: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}

.tooltip[x-placement^="bottom"] {
  margin-top: 5px;
}

.tooltip[x-placement^="bottom"] .tooltip-arrow {
  border-width: 0 5px 5px 5px;
  border-left-color: transparent !important;
  border-right-color: transparent !important;
  border-top-color: transparent !important;
  top: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}

.tooltip[x-placement^="right"] {
  margin-left: 5px;
}

.tooltip[x-placement^="right"] .tooltip-arrow {
  border-width: 5px 5px 5px 0;
  border-left-color: transparent !important;
  border-top-color: transparent !important;
  border-bottom-color: transparent !important;
  left: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}

.tooltip[x-placement^="left"] {
  margin-right: 5px;
}

.tooltip[x-placement^="left"] .tooltip-arrow {
  border-width: 5px 0 5px 5px;
  border-top-color: transparent !important;
  border-right-color: transparent !important;
  border-bottom-color: transparent !important;
  right: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}

.tooltip.popover .popover-inner {
  background: #f9f9f9;
  color: black;
  padding: 24px;
  border-radius: 5px;
  box-shadow: 0 5px 30px rgba(black, .1);
}

.tooltip.popover .popover-arrow {
  border-color: #f9f9f9;
}

.tooltip[aria-hidden='true'] {
  visibility: hidden;
  opacity: 0;
  transition: opacity .15s, visibility .15s;
}

.tooltip[aria-hidden='false'] {
  visibility: visible;
  opacity: 1;
  transition: opacity .15s;
}
</style>
