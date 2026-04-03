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
          <div class="container ks-rows-section">
            <div class="row">
              <div class="col-md-12">
                <div class="card panel panel-default panel-table">
                  <div class="card-block" style="padding: 15px;">
                    <form class="form-inline" @submit.prevent="getBoxs">
                      <div class="form-group mb-2 mr-sm-2">
                        <input
                          v-model="date"
                          type="date"
                          class="form-control"
                        />
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button
                          type="submit"
                          class="btn btn-default btn-block"
                          name="button"
                        >
                          BUSCAR
                        </button>
                      </div>
                      <!-- <div class="input-group mb-2 mr-sm-2">
                        <button
                          @click="getBoxs()"
                          type="button"
                          class="btn btn-default btn-block"
                          name="button"
                        >
                          RECARGAR
                        </button>
                      </div> -->
                    </form>
                    <hr />
                    <div class="card">
                      <h5 class="card-header">
                        RESUMEN DEL
                        {{
                          this.date
                            ? dateFormat(this.date).format('LL').toUpperCase()
                            : dateFormat().format('LL').toUpperCase()
                        }}
                      </h5>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="card mb-3" style="max-width: 18rem;">
                              <div class="card-header">GANANCIA DEL DIA</div>
                              <div class="card-body">
                                <p class="card-text" style="font-size: 30px;">
                                  $ {{ CLP(this.revenue) }}
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="card mb-3" style="max-width: 18rem;">
                              <div class="card-header">GASTOS DEL DIA</div>
                              <div class="card-body">
                                <p class="card-text" style="font-size: 30px;">
                                  $ {{ CLP(this.expenses) }}
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="card mb-3" style="max-width: 18rem;">
                              <div class="card-header">HORA DE INICIO</div>
                              <div class="card-body">
                                <p class="card-text" style="font-size: 30px;">
                                  {{
                                    this.closing_time != '--:--'
                                      ? dateFormat(this.initial_time).format(
                                          'LT',
                                        )
                                      : '--:--'
                                  }}
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="card mb-3" style="max-width: 18rem;">
                              <div class="card-header">HORA DE CIERRE</div>
                              <div class="card-body">
                                <p class="card-text" style="font-size: 30px;">
                                  {{
                                    this.closing_time != '--:--'
                                      ? dateFormat(this.closing_time).format(
                                          'LT',
                                        )
                                      : '--:--'
                                  }}
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header">TURNOS</div>
                          <div class="card-body">
                            <div class="table-responsive">
                              <table class="table table-sm">
                                <thead class="thead-light">
                                  <tr>
                                    <th>TURNO</th>
                                    <th>FECHA</th>
                                    <th>HORA INICIO</th>
                                    <th>HORA FIN</th>
                                    <th>TOTAL VENDIDO</th>
                                    <th>GANANCIA</th>
                                    <th>ACCIÓN</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    :key="index"
                                    v-for="(item, index) in items"
                                  >
                                    <td>{{ ++index }}</td>
                                    <td>{{ formatDate(item.created_at) }}</td>
                                    <td>{{ formatTime(item.time_start) }}</td>
                                    <td>{{ formatTime(item.time_end) }}</td>
                                    <td> {{ CLP(item.total) }}</td>
                                    <td>$ {{ CLP(item.total_profit) }}</td>
                                    <td>
                                      <router-link
                                        class="btn btn-default btn-sm"
                                        :to="{
                                          name: 'box',
                                          params: {
                                            id: encryptString(item.id),
                                            date,
                                          },
                                        }"
                                      >
                                        VER DETALLES
                                      </router-link>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="row mt-3">
                          <div class="col-md-6">
                            <div class="card">
                            <div class="card-body">
                              <bar-chart
                              :chart-data="datacollection"
                              :options="options"
                              :height="200"
                            ></bar-chart>
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
</template>

<script>
import moment from 'moment'
import { number_format } from '../../utils/number_format'
import BarChart from "../../components/BarChartComponent.vue";


export default {
  components: {
    BarChart
  },
  data() {
    return {
      items: [],
      item: [],
      date: '',
      revenue: 0,
      expenses: 0,
      initial_time: '--:--',
      closing_time: '--:--',
      datacollection: null,
      options: {
        tooltips: {
          callbacks: {
            label: function (tooltipItem, data) {
              return (
                "$ " +
                tooltipItem.yLabel
                  .toFixed(0)
                  .toString()
                  .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
              );
            },
          },
        },
      },
    }
  },
  mounted() {
    moment.locale('es')
    this.getBoxs()
  },
  methods: {
    getSummary() {
      let rev = 0 // ganancia
      let exp = 0 // gastos
      let ini = '--:--' // hora inicio
      let clo = '--:--' // hora cierre
      let dataDate = [];
      let amount_revenue = [];
      let amount_total = [];
      let amount_expenses = [];

      this.items.map(function (item, index) {
        if (index === 0) {
          ini = item.time_start
        }
        dataDate.push('TURNO '+ (index + 1));
        amount_revenue.push(item.total_profit);
        amount_total.push(item.total);
        amount_expenses.push(item.expenses);

        rev = rev + parseInt(item.total_profit)
        exp = exp + parseInt(item.expenses)
        clo = item.time_end

      })

      this.datacollection = {
          labels: dataDate,
          datasets: [
            {
              label: "Ganancia",
              backgroundColor: "#42DE23",
              data: amount_revenue,
            },
            {
              label: "Venta Total",
              backgroundColor: "#CD23DE",
              data: amount_total,
            },
            {
              label: "Gastos",
              backgroundColor: "#3EDBE8",
              data: amount_expenses,
            },
          ],
      };

      this.revenue = rev ? rev : 0
      this.expenses = exp ? exp : 0
      this.initial_time = ini ? ini : '--:--'
      this.closing_time = clo ? clo : '--:--'
    },
    CLP(amount) {
      if (amount) {
        return number_format(String(amount))
      } else {
        return 0
      }
    },
    dateFormat(date) {
      return moment(date)
    },
    getBoxs() {
      axios.post('/get/boxs', { date: this.date }).then((response) => {
        this.items = response.data
        this.getSummary()
      })
    },
    encryptString(id) {
      return btoa(id)
    },
    formatDate(date) {
      return moment(date).format('ll')
    },
    formatTime(time) {
      return moment(time).format('hh:mm A')
    },
  },
}
</script>

<style scope>
.pagination {
  justify-content: center !important;
}
</style>

<style scoped>
.modal-body {
  max-height: 75vh;
  overflow-y: auto;
}
</style>
