<template>
  <div class="ks-column ks-page">
    <div class="ks-page-header">
      <section class="ks-title-and-subtitle">
        <div class="ks-title-block">
          <h3 class="ks-main-title">Tablero / Reportes</h3>
          <div class="ks-sub-title">Reportes generales.</div>
        </div>
        <button
          class="
            btn btn-outline-secondary
            ks-light ks-no-text ks-tabbed-sidebar-navigation-block-toggle
          "
          data-block-toggle=".ks-dashboard-tabbed-sidebar-sidebar"
        >
          <span class="ks-icon la la-bars"></span>
        </button>
      </section>
    </div>

    <table>
      <tr>
        <th>Producto</th>
        <th>Codigo</th>
        <th>Stock Actual</th>
        <th>Disponibilidad en dias</th>
      </tr>
    </table>

    <div class="ks-page-content">
      <div class="ks-page-content-body">
        <div class="ks-dashboard-tabbed-sidebar">
          <div class="ks-dashboard-tabbed-sidebar-widgets">
            <transition name="fade">
              <div class="row" v-if="isadmin">
                <div class="col-lg-3">
                  <div class="card panel panel-default ks-user-widget">
                    <div class="card-block">
                      <img
                        :src="this.photo ? this.photo : '/img/user_default.png'"
                        class="img-circle ks-avatar"
                      />
                      <div class="ks-name">
                        {{ this.name }} {{ this.last_name }}
                      </div>
                      <div class="ks-desc">
                        <div class="ks-nickname">{{ this.email }}</div>
                        <div class="ks-country">{{ this.city }}</div>
                      </div>
                      <div class="ks-rate">
                        <div class="ks-name">Plan: {{ userplanname }}</div>
                        <div
                          class="ks-due-date"
                          v-if="userplanname != 'Ilimitado'"
                        >
                          Hasta {{ DifForHumans(this.date_to_pay) }}
                        </div>
                      </div>
                      <div class="ks-balance">
                        Precio: $ {{ userplanprice }}
                      </div>
                      <a
                        href="#"
                        class="btn btn-primary"
                        v-if="userplanname != 'Ilimitado'"
                        >Atualizar plan</a
                      >
                    </div>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div
                    class="card panel panel-purple ks-amount-widget"
                    data-type="purple"
                  >
                    <div class="card-header">
                      VENTAS DEL DIA
                      <div class="ks-controls">
                        <a
                          href="#"
                          @click="salesOfTheDay"
                          class="ks-control ks-update"
                          ><font-awesome-icon :icon="['fas', 'redo-alt']"
                        /></a>
                      </div>
                    </div>
                    <div class="card-block panel-body">
                      <div class="ks-amount">$ {{ this.sales_of_day }}</div>
                      <div class="ks-progress">
                        <div class="progress ks-progress-xs">
                          <div
                            class="progress-bar bg-white"
                            role="progressbar"
                            v-bind:style="{ width: this.am_p + '%' }"
                            aria-valuenow="45"
                            aria-valuemin="0"
                            aria-valuemax="100"
                          ></div>
                        </div>
                      </div>
                      <div class="ks-desc ks-up">
                        {{ percentaje_of_day }}
                      </div>
                    </div>
                  </div>

                  <div
                    style="background-color: #9dd666; color: white"
                    class="card panel ks-amount-widget"
                    data-type="purple"
                  >
                    <div class="card-header">
                      BOLETAS EMITIDAS
                      <div class="ks-controls">
                        <a
                          href="#"
                          @click="salesOfTheDay"
                          class="ks-control ks-update"
                          ><font-awesome-icon :icon="['fas', 'redo-alt']"
                        /></a>
                      </div>
                    </div>
                    <div class="card-block panel-body">
                      <div class="ks-amount">{{ this.tickets_t }}</div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-3">
                  <div
                    class="card panel panel-info ks-amount-widget"
                    data-type="info"
                  >
                    <div class="card-header">
                      GASTOS DEL DIA
                      <div class="ks-controls">
                        <a href="#" class="ks-control ks-update"
                          ><font-awesome-icon :icon="['fas', 'redo-alt']"
                        /></a>
                      </div>
                    </div>
                    <div class="card-block panel-body">
                      <div class="ks-amount">$ {{ this.expenses_of_day }}</div>
                      <div class="ks-progress">
                        <div class="progress ks-progress-xs">
                          <div
                            class="progress-bar bg-white"
                            role="progressbar"
                            style="width: 0%"
                            aria-valuenow="60"
                            aria-valuemin="0"
                            aria-valuemax="100"
                          ></div>
                        </div>
                      </div>
                      <div class="ks-desc ks-down">
                        0 % mayor al dia anterior.
                      </div>
                    </div>
                  </div>

                  <div
                    style="background-color: #76abe3; color: white"
                    class="card panel ks-amount-widget"
                    data-type="purple"
                  >
                    <div class="card-header">
                      FACTURAS EMITIDAS
                      <div class="ks-controls">
                        <a
                          href="#"
                          @click="salesOfTheDay"
                          class="ks-control ks-update"
                          ><font-awesome-icon :icon="['fas', 'redo-alt']"
                        /></a>
                      </div>
                    </div>
                    <div class="card-block panel-body">
                      <div class="ks-amount">{{ this.invoices_t }}</div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div
                    class="card panel panel-success ks-amount-widget"
                    data-type="success"
                  >
                    <div class="card-header">
                      VENTAS PROCESADAS
                      <div class="ks-controls">
                        <a
                          href="#"
                          @click="salesOfTheDay"
                          class="ks-control ks-update"
                          ><font-awesome-icon :icon="['fas', 'redo-alt']"
                        /></a>
                      </div>
                    </div>
                    <div class="card-block panel-body">
                      <div class="ks-amount">{{ this.sales_total }}</div>
                      <div class="ks-progress">
                        <div class="progress ks-progress-xs">
                          <div
                            class="progress-bar bg-white"
                            role="progressbar"
                            v-bind:style="{ width: this.sa_p + '%' }"
                            aria-valuenow="30"
                            aria-valuemin="0"
                            aria-valuemax="100"
                          ></div>
                        </div>
                      </div>
                      <div class="ks-desc ks-up">
                        {{ this.percentaje_sales }}
                      </div>
                    </div>
                  </div>

                  <div
                    style="background-color: #be81fa; color: white"
                    class="card panel ks-amount-widget"
                    data-type="purple"
                  >
                    <div class="card-header">
                      COTIZACIONES EMITIDAS
                      <div class="ks-controls">
                        <a
                          href="#"
                          @click="salesOfTheDay"
                          class="ks-control ks-update"
                          ><font-awesome-icon :icon="['fas', 'redo-alt']"
                        /></a>
                      </div>
                    </div>
                    <div class="card-block panel-body">
                      <div class="ks-amount">{{ this.quotation_t }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
            <transition name="fade">
              <div class="row" v-if="isadmin">
                <div class="col-lg-3 col-md-6">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-purple"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="ks-icon-user ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.clients }} / {{ userclientmax }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        CLIENTES
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-green"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-users ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.providers }} / {{ userprovidersmax }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        PROVEEDORES
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-pink"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-outdent ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.items }} / {{ useritemsmax }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-down-left"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        ITEMS
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-orange"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-list-ul ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.categories }} / {{ usercatsmax }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        CATEGORIAS
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-3">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-pink"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-user-plus ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.sellers }} / {{ usersellersmax }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        VENDEDORES
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-3">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-purple"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-hourglass-end ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.invoices_t }} / {{ this.bsale_f_max }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        FACTURAS
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-3">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-pink"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-hourglass-end ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.tickets_t }} / {{ this.bsale_b_max }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        BOLETAS
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-3">
                  <div
                    class="card ks-widget-payment-simple-amount-item ks-green"
                  >
                    <div class="payment-simple-amount-item-icon-block">
                      <span class="la la-shopping-cart ks-icon"></span>
                    </div>

                    <div class="payment-simple-amount-item-body">
                      <div class="payment-simple-amount-item-amount">
                        <span class="ks-amount">
                          <div style="font-size: 15px">
                            {{ this.purchases }}
                          </div>
                        </span>
                        <span
                          class="ks-amount-icon ks-icon-circled-up-right"
                        ></span>
                      </div>
                      <div class="payment-simple-amount-item-description">
                        COMPRAS
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
            <div class="row">
              <div class="col-xl-12">
                <div class="card ks-card-widget ks-widget-payment-table">
                  <h5 class="card-header">VENTAS DEL MES</h5>
                  <div class="card-block">
                    <line-chart
                      :chart-data="datacollection"
                      :options="options"
                      :height="100"
                    ></line-chart>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-4">
                <div
                  class="
                    card
                    ks-card-widget ks-widget-payment-card-rate-details
                  "
                >
                  <h5 class="card-header">ITEMS MAS VENDIDOS</h5>
                  <div class="card-block">
                    <pie-chart :chart-data="dataPieCollection"></pie-chart>
                  </div>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="card ks-card-widget ks-widget-payment-earnings">
                  <h5 class="card-header">STOCK BAJO</h5>
                  <div class="card-block">
                    <table class="table table-sm table-hover mt-3">
                      <tbody>
                        <tr v-for="r in this.rank">
                          <td :title="r.name">
                            {{ r.name.substr(0, 17) }} <br />
                            {{ r.code }}
                          </td>
                          <td class="ks-amount">
                            <span
                              style="font-size: 11px"
                              class="badge badge-pill badge-danger-outline"
                              >{{ r.stock }}</span
                            >
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="card ks-card-widget ks-widget-payment-earnings">
                  <div class="card-block mt-4">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                      <!-- <li class="nav-item" title="SOLO PROCESADAS">
																    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">S/P</a>
																  </li> -->
                      <li class="nav-item" title="BOLETAS">
                        <a
                          class="nav-link active"
                          id="profile-tab"
                          data-toggle="tab"
                          href="#profile"
                          role="tab"
                          aria-controls="profile"
                          aria-selected="true"
                          >BOLETAS</a
                        >
                      </li>
                      <li class="nav-item" title="FACTURAS">
                        <a
                          class="nav-link"
                          id="contact-tab"
                          data-toggle="tab"
                          href="#contact"
                          role="tab"
                          aria-controls="contact"
                          aria-selected="false"
                          >FACTURAS</a
                        >
                      </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                      <div
                        class="tab-pane fade show active"
                        id="profile"
                        role="tabpanel"
                        aria-labelledby="profile-tab"
                      >
                        <div class="table-responsive">
                          <table class="table table-sm table-hover mt-2">
                            <thead>
                              <tr>
                                <th>MONTO</th>
                                <th>PDF</th>
                                <th>ESTADO</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="t in this.tickets">
                                <td>$ {{ number_format(t.amount) }}</td>
                                <td>
                                  <a
                                    @click="GenerarVoucher(t.id)"
                                    href="javascript:void(0)"
                                  >
                                    <font-awesome-icon
                                      :icon="['fas', 'file-pdf']"
                                      size="2x"
                                    />
                                  </a>
                                </td>
                                <td v-html="informedSii(t.informedSii)"></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div
                        class="tab-pane fade"
                        id="contact"
                        role="tabpanel"
                        aria-labelledby="contact-tab"
                      >
                        <div class="table-responsive">
                          <table class="table table-sm table-hover mt-2">
                            <thead>
                              <tr>
                                <th>MONTO</th>
                                <th>PDF</th>
                                <th>ESTADO</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="i in this.invoices">
                                <td>$ {{ number_format(i.amount) }}</td>
                                <td>
                                  <a
                                    @click="GenerarVoucher(i.id)"
                                    href="javascript:void(0)"
                                  >
                                    <font-awesome-icon
                                      :icon="['fas', 'file-pdf']"
                                      size="2x"
                                    />
                                  </a>
                                </td>
                                <td v-html="informedSii(i.informedSii)"></td>
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
</template>

<script>
import LineChart from "../components/LineChartComponent";
import PieChart from "../components/PieChartComponent";
import moment from "moment";
import { dispatchClientPrint } from "../utils/printAgent";

export default {
  components: {
    LineChart,
    PieChart,
  },
  data() {
    return {
      // sell
      sales_of_day: 0,
      expenses_of_day: 0,
      percentaje_of_day: 0,
      sales_total: 0,
      percentaje_sales: 0,
      am_p: 0,
      sa_p: 0,
      tickets_t: 0,
      bsale_f_max: 0,
      bsale_b_max: 0,
      invoices_t: 0,
      quotation_t: 0,
      purchases: 0,
      //profile
      name: "",
      last_name: "",
      email: "",
      photo: "",
      city: "",
      fk_id_plan: "",
      date_to_pay: "",
      // plan
      clients_max: 0,
      items_max: 0,
      prov_max: 0,
      cat_max: 0,
      price: 0,
      isadmin: false,
      clients: 0,
      providers: 0,
      items: 0,
      categories: 0,
      sellers: 0,
      best_sellers: {},
      rank: {},
      process: {},
      tickets: {},
      invoices: {},
      datacollection: null,
      dataPieCollection: null,
      options: {
        tooltips: {
          callbacks: {
            label: function (tooltipItem, data) {
              return (
                " $ " +
                tooltipItem.yLabel
                  .toFixed(0)
                  .toString()
                  .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
              );
            },
          },
        },
      },
    };
  },
  mounted() {
    this.ChartForDay();
    this.BestSellers();
    this.RankLessStock();
    this.LastSales();
    this.Counters();
    this.checkIsadmin();
    this.chargueProfile();
    this.salesOfTheDay();
    this.totalpurchases();
  },
  computed: {
    userclientmax() {
      return this.$store.getters.getPlanClientsMax;
    },
    usercatsmax() {
      return this.$store.getters.getPlanCatsMax;
    },
    usersellersmax() {
      return this.$store.getters.getPlanSellersMax;
    },
    useritemsmax() {
      return this.$store.getters.getPlanItemsMax;
    },
    userprovidersmax() {
      return this.$store.getters.getPlanProvidersMax;
    },
    userplanprice() {
      return this.$store.getters.getPlanPrice;
    },
    userplanname() {
      return this.$store.getters.getPlanName;
    },
  },
  methods: {
    async GenerarVoucher(sale_id) {
      const result = await this.$swal.fire({
        title: 'Motivo de reimpresion',
        input: 'text',
        inputPlaceholder: 'Ejemplo: reposicion por papel defectuoso',
        showCancelButton: true,
        confirmButtonText: 'Reimprimir',
        cancelButtonText: 'Cancelar',
      });

      if (!result.isConfirmed) {
        return;
      }

      const reason = (result.value || '').trim();
      if (!reason) {
        this.$swal.fire('Debe ingresar un motivo para reimprimir.', '', 'warning');
        return;
      }

      try {
        const response = await axios.post('/sales/' + sale_id + '/print/reprint-execute', {
          reason: reason,
          source: 'ADMIN_PANEL',
        });

        if (response.data.client_print) {
          try {
            await dispatchClientPrint(response.data.client_print);
            this.$swal.fire('Reimpresion enviada a impresora local', '', 'success');
          } catch (err) {
            this.$swal.fire(err.message || 'No se pudo imprimir en el agente local.', '', 'error');
          }
          return;
        }

        if (response.data.agent_dispatched) {
          this.$swal.fire('Reimpresion enviada a impresora local', '', 'success');
          return;
        }
        if (response.data.agent_attempted) {
          this.$swal.fire(response.data.agent_message || 'No se pudo enviar a impresora local', '', 'error');
          return;
        }

        this.$swal.fire({
          html:
            '<iframe src="' +
            response.data.preview_url +
            '#&zoom=180" width="100%" height="470" ></iframe>',
          showCloseButton: false,
          showCancelButton: false,
          focusConfirm: false,
          confirmButtonText: '<i class="fa fa-thumbs-up"></i> CERRAR',
          confirmButtonAriaLabel: 'Thumbs up, great!',
        });
      } catch (error) {
        const message =
          (error.response && error.response.data && error.response.data.message) ||
          'No fue posible ejecutar la reimpresion.';
        this.$swal.fire(message, '', 'error');
      }
    },
    totalpurchases() {
      axios.post("/total_purchases").then((response) => {
        this.purchases = response.data;
      });
    },
    maxbsaledocuments() {
      axios.post("/folios/33").then((response) => {
        this.bsale_f_max = response.data;
      });
      axios.post("/folios/39").then((response) => {
        this.bsale_b_max = response.data;
      });
    },
    salesOfTheDay() {
      axios.post("/sales_of_day").then((response) => {
        this.sales_of_day = response.data.current_day;
        this.expenses_of_day = response.data.expenses_day;
        this.sales_total = response.data.sales_current;
        this.percentaje_of_day = response.data.amount_percentaje;
        this.percentaje_sales = response.data.sales_percentaje;
        this.am_p = response.data.am_p;
        this.sa_p = response.data.sa_p;
        this.tickets_t = response.data.tickets;
        this.invoices_t = response.data.invoices;
        this.quotation_t = response.data.quotation;
      });
    },
    chargueProfile() {
      axios.post("/profile_config_get").then((response) => {
        //console.log(response);
        this.name = response.data.name;
        this.last_name = response.data.last_name;
        this.email = response.data.email;
        this.photo = response.data.photo;
        this.city = response.data.city;
        this.date_to_pay = response.data.date_to_pay;
      });
    },
    checkIsadmin() {
      axios.get("/check-auth").then((response) => {
        this.isadmin = response.data.isadmin;
      });
    },
    DifForHumans: function (d) {
      moment.locale("es");
      return moment(d).add(31, "days").format("MMM Do YY");
    },
    Counters() {
      axios.post("counters").then((response) => {
        this.clients = response.data.clients;
        this.providers = response.data.providers;
        this.items = response.data.items;
        this.categories = response.data.categories;
        this.sellers = response.data.sellers;
      });
    },
    LastSales() {
      axios.post("/documents").then((response) => {
        this.process = response.data.process;
        this.tickets = response.data.tickets;
        this.invoices = response.data.invoices;
      });
    },
    RankLessStock() {
      axios.post("/rank").then((response) => {
        this.rank = response.data;
      });
    },
    BestSellers() {
      axios.post("/best-sellers").then((response) => {
        this.best_sellers = response.data;
        let name = [];
        let sold = [];
        response.data.forEach(miFunction);
        function miFunction(elemento, indice) {
          name.push(elemento.name);
          sold.push(elemento.sold);
        }
        this.dataPieCollection = {
          labels: name,
          datasets: [
            {
              label: "Mas Vendidos",
              backgroundColor: [
                "#2FE956",
                "#E13C18",
                "#3255F2",
                "#29CDB1",
                "#E0C53D",
              ],
              data: sold,
            },
          ],
        };
      });
    },
    ChartForDay() {
      axios.post("/chart-day").then((response) => {
        let dataDate = [];
        let amount = [];
        response.data.forEach(miFunction);
        function miFunction(elemento, indice) {
          dataDate.push(elemento.date);
          amount.push(elemento.amount);
        }
        this.datacollection = {
          labels: dataDate,
          datasets: [
            {
              label: "Ventas",
              backgroundColor: "#6E4EE7",
              data: amount,
            },
          ],
        };
      });
    },
    informedSii(info) {
      if (info == 0) {
        return '<span class="badge badge-success">CORRECTO</span>';
      } else if (info == 1) {
        return '<span class="badge badge-warning">ENVIADO</span>';
      } else {
        return '<span class="badge badge-danger">RECHAZADO</span>';
      }
    },
    payment_method(method) {
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
      amount += ""; // por si pasan un numero en vez de un string
      amount = parseFloat(amount.replace(/[^0-9\.]/g, "")); // elimino cualquier cosa que no sea numero o punto

      decimals = decimals || 0; // por si la variable no fue fue pasada

      // si no es un numero o es igual a cero retorno el mismo cero
      if (isNaN(amount) || amount === 0) return parseFloat(0).toFixed(decimals);

      // si es mayor o menor que cero retorno el valor formateado como numero
      amount = "" + amount.toFixed(decimals);

      var amount_parts = amount.split("."),
        regexp = /(\d+)(\d{3})/;

      while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, "$1" + "," + "$2");

      return amount_parts.join(".");
    },
  },
};
</script>
<style media="screen">
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.9s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
