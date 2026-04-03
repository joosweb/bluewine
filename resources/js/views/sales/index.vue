<template>
  <div class="" v-if="hidden">
    <!-- Modal -->
    <div class="modal fade" id="saleView" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">DETALLES DE LA VENTA</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">CARRITO</a>
                <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">DETALLES</a>
              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="table-responsive mt-3">
                  <table class="table table-sm" style="">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                  </table>
                  <table class="table tableBodyScroll">
                    <tbody>
                      <tr v-for="sale, index in this.saleDetails" :key="sale.id">
                        <td>{{ sale.name_item }}</td>
                        <td>$ {{ number_format(sale.price) }}</td>
                        <td>{{ sale.quantity }}</td>
                        <td>$ {{ number_format(sale.price * sale.quantity) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-for="s in this.sale" class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="table-responsive mt-3">
                  <table class="table table-sm" style="table-layout:fixed;">
                    <thead>
                      <tr>
                        <td>TIPO DE DOCUMENTO</td>
                        <td v-html="type_document(s.type_document)"></td>
                      </tr>
                      <tr>
                        <td>METODO DE PAGO</td>
                        <td v-html="payment_method(s.payment_method)"></td>
                      </tr>
                      <tr>
                        <td v-if="clientName" colspan="2">
                          CLIENTE ASOCIADO
                          <hr>
                          <button @click="clientView = !clientView" type="button" name="button">DETALLES DEL CLIENTE</button>
                          <hr>
                            <table class="table table-sm" v-if="clientView">
                              <tr>
                                <td>NOMBRE</td>
                                <td>{{ clientName }}</td>
                              </tr>
                              <tr>
                                <td>APELLIDOS</td>
                                <td>{{ clientLast_name }}</td>
                              </tr>
                              <tr>
                                <td>EMAIL</td>
                                <td>{{ clientEmail }}</td>
                              </tr>
                              <tr>
                                <td>TELEFONO</td>
                                <td>{{ clientPhone }}</td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                      <tr>
                        <td>DESCUENTO</td>
                        <td>{{ s.discount }} %</td>
                      </tr>
                      <tr>
                        <td>TOTAL</td>
                        <td>$ {{ number_format(s.amount) }}</td>
                      </tr>
                      <tr v-if="s.folio != 0">
                        <td>FOLIO</td>
                        <td>{{ s.folio }}</td>
                      </tr>
                      <tr>
                        <td>FECHA</td>
                        <td>{{ DifForHumans(s.created_at) }}</td>
                      </tr>
                      <tr v-if="s.folio != 0">
                        <td>DOCUMENTO</td>
                        <td>
                          <a v-if="s.urlpdf != 0" class="btn btn-default btn-sm" :href="s.urlpdf" target="_blank">
                            VER DOCUMENTO <font-awesome-icon :icon="['fas', 'file-pdf']" />
                          </a>
                          <div v-else="s.urlpdf != 0" title="SIN DOCUMENTO">
                            S/D
                          </div>
                        </td>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12 col-xs-12 col- mt-3">
        <div class="card panel panel-default panel-table">
          <div class="card-block" style="padding:15px;">
              <form class="form-inline" @submit.prevent="FormSearch">
              <label class="sr-only" for="inlineFormInputName2">Name</label>
              <select class="form-control mb-2 mr-sm-2" v-model="filter">
                <option value="T1.folio" selected>FOLIO</option>
                <!--<option value="T2.name">NOMBRE</option>-->
              </select>
              <div class="input-group mb-2 mr-sm-2">
                <input v-model="inputValue" type="text" class="form-control" autofocus>
              </div>
              <div class="input-group mb-2 mr-sm-2">
                <button type="button" class="btn btn-default btn-block" @click="getSales"><font-awesome-icon :icon="['fas', 'sync']" /></button>
              </div>
              <div class="input-group mb-2 mr-sm-2">
                <button v-tooltip="'BUSCAR :'" :disabled="this.bussy" type="submit" class="btn btn-default btn-block" id="searchBtn">
                  <font-awesome-icon v-show="!this.bussy" :icon="['fas', 'search']"  />
                  <font-awesome-icon v-show="this.bussy" icon="spinner" spin />
                </button>
              </div>
            </form>
            <hr>
            <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
              <thead class="thead-light">
                <tr>
                  <th v-if="!hidden">RUN CLIENTE</th>
                  <th v-if="!hidden">VENDEDOR</th>
                  <th>FOLIO</th>
                  <th>ESTADO</th>
                  <th>T. DOC</th>
                  <th>TOTAL</th>
                  <th v-if="!hidden">SIN DESCUENTO</th>
                  <th v-if="!hidden">FECHA</th>
                  <th>ACCIÓN</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="sale, index in SalesData" :title="sale.name + sale.last_name ? sale.name + sale.last_name: '--'" :key="sale.id">
                  <td v-if="!hidden">{{ sale.run ? sale.run : '-----------'}}</button></td>
                  <td v-if="!hidden">
                    <div v-if="sale.fk_user_id == adminID"  class="badge badge-primary">
                      {{ sale.vname }} {{ sale.vlast_name}}
                    </div>
                    <div v-else-if="sale.fk_user_id != adminID"  class="badge badge-info">
                      {{ sale.vname }} {{ sale.vlast_name}}
                    </div>
                </td>
                  <td>{{ sale.folio }}</td>
                  <td v-html="informedSii(sale.informedSii)"></td>
                  <td v-html="type_document(sale.type_document)"></td>
                  <td >{{ number_format(sale.amount) }}</td>
                  <td v-if="!hidden">$ {{ TotalwhitOutDiscount(sale.discount_type, sale.discount, sale.amount) }}</td>
                  <td v-if="!hidden">{{ DifForHumans(sale.created_at) }}</td>
                  <td>
                    <div class="btn-group" role="group">
                    <button title="GENERAR VOUCHER" @click="GenerarVoucher(sale.id)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'file-invoice']" /></button>
                    <button v-if="IsAdmin" title="BITACORA IMPRESION" @click="ShowPrintLogs(sale.id)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'history']" /></button>
                    <button title="DETALLES" @click="ModalViewSale(sale.id, sale.fk_cliente_id)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>
                    <button v-if="IsAdmin" title="ELIMINAR" @click="DeleteSale(sale.id, index)" type="button" class="btn btn-default btn-sm" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
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
            <pagination v-if="!searchMode" :limit="6" :data="sales" @pagination-change-page="getSales">
                <span slot="prev-nav">&lt;</span>
	              <span slot="next-nav">&gt;</span>
            </pagination>
            <pagination v-if="searchMode" :limit="6" :data="sales" @pagination-change-page="FormSearch">
                <span slot="prev-nav">&lt;</span>
	              <span slot="next-nav">&gt;</span>
            </pagination>
            </div>
          </div>
       </div>
    </div>
  </div>
  <div class="ks-column ks-page mt-4" v-else-if="!hidden">
    <!-- Modal -->
    <div class="modal fade" id="saleView" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">DETALLES DE LA VENTA</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">CARRITO</a>
                <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">DETALLES</a>
              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="table-responsive mt-3">
                  <table class="table table-sm" style="">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                  </table>
                  <table class="table tableBodyScroll">
                    <tbody>
                      <tr v-for="sale, index in this.saleDetails" :key="sale.id">
                        <td>{{ sale.name_item }}</td>
                        <td>$ {{ number_format(sale.price) }}</td>
                        <td>{{ sale.quantity }}</td>
                        <td>$ {{ number_format(sale.price * sale.quantity) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-for="s in this.sale" :key="s.id" class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="table-responsive mt-3">
                  <table class="table table-sm" style="table-layout:fixed;">
                    <thead>
                      <tr>
                        <td>TIPO DE DOCUMENTO</td>
                        <td v-html="type_document(s.type_document)"></td>
                      </tr>
                      <tr>
                        <td>METODO DE PAGO</td>
                        <td v-html="payment_method(s.payment_method)"></td>
                      </tr>
                      <tr>
                        <td v-if="clientName" colspan="2">
                          CLIENTE ASOCIADO
                          <hr>
                          <button @click="clientView = !clientView" type="button" name="button">DETALLES DEL CLIENTE</button>
                          <hr>
                            <table class="table table-sm" v-if="clientView">
                              <tr>
                                <td>NOMBRE</td>
                                <td>{{ clientName }}</td>
                              </tr>
                              <tr>
                                <td>APELLIDOS</td>
                                <td>{{ clientLast_name }}</td>
                              </tr>
                              <tr>
                                <td>EMAIL</td>
                                <td>{{ clientEmail }}</td>
                              </tr>
                              <tr>
                                <td>TELEFONO</td>
                                <td>{{ clientPhone }}</td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                      <tr>
                        <td>DESCUENTO</td>
                        <td>{{ s.discount }} %</td>
                      </tr>
                      <tr>
                        <td>TOTAL</td>
                        <td>$ {{ number_format(s.amount) }}</td>
                      </tr>
                      <tr v-if="s.folio != 0">
                        <td>FOLIO</td>
                        <td>{{ s.folio }}</td>
                      </tr>
                      <tr>
                        <td>FECHA</td>
                        <td>{{ DifForHumans(s.created_at) }}</td>
                      </tr>
                      <tr v-if="s.folio != 0">
                        <td>DOCUMENTO</td>
                        <td>
                          <a class="btn btn-default btn-sm" :href="s.urlpdf" target="_blank">
                            VER DOCUMENTO <font-awesome-icon :icon="['fas', 'file-pdf']" />
                          </a>
                        </td>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
          </div>
        </div>
      </div>
    </div>
    <div class="ks-page-header">
        <section class="ks-title-and-subtitle">
            <div class="ks-title-block">
                <h3 class="ks-main-title"></h3>
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
                                    <form class="form-inline" @submit.prevent="FormSearch">
                                    <label class="sr-only" for="inlineFormInputName2">Name</label>
                                    <select class="form-control mb-2 mr-sm-2" v-model="filter">
                                      <option value="T1.folio">FOLIO</option>
                                      <!--<option value="T2.name">NOMBRE</option>-->
                                    </select>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <input v-model="inputValue" type="text" class="form-control" autofocus>
                                    </div>
                                    <div class="input-group">
                                      <input  type="date" v-tooltip="'DESDE :'" v-model="from" class="form-control mb-2 mr-sm-2" placeholder="DESDE" autofocus>
                                    </div>
                                    <div class="input-group">
                                      <input  type="date" v-tooltip="'HASTA :'" v-model="to" class="form-control mb-2 mr-sm-2" placeholder="HASTA" autofocus>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button v-tooltip="'BUSCAR :'" :disabled="this.bussy" type="submit" class="btn btn-default btn-block" id="searchBtn">
                                        <font-awesome-icon v-show="!this.bussy" :icon="['fas', 'search']"  />
                                        <font-awesome-icon v-show="this.bussy" icon="spinner" spin />
                                      </button>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button type="button" @click="getSales" :disabled="this.bussy" class="btn btn-default btn-block"><font-awesome-icon :icon="['fas', 'redo-alt']" /></button>
                                    </div>
                                  </form>
                                  <div class="row mb-2">
                                    <div class="col-md-4">
                                      <div class="alert alert-primary" style="margin-bottom:5px;">
                                        <strong>IMPRESIONES TOTALES:</strong> {{ number_format(printMarkers.total_prints) }}
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="alert alert-warning" style="margin-bottom:5px;">
                                        <strong>REIMPRESIONES:</strong> {{ number_format(printMarkers.total_reprints) }}
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="alert alert-info" style="margin-bottom:5px;">
                                        <strong>VENTAS CON REIMPRESION:</strong> {{ number_format(printMarkers.sales_with_reprint) }}
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row mb-2" v-if="reprintDashboard.top_reprinters && reprintDashboard.top_reprinters.length">
                                    <div class="col-md-12">
                                      <div class="card">
                                        <div class="card-header">TOP 5 REIMPRESIONES (USUARIOS)</div>
                                        <div class="card-body" style="padding:10px;">
                                          <span
                                            v-for="(top, idx) in reprintDashboard.top_reprinters"
                                            :key="'top-r-' + idx"
                                            class="badge badge-default mr-1"
                                            style="font-size:12px; padding:8px;"
                                          >
                                            #{{ idx + 1 }} {{ (top.user_name && top.user_name.trim()) ? top.user_name : ('ID ' + top.user_id) }}: {{ top.reprint_count }}
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row mb-2" v-if="reprintDashboard.user_sales && reprintDashboard.user_sales.length">
                                    <div class="col-md-12">
                                      <div class="card">
                                        <div class="card-header">PERSONAS QUE REIMPRIMIERON Y BOLETAS INVOLUCRADAS</div>
                                        <div class="card-body" style="padding:10px;">
                                          <div class="table-responsive" style="max-height:260px; overflow:auto;">
                                            <table class="table table-sm table-bordered" style="table-layout:fixed; font-size:12px;">
                                              <thead>
                                                <tr>
                                                  <th>USUARIO</th>
                                                  <th>BOLETA</th>
                                                  <th>REIMP.</th>
                                                  <th>ULTIMA</th>
                                                  <th>ACCIONES</th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <tr v-for="s in reprintDashboard.user_sales" :key="'sale-r-' + s.user_id + '-' + s.sale_id">
                                                  <td style="white-space:nowrap;">{{ s.user_name }}</td>
                                                  <td>
                                                    <button
                                                      @click="ModalViewSale(s.sale_id, s.client_id)"
                                                      class="btn btn-default btn-sm"
                                                      type="button"
                                                      :title="'Ver detalle de venta #' + s.sale_id"
                                                    >
                                                      {{ s.folio ? ('Folio ' + s.folio) : ('Venta #' + s.sale_id) }}
                                                    </button>
                                                  </td>
                                                  <td>{{ number_format(s.reprint_events) }}</td>
                                                  <td style="white-space:nowrap;">{{ formatLogDate(s.last_reprint_at) }}</td>
                                                  <td>
                                                    <button
                                                      @click="ShowPrintLogs(s.sale_id)"
                                                      class="btn btn-default btn-sm"
                                                      type="button"
                                                      :title="'Ver bitacora de venta #' + s.sale_id"
                                                    >
                                                      <font-awesome-icon :icon="['fas', 'history']" />
                                                    </button>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <hr>
                                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">TABLA</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">GRAFICOS Y REPORTES</a>
                                    </li>
                                  </ul>
                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                      <div class="table-responsive mt-3">
                                      <table class="table table-sm table-bordered table-hover">
                                        <thead class="thead-light">
                                          <tr>
                                            <th>FOLIO</th>
                                            <th>VENDEDOR</th>
                                            <th>ESTADO</th>
                                            <th>T. DOC</th>
                                            <th v-if="!hidden">TOTAL</th>
                                            <th v-if="!hidden">SIN DESC.</th>
                                            <th v-if="!hidden">FECHA</th>
                                            <th>ACCIÓN</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr v-for="sale, index in SalesData" :key="sale.id">
                                            <td >{{ sale.folio ? sale.folio : '-----------'}}</button></td>
                                            <td>
                                              <div v-if="sale.fk_user_id == adminID" :title="sale.vname + ' ' + sale.vlast_name" class="badge badge-primary">
                                                {{ sale.vname }} {{ sale.vlast_name && sale.vlast_name.substr(0,2) }}...
                                              </div>
                                              <div v-else="sale.fk_user_id != adminID" :title="sale.vname + ' ' + sale.vlast_name" class="badge badge-info">
                                                {{ sale.vname }} {{ sale.vlast_name && sale.vlast_name.substr(0,2) }}...
                                              </div>
                                            </td>
                                            <td v-html="informedSii(sale.informedSii)"></td>
                                            <td v-html="type_document(sale.type_document)"></td>
                                            <td v-if="!hidden">{{ number_format(sale.amount) }}</td>
                                            <td v-if="!hidden">$ {{ TotalwhitOutDiscount(sale.discount_type, sale.discount, sale.amount) }}</td>
                                            <td v-if="!hidden">{{ DifForHumans(sale.created_at) }}</td>
                                            <td>
                                              <div class="btn-group" role="group">
                                              <button title="GENERAR VOUCHER" @click="GenerarVoucher(sale.id)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'file-invoice']" /></button>
                                              <button v-if="IsAdmin" title="BITACORA IMPRESION" @click="ShowPrintLogs(sale.id)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'history']" /></button>
                                              <button title="DETALLES" @click="ModalViewSale(sale.id, sale.fk_cliente_id)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>
                                              <button v-if="IsAdmin" title="ELIMINAR" @click="DeleteSale(sale.id, index)" type="button" class="btn btn-default btn-sm" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
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
                                      <pagination v-if="!searchMode" :limit="6" :data="sales" @pagination-change-page="getSales">
                                          <span slot="prev-nav">&lt;</span>
                          	              <span slot="next-nav">&gt;</span>
                                      </pagination>
                                      <pagination v-if="searchMode" :limit="6" :data="sales" @pagination-change-page="FormSearch">
                                          <span slot="prev-nav">&lt;</span>
                          	              <span slot="next-nav">&gt;</span>
                                      </pagination>
                                      </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                      <div class="alert alert-info mt-3" role="alert" v-if="!this.total_amount_from_range">
                                        No se ha generado ningun reporte, genere uno completando el formulario de arriba.
                                      </div>
                                      <transition name="fade">
                                          <div class="row mt-3" v-show="this.total_amount_from_range">
                                            <div class="col-md-12">
                                              <div class="card">
                                                <div class="card-header">
                                                  RESUMEN DEL PERIODO {{ this.from }} | {{ this.to }}
                                                </div>
                                                <div class="card-body">
                                                  <div class="row">
                                                    <div class="col-lg-3 col-md-6">
                        																<div class="card ks-widget-payment-simple-amount-item ks-purple">
                        																		<div class="payment-simple-amount-item-icon-block">
                        																				<span class="la la-shopping-cart ks-icon"></span>
                        																		</div>

                        																		<div class="payment-simple-amount-item-body">
                        																				<div class="payment-simple-amount-item-amount">
                        																						<span class="ks-amount">
                        																							<div style="font-size:15px;">{{ this.marcators.carritos_procesados }}</div>
                        																						</span>
                        																						<span class="ks-amount-icon ks-icon-circled-up-right"></span>
                        																				</div>
                        																				<div class="payment-simple-amount-item-description">
                        																						CARRITOS <br> PROCESADOS
                        																				</div>
                        																		</div>
                        																</div>
                        														</div>
                                                    <div class="col-lg-3 col-md-6">
                        																<div class="card ks-widget-payment-simple-amount-item ks-green">
                        																		<div class="payment-simple-amount-item-icon-block">
                        																				<span class="la la-file-text ks-icon"></span>
                        																		</div>

                        																		<div class="payment-simple-amount-item-body">
                        																				<div class="payment-simple-amount-item-amount">
                        																						<span class="ks-amount">
                        																							<div style="font-size:15px;">{{ this.marcators.correctos }}</div>
                        																						</span>
                        																						<span class="ks-amount-icon ks-icon-circled-up-right"></span>
                        																				</div>
                        																				<div class="payment-simple-amount-item-description">
                                                                    DOCUMENTOS <br>
                        																						CORRECTOS
                        																				</div>
                        																		</div>
                        																</div>
                        														</div>
                                                    <div class="col-lg-3 col-md-6">
                        																<div class="card ks-widget-payment-simple-amount-item ks-pink">
                        																		<div class="payment-simple-amount-item-icon-block">
                        																				<span class="la la-file-text ks-icon"></span>
                        																		</div>

                        																		<div class="payment-simple-amount-item-body">
                        																				<div class="payment-simple-amount-item-amount">
                        																						<span class="ks-amount">
                        																							<div style="font-size:15px;">{{ this.marcators.rechazados }}</div>
                        																						</span>
                        																						<span class="ks-amount-icon ks-icon-circled-up-right"></span>
                        																				</div>
                        																				<div class="payment-simple-amount-item-description">
                                                                    DOCUMENTOS <br>
                        																						RECHAZADOS
                        																				</div>
                        																		</div>
                        																</div>
                        														</div>

                                                    <div class="col-lg-3 col-md-6">
                        																<div class="card ks-widget-payment-simple-amount-item ks-orange">
                        																		<div class="payment-simple-amount-item-icon-block">
                        																				<span class="la la-file-text ks-icon"></span>
                        																		</div>

                        																		<div class="payment-simple-amount-item-body">
                        																				<div class="payment-simple-amount-item-amount">
                        																						<span class="ks-amount">
                        																							<div style="font-size:15px;">{{ this.marcators.enviados }}</div>
                        																						</span>
                        																						<span class="ks-amount-icon ks-icon-circled-up-right"></span>
                        																				</div>
                        																				<div class="payment-simple-amount-item-description">
                        																						DOCUMENTOS <br>
                                                                    ENVIADOS
                        																				</div>
                        																		</div>
                        																</div>
                        														</div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-5 mt-3">
                                              <div class="card">
                                                <div class="card-header">
                                                  VENTAS POR VENDEDOR
                                                </div>
                                                <div class="card-body">
                                                      <table class="table">
                                                        <thead>
                                                          <tr>
                                                            <th>NOMBRE</th>
                                                            <th>TOTAL</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <tr v-for="top in this.top_sellers" :key="top.id">
                                                            <td>
                                                              <div v-if="top.fk_user_id == adminID"  class="badge badge-primary">
                                                                {{ top.name }} {{ sale.last_name}}
                                                              </div>
                                                              <div v-else-if="top.fk_user_id != adminID"  class="badge badge-info">
                                                                {{ top.name }} {{ top.last_name}}
                                                              </div>
                                                            </td>
                                                            <td><span class="badge badge-default">$ {{ number_format(top.amount) }}</span></td>
                                                          </tr>
                                                        </tbody>
                                                      </table>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-4 mt-3">
                                              <div class="card">
                                                <div class="card-header">
                                                  VENTAS POR MEDIOS DE PAGO
                                                </div>
                                                <div class="card-body">
                                                  <table class="table">
                                                    <thead>
                                                      <tr>
                                                        <th>TIPO</th>
                                                        <th>TOTAL</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <tr v-for="v in this.sales_for_type_payment">
                                                        <td v-html="payment_method(v.payment_method)"></td>
                                                        <td><span class="badge badge-default">$ {{ number_format(v.amount) }}</span></td>
                                                      </tr>
                                                    </tbody>
                                                  </table>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                              <div class="card">
                                                <div class="card-header">
                                                  DOCUMENTOS
                                                </div>
                                                <div class="card-body">
                                                  <div class="row">
                                                    <div class="col-md-7">
                                                    <span class="badge badge-info">BOLETAS</span>
                                                    <span class="badge badge-primary mt-1">FACTURAS</span>
                                                    <hr>
                                                    <span class="badge badge-success mt-1">COTIZACIONES</span>
                                                    <span class="badge badge-warning mt-1">PROCESADOS</span>
                                                    </div>
                                                    <div class="col-md-5">
                                                      <span class="badge badge-default">{{ this.sales_for_type_document.boletas }}</span>
                                                      <span class="badge badge-default mt-1">{{ this.sales_for_type_document.facturas }}</span>
                                                      <hr>
                                                      <span class="badge badge-default mt-1">{{ this.sales_for_type_document.cotizaciones }}</span>
                                                      <span class="badge badge-default mt-1">{{ this.sales_for_type_document.procesados }}</span>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                              <div class="card">
                                                <div class="card-header">
                                                  <div class="float-left">GRAFICO | RESUMEN DE VENTAS</div>
                                                  <div class="float-right">TOTAL: $ {{ number_format(this.total_amount_from_range) }}</div>
                                                </div>
                                                <div class="card-body">
                                                  <line-chart id="chart" :bind="true" :options="options" :chart-data="datacollection" :height="110"></line-chart>
                                                </div>
                                              </div>
                                              <div class="card">
                                                <div class="card-header">
                                                  <div class="float-left">GRAFICO | RESUMEN DE GASTOS</div>
                                                  <div class="float-right">TOTAL: $ {{ number_format(this.total_expenses_from_range) }}</div>
                                                </div>
                                                <div class="card-body">
                                                  <line-chart id="chartTwo" :bind="true" :options="options" :chart-data="datacollectionTwo" :height="110"></line-chart>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </transition>
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
    import Vue from 'vue'
    import LineChart from '../../components/LineChartComponent';
    import moment from 'moment'
    import VueSweetalert2 from 'vue-sweetalert2';
    import $ from 'jquery'
    Vue.use(VueSweetalert2);

    export default {
        props: {
          margin: String,
          hidden: Boolean,
          containerC: Boolean,
        },
        components: {
          LineChart,
        },
        data() {
          return {
            printer: 0,
            bussy:false,
            sales: {},
            sale: {},
            saleDetails: {},
            total_amount_from_range:0,
            total_expenses_from_range:0,
            sales_for_type_payment:0,
            sales_for_type_document: {},
            searchMode:false,
            marcators: {},
            top_sellers: {},
            filter: 'T1.folio',
            inputValue: '',
            withoutResults: false,
            clientName: '',
            clientLast_name: '',
            clientEmail: '',
            clientPhone: '',
            clientView: false,
            adminID: 0,
            from: '',
            to: '',
            datacollection: null,
            datacollectionTwo: null,
            printLogs: [],
            printMarkers: {
              total_original_prints: 0,
              total_reprints: 0,
              total_prints: 0,
              sales_with_original: 0,
              sales_with_reprint: 0,
            },
            reprintDashboard: {
              top_reprinters: [],
              users: [],
              user_sales: [],
            },
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
        created(){
          this.getSales();
          this.checkIsadmin();
          this.GeneralConfigCharge();
        },
        mounted() {
            //console.log('component mounted');
            
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
          SalesData() {
            return this.sales.data;
          }
        },
        methods: {
          getPrintSummary(filter = null, value = null, from = null, to = null) {
            const formData = new FormData();
            if (filter) {
              formData.append('filter', filter);
            }
            if (value) {
              formData.append('value', value);
            }
            if (from) {
              formData.append('from', from);
            }
            if (to) {
              formData.append('to', to);
            }

            axios.post('/sales/print/summary', formData).then((response) => {
              this.printMarkers = response.data;
            });
          },
          getReprintDashboard(filter = null, value = null, from = null, to = null) {
            const formData = new FormData();
            if (filter) {
              formData.append('filter', filter);
            }
            if (value) {
              formData.append('value', value);
            }
            if (from) {
              formData.append('from', from);
            }
            if (to) {
              formData.append('to', to);
            }

            axios.post('/sales/print/reprint-dashboard', formData).then((response) => {
              this.reprintDashboard = response.data;
            });
          },
          actionLabel(action) {
            if (action === 'ORIGINAL') {
              return '<span class="badge badge-success">ORIGINAL</span>';
            }
            if (action === 'REPRINT') {
              return '<span class="badge badge-warning">REIMPRESION</span>';
            }
            return '<span class="badge badge-danger">BLOQUEADO</span>';
          },
          formatLogDate(value) {
            if (!value) {
              return '-';
            }
            return moment(value).format('DD/MM/YYYY HH:mm');
          },
          async ShowPrintLogs(saleID) {
            try {
              const response = await axios.get('/sales/' + saleID + '/print/logs');
              this.printLogs = response.data.logs || [];

              if (!this.printLogs.length) {
                this.$swal.fire('Sin registros', 'Esta venta no tiene eventos de impresion.', 'info');
                return;
              }

              let rows = '';
              this.printLogs.forEach((log) => {
                rows +=
                  '<tr>' +
                  '<td>' + this.actionLabel(log.action) + '</td>' +
                  '<td>' + ((log.user_name && log.user_name.trim()) ? log.user_name : ('ID ' + (log.user_id || '-'))) + '</td>' +
                  '<td>' + ((log.approved_by_name && log.approved_by_name.trim()) ? log.approved_by_name : '-') + '</td>' +
                  '<td>' + (log.copy_number || 0) + '</td>' +
                  '<td>' + (log.reason ? log.reason : '-') + '</td>' +
                  '<td>' + this.formatLogDate(log.created_at) + '</td>' +
                  '</tr>';
              });

              this.$swal.fire({
                title: 'Bitacora de impresion - Venta #' + saleID,
                html:
                  '<div style="max-height:380px;overflow:auto;">' +
                  '<table class="table table-sm table-bordered">' +
                  '<thead><tr><th>ACCION</th><th>USUARIO</th><th>AUTORIZA</th><th>COPIA</th><th>MOTIVO</th><th>FECHA</th></tr></thead>' +
                  '<tbody>' + rows + '</tbody>' +
                  '</table>' +
                  '</div>',
                width: '860px',
              });
            } catch (error) {
              const message =
                (error.response && error.response.data && error.response.data.message) ||
                'No se pudo obtener la bitacora de impresion.';
              this.$swal.fire(message, '', 'error');
            }
          },
          GeneralConfigCharge() {
            axios.post("/general_config_get").then((response) => {
              //console.log(response);
              this.printer = response.data.printer;
            });
          },
          async GenerarVoucher(sale_id) {
            const result = await this.$swal.fire({
              title: 'Motivo de reimpresion',
              input: 'text',
              inputPlaceholder: 'Ejemplo: cliente solicita copia',
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

              if (response.data.agent_dispatched) {
                this.$swal.fire('Reimpresion enviada a impresora local', '', 'success');
                return;
              }
              if (response.data.agent_attempted) {
                this.$swal.fire(response.data.agent_message || 'No se pudo enviar a impresora local', '', 'error');
                return;
              }

              if(this.printer === 0 || this.printer === '0') {
                await fetch(response.data.local_url).then(r => r.json());
                this.$swal.fire('Reimpresion enviada a impresora', '', 'success');
              } else {
                this.$swal.fire({
                  html:
                    '<iframe src="' + response.data.preview_url + '#&zoom=180" width="100%" height="470" ></iframe>',
                  showCloseButton: false,
                  showCancelButton: false,
                  focusConfirm: false,
                  confirmButtonText:
                    '<i class="fa fa-thumbs-up"></i> CERRAR',
                  confirmButtonAriaLabel: 'Thumbs up, great!'
                })
              }
            } catch (error) {
              const message = (error.response && error.response.data && error.response.data.message)
                ? error.response.data.message
                : 'No fue posible ejecutar la reimpresion.';
              this.$swal.fire(message, '', 'error');
            }
          },
          formReset() {
            this.total_amount_from_range  = 0;
            this.total_expenses_from_range  = 0;
            this.sales_for_type_payment   = 0;
            this.sales_for_type_document  = 0;
            this.top_sellers              = 0;
          },
          checkIsadmin(){
  					axios.get('/check-auth').then(response=>{
  						this.adminID = response.data.id;
  					});
  				},
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
                  //console.log(response);
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
            this.bussy = true;
            this.searchMode = false;
            //this.formReset();
            axios.post('/sales/get?page='+page).then(response => {
              this.sales = response.data;
              this.bussy = false;
            });
            this.getPrintSummary();
            this.getReprintDashboard();
          },
          type_document(documentID){
            if(documentID == 1){
              return '<span class="badge badge-info">BOLETA</span>';
            }
            else if(documentID == 2) {
              return '<span class="badge badge-primary">FACTURA</span>';
            }
            else if(documentID == 3) {
              return '<span class="badge badge-success">COTIZACIÓN</span>';
            }
            else {
              return '<span class="badge badge-warning">PROCESADO</span>';
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
          ChartSales(grafic_sales){

              let data_sales = [];
              let amount_sales = [];

              grafic_sales.forEach(getSales);
              function getSales(elemento, indice) {
                  data_sales.push(elemento.date);
                  amount_sales.push(elemento.amount);
              }

              this.datacollection = {
                  labels: data_sales,
                  datasets: [
                    {
                      label: 'VENTAS',
                      borderColor: '#4caf50',
                      data: amount_sales
                    }
                  ]
                }
          },
          ChartExpenses(grafic_expenses){

              let data_expenses = [];
              let amount_expenses = [];

              grafic_expenses.forEach(getExpenses);
              function getExpenses(elemento, indice) {
                  data_expenses.push(elemento.date);
                  amount_expenses.push(elemento.amount);
              }

              this.datacollectionTwo = {
                  labels: data_expenses,
                  datasets: [
                    {
                      label: 'GASTOS',
                      borderColor: '#D62121',
                      data: amount_expenses
                    }
                  ]
                }
          },
          FormSearch(page = 1) {
              if(!this.inputValue) {
                if(!this.from || !this.to){
                  this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ingrese un Folio o un rango de fechas!',
                  })
                  return;
                }
              }
              this.bussy = true;
              this.searchMode = true;
              let formData = new FormData();
              formData.append('filter', this.filter);
              formData.append('value', this.inputValue);
              formData.append('from', this.from);
              formData.append('to', this.to);
              axios.post('/sales/get/filters?page='+page, formData)
              .then(response => {
                console.log(response);
                this.getPrintSummary(this.filter, this.inputValue, this.from, this.to);
                this.getReprintDashboard(this.filter, this.inputValue, this.from, this.to);
                if(response.data.table.total == 0){
                  this.sales = {};
                  this.withoutResults = true;
                  this.bussy = false;
                }
                else {
                  this.withoutResults = false;
                  this.bussy = false;

                  if(response.data.table.total == 1){
                    this.sales = response.data.table;
                  }
                  else {
                    this.sales = response.data.table;
                    this.total_amount_from_range = response.data.amount;
                    this.total_expenses_from_range = response.data.total_expenses_from_range;
                    this.top_sellers = response.data.top_sellers;
                    this.sales_for_type_payment = response.data.sales_for_type_payment;
                    this.sales_for_type_document = response.data.sales_for_type_document;
                    this.marcators = response.data.marcators;
                    if(response.data.grafic_sales) {
                      this.ChartSales(response.data.grafic_sales);
                      this.ChartExpenses(response.data.grafic_expenses);
                    }
                  }
                }
            }).catch((error) => {
                // Error 😨
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
transition: opacity .9s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
opacity: 0;
}
.pagination {
  justify-content: flex-end!important;
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
