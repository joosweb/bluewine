<template>
  <div class="ks-column ks-page mt-4">
    <div class="ks-page-header">
      <section class="ks-title-and-subtitle">
        <div class="ks-title-block">
          <h3 class="ks-main-title"></h3>
          <div class="ks-sub-title">App / Productos - Reporte de productos vendidos (cierre de caja).</div>
        </div>
      </section>
    </div>
    <div class="ks-page-content">
      <div class="ks-page-content-body tables-page">
        <div class="ks-nav-body-wrapper">
          <div class="container ks-rows-section">
            <div class="row">
              <div class="col-md-12">
                <div class="card panel panel-default panel-table">
                  <div class="card-block" style="padding:15px;">
                    <form class="form-inline" @submit.prevent="getProducts">
                      <div class="input-group">
                        <input v-tooltip="'BUSCAR (CODIGO O NOMBRE):'" type="text" v-model="search" class="form-control mb-2 mr-sm-2" placeholder="Producto / código">
                      </div>
                      <div class="input-group">
                        <input type="date" v-tooltip="'DESDE :'" v-model="from" class="form-control mb-2 mr-sm-2" placeholder="DESDE">
                      </div>
                      <div class="input-group">
                        <input type="date" v-tooltip="'HASTA :'" v-model="to" class="form-control mb-2 mr-sm-2" placeholder="HASTA">
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button v-tooltip="'HOY'" type="button" @click="setToday" class="btn btn-default btn-block">
                          HOY
                        </button>
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button v-tooltip="'BUSCAR'" :disabled="bussy" type="submit" class="btn btn-default btn-block">
                          <font-awesome-icon v-show="!bussy" :icon="['fas', 'search']" />
                          <font-awesome-icon v-show="bussy" icon="spinner" spin />
                        </button>
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button v-tooltip="'LIMPIAR FORMULARIO'" @click="resetForm()" type="reset" class="btn btn-default btn-block">
                          <font-awesome-icon :icon="['fas', 'recycle']" />
                        </button>
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button v-tooltip="'IMPRIMIR REPORTE'" type="button" @click="printReport()" class="btn btn-default btn-block">
                          <font-awesome-icon :icon="['fas', 'print']" />
                        </button>
                      </div>
                    </form>

                    <div class="row mb-2" v-if="rangeApplied">
                      <div class="col-md-3">
                        <div class="alert alert-primary" style="margin-bottom:5px;">
                          <strong>VENTAS:</strong> {{ number_format(sales_count) }}
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="alert alert-info" style="margin-bottom:5px;">
                          <strong>PRODUCTOS:</strong> {{ number_format(total_lines) }}
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="alert alert-warning" style="margin-bottom:5px;">
                          <strong>UNIDADES:</strong> {{ number_format(total_quantity) }}
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="alert alert-success" style="margin-bottom:5px;">
                          <strong>TOTAL: $</strong> {{ number_format(total_amount) }}
                        </div>
                      </div>
                    </div>

                    <hr>

                    <div id="printableArea">
                      <div class="print-header" v-if="rangeApplied">
                        <h4>REPORTE DE PRODUCTOS VENDIDOS</h4>
                        <p>Periodo: <strong>{{ formatDate(from) }}</strong> al <strong>{{ formatDate(to) }}</strong></p>
                      </div>
                      <div class="table table-responsive mt-3">
                        <table class="table table-bordered table-sm table-hover">
                          <thead class="thead-light">
                            <tr>
                              <th>#</th>
                              <th>CÓDIGO</th>
                              <th>PRODUCTO</th>
                              <th class="text-right">CANTIDAD</th>
                              <th class="text-right">PRECIO PROMEDIO</th>
                              <th class="text-right">SUBTOTAL</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(p, index) in products" :key="(p.code || 'noc') + '-' + index">
                              <td>{{ index + 1 }}</td>
                              <td>{{ p.code || '-' }}</td>
                              <td>{{ p.name_item }}</td>
                              <td class="text-right">{{ number_format(p.total_quantity) }}</td>
                              <td class="text-right">$ {{ number_format(Math.round(p.avg_price)) }}</td>
                              <td class="text-right">$ {{ number_format(Math.round(p.subtotal)) }}</td>
                            </tr>
                            <tr v-if="!products.length && !bussy">
                              <td colspan="6">
                                <div class="alert alert-info mb-0">
                                  Sin resultados para el rango seleccionado.
                                </div>
                              </td>
                            </tr>
                          </tbody>
                          <tfoot v-if="products.length">
                            <tr>
                              <th colspan="3" class="text-right">TOTALES</th>
                              <th class="text-right">{{ number_format(total_quantity) }}</th>
                              <th class="text-right">--</th>
                              <th class="text-right">$ {{ number_format(Math.round(total_amount)) }}</th>
                            </tr>
                          </tfoot>
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
</template>

<script>
import moment from 'moment'
import VueSweetalert2 from 'vue-sweetalert2'
import Vue from 'vue'
import { dispatchClientPrint } from '../../utils/printAgent'
Vue.use(VueSweetalert2)

export default {
  data() {
    return {
      bussy: false,
      from: '',
      to: '',
      search: '',
      products: [],
      total_quantity: 0,
      total_amount: 0,
      total_lines: 0,
      sales_count: 0,
      rangeApplied: false,
    }
  },
  created() {
    // Cargar por defecto el día de hoy.
    this.setToday();
    this.getProducts();
  },
  methods: {
    setToday() {
      const today = moment().format('YYYY-MM-DD');
      this.from = today;
      this.to = today;
    },
    formatDate(value) {
      if (!value) return '-';
      return moment(value).format('DD/MM/YYYY');
    },
    resetForm() {
      this.search = '';
      this.from = '';
      this.to = '';
    },
    number_format(amount, decimals) {
      amount += '';
      amount = parseFloat(amount.replace(/[^0-9\.\-]/g, ''));
      decimals = decimals || 0;
      if (isNaN(amount) || amount === 0) {
        return parseFloat(0).toFixed(decimals);
      }
      amount = '' + amount.toFixed(decimals);
      const parts = amount.split('.');
      const regexp = /(\d+)(\d{3})/;
      while (regexp.test(parts[0])) {
        parts[0] = parts[0].replace(regexp, '$1' + ',' + '$2');
      }
      return parts.join('.');
    },
    getProducts() {
      // Validación: si se entrega un rango, ambas fechas son requeridas.
      if ((this.from && !this.to) || (!this.from && this.to)) {
        this.$swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Debe seleccionar fecha DESDE y HASTA, o dejar vacío para usar el día de hoy.',
        });
        return;
      }
      this.bussy = true;
      const formData = new FormData();
      if (this.from) formData.append('from', this.from);
      if (this.to) formData.append('to', this.to);
      if (this.search) formData.append('search', this.search);

      axios.post('/reports/products', formData)
        .then((response) => {
          this.products = response.data.products || [];
          this.total_quantity = response.data.total_quantity || 0;
          this.total_amount = response.data.total_amount || 0;
          this.total_lines = response.data.total_lines || 0;
          this.sales_count = response.data.sales_count || 0;
          // El backend siempre normaliza a un rango, lo reflejamos en pantalla.
          if (response.data.from) this.from = moment(response.data.from).format('YYYY-MM-DD');
          if (response.data.to) this.to = moment(response.data.to).format('YYYY-MM-DD');
          this.rangeApplied = true;
          this.bussy = false;
        })
        .catch((error) => {
          this.bussy = false;
          const msg = (error.response && error.response.data && error.response.data.message)
            ? error.response.data.message
            : 'No fue posible obtener el reporte de productos.';
          this.$swal.fire(msg, '', 'error');
        });
    },
    async printReport() {
      if (!this.products.length) {
        this.$swal.fire('No hay datos para imprimir', '', 'info');
        return;
      }

      // Mismos filtros que la consulta visible. El backend reusa la query y
      // arma el ESC/POS para que el navegador lo despache al agente local.
      const formData = new FormData();
      if (this.from) formData.append('from', this.from);
      if (this.to) formData.append('to', this.to);
      if (this.search) formData.append('search', this.search);

      try {
        const { data } = await axios.post('/reports/products/print', formData);

        if (!data || !data.client_print) {
          this.$swal.fire(
            (data && data.message) || 'El agente de impresion no esta disponible.',
            '',
            'warning'
          );
          return;
        }

        await dispatchClientPrint(data.client_print);
        this.$swal.fire({
          position: 'top-center',
          icon: 'success',
          title: 'Reporte enviado a impresora',
          showConfirmButton: false,
          timer: 1500,
        });
      } catch (error) {
        const msg =
          (error.response && error.response.data && error.response.data.message) ||
          error.message ||
          'No fue posible imprimir el reporte.';
        this.$swal.fire(msg, '', 'error');
      }
    },
  }
}
</script>

<style scoped>
.text-right { text-align: right; }
.print-header { margin-top: 10px; }
</style>
