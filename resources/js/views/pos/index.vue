<template>
  <div class="container-fluid">
    <div class="row mt-1">
      <div class="col-md-6" style="padding: 2px 2px 2px 2px;">
        <div class="card">
          <div class="card-header bg-primary">
            <!-- Modal -->
            <div
              class="modal fade"
              id="SaveShop"
              data-backdrop="static"
              tabindex="-1"
              role="dialog"
              aria-labelledby="staticBackdropLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                      GUARDAR VENTA EN BORRADOR
                    </h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div
                      v-show="alert_shop"
                      class="alert alert-danger alert-dismissible fade show"
                      role="alert"
                    >
                      <strong>Nombre ya existe!</strong>
                      Intente nuevamente.
                    </div>
                    <form class="form-inline" @submit.prevent="SaveShop()">
                      <div class="form-group mx-sm-3 mb-2">
                        <input
                          required
                          v-model="commit_shop"
                          type="text"
                          class="form-control"
                          placeholder="Ejemplo: Mesa 1"
                        />
                      </div>
                      <button type="submit" class="btn btn-default mb-2">
                        GUARDAR
                      </button>
                    </form>
                    <hr />
                    <table class="table table-sm tableBodyScroll">
                      <thead>
                        <tr>
                          <th width="5%">#</th>
                          <th width="45%">Nombre</th>
                          <th width="20%">Fecha</th>
                          <th width="30%">Accion</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(s, index) in shops" :key="s.id">
                          <td width="5%">{{ index + 1 }}</td>
                          <td width="45%">
                            <span class="badge badge-default">
                              {{ s.commentary }}
                            </span>
                          </td>
                          <td width="20%">{{ s.created_at }}</td>
                          <td width="30%">
                            <button
                              v-tooltip="'RETORNAR AL CARRITO'"
                              @click="ReturnSale(s.token, false)"
                              class="btn btn-default btn-sm"
                              type="button"
                              name="button"
                            >
                              <font-awesome-icon :icon="['fas', 'save']" />
                            </button>
                            <button
                              v-tooltip="'ELIMINAR VENTA'"
                              @click="ReturnSale(s.token, true)"
                              class="btn btn-default btn-sm"
                              type="button"
                              name="button"
                            >
                              <font-awesome-icon :icon="['fas', 'trash']" />
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <button
                      type="button"
                      class="btn btn-default"
                      data-dismiss="modal"
                    >
                      CERRAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="NewExpenseModal"
              tabindex="-1"
              role="dialog"
              aria-labelledby="NewExpenseModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="NewExpenseModalLabel">
                      NUEVO GASTO
                    </h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <NewExpense
                      @updatingScore="getExpenses((page = 1))"
                    ></NewExpense>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="changeSession"
              tabindex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div
                class="modal-dialog modal-dialog-centered modal-lg"
                role="document"
              >
                <div class="modal-content">
                  <div class="modal-header" style="background-color: #251927;">
                    <h5
                      class="modal-title"
                      id="exampleModalLabel"
                      style="color: white;"
                    >
                      CAMBIAR DE USUARIO
                    </h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" style="background-color: #251927;">
                    <div class="row">
                      <div
                        class="col-md-4"
                        v-for="u in this.usersSession"
                        :key="u.id"
                      >
                        <table class="table table-sm">
                          <tr>
                            <td
                              @click="SelectedUser(u)"
                              align="center"
                              style="color: white;"
                            >
                              <img
                                :src="u.photo"
                                width="100"
                                class="img-thumbnail"
                              />
                            </td>
                          </tr>
                          <tr>
                            <td align="center" style="color: white;">
                              {{ u.name }} {{ u.last_name }}
                            </td>
                          </tr>
                          <tr>
                            <td :id="'user_' + u.id" style="display: none;">
                              <input
                                :id="'password_' + u.id"
                                placeholder="CONTRASEÑA"
                                type="password"
                                class="form-control"
                                name=""
                                value=""
                              />
                              <button
                                @click="SessionChange(u.id)"
                                class="btn btn-default btn-block mt-1"
                                type="button"
                                name="button"
                              >
                                ENTRAR
                              </button>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer" style="background-color: #251927;">
                    <button
                      type="button"
                      class="btn btn-default"
                      data-dismiss="modal"
                    >
                      CERRAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="shortcuts"
              tabindex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ATAJOS</h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <table class="table table-sm">
                      <tr>
                        <td>PROCESAR VENTA</td>
                        <td>CTRL + Z</td>
                      </tr>
                      <tr>
                        <td>CANCELAR VENTA</td>
                        <td>CTRL + X</td>
                      </tr>
                      <tr>
                        <td>ITEM MANUAL</td>
                        <td>CTRL + Y</td>
                      </tr>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <button
                      type="button"
                      class="btn btn-secondary"
                      data-dismiss="modal"
                    >
                      CERRAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="ShopCartProccess"
              tabindex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div
                class="modal-dialog modal-lg"
                id="modalDocument"
                role="document"
              >
                <div class="modal-content">
                  <hr />
                  <div class="modal-body">
                    <ul
                      class="nav nav-tabs"
                      id="myTab"
                      role="tablist"
                      style="margin-top: -30px;"
                    >
                      <li class="nav-item">
                        <a
                          @click="ModalChangeWidth(1)"
                          class="nav-link active"
                          id="home-tab"
                          data-toggle="tab"
                          href="#home"
                          role="tab"
                          aria-controls="home"
                          aria-selected="true"
                        >
                          FORMULARIO
                        </a>
                      </li>
                      <li class="nav-item" v-if="this.company_billing != 0">
                        <a
                          @click="ModalChangeWidth(2)"
                          class="nav-link"
                          id="profile-tab"
                          data-toggle="tab"
                          href="#document"
                          role="tab"
                          aria-controls="document"
                          aria-selected="false"
                        >
                          DOCUMENTO
                        </a>
                      </li>
                      <li class="nav-item" v-if="this.company_billing == 0">
                        <a
                          @click="ModalChangeWidth(1)"
                          class="nav-link"
                          id="boleta-tab"
                          data-toggle="tab"
                          href="#boleta"
                          role="tab"
                          aria-controls="boleta"
                          aria-selected="false"
                        >
                          E-BOLETA SII
                        </a>
                      </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                      <div
                        class="tab-pane fade show active"
                        id="home"
                        role="tabpanel"
                        aria-labelledby="home-tab"
                      >
                        <div
                          class="card mt-2"
                          style="color: black; font-size: 12px;"
                        >
                          <div class="card-body">
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                v-on:change="checkDiscount()"
                                type="radio"
                                value="0"
                                v-model="type_discount"
                                checked
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp; SIN DESCUENTO
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                v-on:change="checkDiscount()"
                                type="radio"
                                value="2"
                                v-model="type_discount"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp;% PORCENTAJE
                              </label>
                            </div>
                            <div
                              class="form-check form-check-inline"
                              NewExpense="type_discount == 2"
                            >
                              <input
                                v-on:keyup="checkDiscount()"
                                v-model="discount"
                                class="form-control input-sm mt-1"
                                type="number"
                                placeholder="0"
                              />
                            </div>
                          </div>
                        </div>
                        <div
                          class="card mt-2"
                          style="color: black; font-size: 12px;"
                        >
                          <div class="card-body">
                            <!-- <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" @change="updateInvoice()" v-model="typeDocument"  value="0" checked>
                                <label class="form-check-label" for="inlineRadio2">&nbsp;SISTEMA SII (E-BOLETA)</label>
                              </div> -->
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="inlineRadioOptions"
                                @change="updateInvoice()"
                                v-model="typeDocument"
                                value="1"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp;BOLETA
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="inlineRadioOptions"
                                @change="updateInvoice()"
                                v-model="typeDocument"
                                value="2"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio3"
                              >
                                &nbsp;FACTURA
                              </label>
                            </div>
                            <div
                              class="form-check form-check-inline"
                              v-if="this.company_billing != 0"
                            >
                              <input
                                class="form-check-input"
                                type="radio"
                                name="inlineRadioOptions"
                                @change="updateInvoice()"
                                v-model="typeDocument"
                                value="3"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio3"
                              >
                                &nbsp;COTIZACIÓN
                              </label>
                            </div>
                          </div>
                        </div>
                        <div
                          class="card mt-2"
                          style="color: black; font-size: 12px;"
                        >
                          <div class="card-body">
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="PayType"
                                v-model="PayType"
                                value="2"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp;CREDITO
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="PayType"
                                v-model="PayType"
                                value="6"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp;DEBITO
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="PayType"
                                v-model="PayType"
                                value="1"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp;EFECTIVO
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="PayType"
                                v-model="PayType"
                                value="8"
                              />
                              <label
                                class="form-check-label"
                                for="inlineRadio2"
                              >
                                &nbsp;TRANSFERENCIA
                              </label>
                            </div>
                          </div>
                        </div>
                        <div
                          class="card mt-2"
                          style="color: black; font-size: 12px;"
                        >
                          <div class="card-header">
                            <h5 class="mr-4">ASOCIAR CLIENTE</h5>
                            <autocomplete
                              v-tooltip="'RUT DEL CLIENTE SIN GUIÓN NI PUNTOS'"
                              ref="autocomplete"
                              source="/clients/autocomplete?q="
                              inputClass="form-control autocomplete autoC"
                              results-property="data"
                              @selected="setXHRValue"
                              @clear="SearchClear"
                              :results-display="formatDisplay"
                            ></autocomplete>
                          </div>
                        </div>
                        <div
                          class="card mt-2"
                          style="color: black; font-size: 12px;"
                          v-if="
                            isInvoice &&
                            this.clientRUN &&
                            this.company_billing != 0
                          "
                        >
                          <div class="card-header bg-light">
                            <h5>DATOS DE LA EMPRESA</h5>
                          </div>
                          <div class="card-body">
                            <table class="table" style="table-layout: fixed;">
                              <tr>
                                <td>
                                  <input
                                    v-tooltip="'RUT'"
                                    autocomplete="off"
                                    class="form-control"
                                    type="text"
                                    v-on:change="siiRut()"
                                    v-on:keyup="formateaRut()"
                                    v-model="formC.runCompany"
                                    placeholder="RUT EMPRESA"
                                  />
                                </td>
                                <td>
                                  <input
                                    v-tooltip="'CIUDAD'"
                                    autocomplete="off"
                                    class="form-control"
                                    type="text"
                                    v-model="formC.city"
                                    placeholder="CIUDAD"
                                  />
                                </td>
                                <td>
                                  <input
                                    v-tooltip="'RAZÓN SOCIAL'"
                                    autocomplete="off"
                                    class="form-control"
                                    type="text"
                                    v-model="formC.company"
                                    placeholder="RAZON SOCIAL"
                                  />
                                </td>
                              </tr>
                              <tr>
                                <td>
                                  <input
                                    v-tooltip="'COMUNA - CIUDAD'"
                                    autocomplete="off"
                                    class="form-control"
                                    type="text"
                                    v-model="formC.municipality"
                                    placeholder="COMUNA - CIUDAD"
                                  />
                                </td>
                                <td>
                                  <input
                                    v-tooltip="'EMAIL'"
                                    autocomplete="off"
                                    class="form-control"
                                    type="text"
                                    v-model="formC.email"
                                    placeholder="EMAIL"
                                  />
                                </td>
                                <td>
                                  <input
                                    v-tooltip="'DIRECCIÓN'"
                                    autocomplete="off"
                                    class="form-control"
                                    type="text"
                                    v-model="formC.address"
                                    placeholder="DIRECCION"
                                  />
                                </td>
                              </tr>
                              <tr>
                                <td>
                                  <select
                                    v-tooltip="'EMPRESA O PERSONA'"
                                    class="form-control"
                                    v-model="formC.companyOrPerson"
                                    id="companyOrPerson"
                                  >
                                    <option value="1">Persona natural</option>
                                    <option value="2">Empresa</option>
                                  </select>
                                </td>
                                <td colspan="2">
                                  <input
                                    id="activity"
                                    v-if="!isFindSii"
                                    autocomplete="off"
                                    v-tooltip="'ACTIVIDAD ECONOMICA'"
                                    class="form-control"
                                    placeholder="ACTIVIDAD"
                                    type="text"
                                    name=""
                                    value=""
                                    v-model="formC.activity"
                                  />
                                  <select
                                    v-tooltip="'ACTIVIDAD ECONOMICA'"
                                    id="activity"
                                    v-if="isFindSii"
                                    class="form-control"
                                    v-model="formC.activity"
                                  >
                                    <option value="0" selected>
                                      SELECCIONE ACTIVIDAD
                                    </option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td colspan="3">
                                  <div
                                    class="btn-group"
                                    role="group"
                                    aria-label="Basic example"
                                  >
                                    <table class="table">
                                      <tr>
                                        <td>
                                          <div
                                            class="form-check form-check-inline"
                                          >
                                            <input
                                              class="form-check-input"
                                              type="radio"
                                              name="sendemail"
                                              v-model="SendMail"
                                              value="1"
                                            />
                                            <label
                                              class="form-check-label"
                                              for="inlineRadio2"
                                            >
                                              Enviar Email
                                            </label>
                                          </div>
                                          <div
                                            class="form-check form-check-inline"
                                          >
                                            <input
                                              class="form-check-input"
                                              type="radio"
                                              name="sendemail"
                                              v-model="SendMail"
                                              value="0"
                                              checked
                                            />
                                            <label
                                              class="form-check-label"
                                              for="inlineRadio2"
                                            >
                                              No enviar.
                                            </label>
                                          </div>
                                        </td>
                                      </tr>
                                    </table>
                                    <!--<button v-tooltip="'GUARDAR'" @click="saveCompany()" type="button" class="btn btn-default ml-4">
                                          <font-awesome-icon title="Total de Items" :icon="['fas', 'save']" size="2x" />
                                        </button>
                                        <button v-tooltip="'LIMPIAR FORMULARIO'" @click="SearchClear()" type="button" class="btn btn-default ml-2">
                                          <font-awesome-icon title="RESET" :icon="['fas', 'redo-alt']" size="2x" />
                                        </button>-->
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </div>
                        </div>
                        <div class="card mt-2">
                          <div class="card-body">
                            <div class="row">
                              <div class="col-md-6" v-if="this.clientRUN">
                                <h5>CLIENTE RUN</h5>
                              </div>
                              <div class="col-md-6" v-if="this.clientRUN">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  {{
                                    this.RutF(
                                      this.clientRUN + '-' + this.clientVD,
                                    )
                                  }}
                                </span>
                              </div>
                              <div class="col-md-6">
                                <h5>ITEMS</h5>
                              </div>
                              <div class="col-md-6">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  {{ this.countItems }}
                                </span>
                              </div>
                              <div class="col-md-6" v-if="this.discount">
                                <h5>DESCUENTO</h5>
                              </div>
                              <div class="col-md-6" v-if="this.discount">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  {{
                                    number_format(this.discount)
                                      ? number_format(this.discount)
                                      : 0
                                  }}
                                  %
                                </span>
                              </div>
                              <div class="col-md-6" v-if="this.discount">
                                <h5>DESCUENTO EN PESOS</h5>
                              </div>
                              <div class="col-md-6" v-if="this.discount">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  $
                                  {{
                                    number_format(this.discountCLP)
                                      ? number_format(this.discountCLP)
                                      : 0
                                  }}
                                </span>
                              </div>
                              <div class="col-md-6" v-if="isInvoice">
                                <h5>NETO</h5>
                              </div>
                              <div class="col-md-6" v-if="isInvoice">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  $
                                  {{
                                    number_format(
                                      Math.floor(
                                        (this.amount - this.discountCLP) / 1.19,
                                      ),
                                    )
                                  }}
                                </span>
                              </div>
                              <div class="col-md-6" v-if="isInvoice">
                                <h5>IVA</h5>
                              </div>
                              <div class="col-md-6" v-if="isInvoice">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  $
                                  {{
                                    number_format(
                                      Math.floor(
                                        (this.amount - this.discountCLP) / 1.19,
                                      ) * 0.19,
                                    )
                                  }}
                                </span>
                              </div>
                              <div class="col-md-6">
                                <h5>TOTAL</h5>
                              </div>
                              <div class="col-md-6">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  $
                                  {{
                                    number_format(
                                      Math.floor(
                                        this.amount - this.discountCLP,
                                      ),
                                    )
                                  }}
                                </span>
                              </div>
                              <div class="col-md-12">
                                <hr />
                              </div>
                              <div class="col-md-6">
                                <h5>TOTAL PAGADO</h5>
                              </div>
                              <div class="col-md-6">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  $
                                  {{
                                    this.TotalToPay
                                      ? number_format(this.TotalToPay)
                                      : number_format(
                                          Math.floor(
                                            this.amount - this.discountCLP,
                                          ),
                                        )
                                  }}
                                </span>
                              </div>
                              <div class="col-md-6">
                                <h5>VUELTO</h5>
                              </div>
                              <div class="col-md-6">
                                <span
                                  class="badge badge-default"
                                  style="width: 100%; font-size: 15px;"
                                >
                                  $ {{ number_format(vuelto) }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div
                          class="card mt-2"
                          style="color: black; font-size: 12px;"
                        >
                          <div class="card-body">
                            <div class="row">
                              <div
                                class="col-md-6"
                                v-if="this.company_billing == 0"
                              >
                                <div class="form-check form-check-inline">
                                  <label class="form-check-label">
                                    VOUCHER / FOLIO :
                                  </label>
                                  <input
                                    v-tooltip="'FOLIO'"
                                    type="number"
                                    v-model="voucher"
                                    class="form-control"
                                  />
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-check form-check-inline">
                                  <label class="form-check-label">
                                    TOTAL PAGADO:
                                  </label>
                                  <input
                                    v-tooltip="'TOTAL PAGADO'"
                                    type="number"
                                    v-model="TotalToPay"
                                    class="form-control"
                                    placeholder="0"
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div
                        class="tab-pane fade"
                        id="document"
                        role="tabpanel"
                        aria-labelledby="profile-tab"
                      >
                        <div id="generatedDocument" class="mt-3">
                          <div class="alert alert-info" role="alert">
                            No se ha generado ningun documento, genere uno
                            completando el formulario
                          </div>
                        </div>
                      </div>
                      <div
                        class="tab-pane fade"
                        id="boleta"
                        role="tabpanel"
                        aria-labelledby="boleta-tab"
                      >
                        <div class="card iframe1">
                          <iframe
                            frameborder="0"
                            allowfullscreen
                            src="https://eboleta.sii.cl"
                          ></iframe>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button
                      @click="ProcessOrder()"
                      type="button"
                      class="btn btn-default btn-lg"
                    >
                      PROCESAR
                    </button>
                    <button
                      type="button"
                      class="btn btn-default btn-lg"
                      data-dismiss="modal"
                    >
                      CERRAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="Clients"
              tabindex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CLIENTES</h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <clients :hidden="true"></clients>
                  </div>
                  <div class="modal-footer">
                    <button
                      type="button"
                      class="btn btn-secondary"
                      data-dismiss="modal"
                    >
                      CERRAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="Items"
              tabindex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ITEMS</h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <items :hidden="true"></items>
                  </div>
                  <div class="modal-footer">
                    <button
                      type="button"
                      class="btn btn-secondary"
                      data-dismiss="modal"
                    >
                      CERRAR
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            <div
              class="modal fade"
              id="modalItemManual"
              tabindex="-1"
              role="dialog"
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                      INSERTAR ITEM MANUAL
                    </h5>
                    <button
                      type="button"
                      class="close"
                      data-dismiss="modal"
                      aria-label="Close"
                    >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form @submit.prevent="InsertManualItemToCart()">
                    <div class="modal-body">
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-user">Nombre del Item</i>
                          </span>
                        </div>
                        <input
                          id="itemName"
                          v-model="form.itemName"
                          type="text"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.has('itemName') }"
                        />
                        <has-error :form="form" field="itemName"></has-error>
                      </div>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="width: 164px;">
                            <i class="fas fa-user">Precio</i>
                          </span>
                        </div>
                        <input
                          v-model="form.itemPrice"
                          type="number"
                          class="form-control"
                          :class="{
                            'is-invalid': form.errors.has('itemPrice'),
                          }"
                        />
                        <has-error :form="form" field="itemPrice"></has-error>
                      </div>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="width: 164px;">
                            <i class="fas fa-user">Cantidad</i>
                          </span>
                        </div>
                        <input
                          v-model="form.itemQuantity"
                          type="number"
                          class="form-control"
                          :class="{
                            'is-invalid': form.errors.has('itemQuantity'),
                          }"
                        />
                        <has-error
                          :form="form"
                          field="itemQuantity"
                        ></has-error>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button
                        type="button"
                        class="btn btn-danger"
                        data-dismiss="modal"
                      >
                        CERRAR
                      </button>
                      <button type="submit" class="btn btn-success">
                        INGRESAR ITEM
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <form
              class="form-inline"
              @submit.prevent="AddForCode(code)"
              method="post"
              autocomplete="off"
            >
              <div class="input-group mx-1 mt-1">
                <input
                  style="width: 150px;"
                  autofocus
                  v-model="code"
                  id="inputCode"
                  type="text"
                  class="form-control"
                  name=""
                  value=""
                  placeholder="CODIGO + ENTER"
                />
              </div>
              <button
                v-tooltip="'ITEM MANUAL'"
                @click="itemManual()"
                @shortkey="itemManual()"
                v-shortkey="['ctrl', 'y']"
                class="btn btn-default mx-1 mt-1"
                type="button"
              >
                <i style="margin-top: -5px;" class="las la-pen-alt la-2x"></i>
              </button>
              <button
                v-tooltip="'ATAJOS'"
                class="btn btn-default mx-1 mt-1"
                type="button"
                data-toggle="modal"
                data-target="#shortcuts"
              >
                <i style="margin-top: -5px;" class="las la-magic la-2x"></i>
              </button>
              <button
                v-tooltip="'CAMBIAR DE USUARIO'"
                data-toggle="modal"
                data-target="#changeSession"
                class="btn btn-default mx-1 mt-1"
                type="button"
                name="button"
              >
                <i style="margin-top: -5px;" class="las la-user-lock la-2x"></i>
              </button>
              <button
                v-tooltip="'GUARDAR VENTA'"
                data-toggle="modal"
                data-target="#SaveShop"
                class="btn btn-default mx-1 mt-1"
                type="button"
                name="button"
              >
                <i style="margin-top: -5px;" class="las la-save la-2x"></i>
              </button>
              <button
                v-tooltip="'CAJA'"
                @click="openBoxBtn(true)"
                @shortkey="openBoxBtn(true)"
                :disabled="this.bussy"
                class="btn btn-default mx-1 mt-1"
                type="button"
                name="button"
              >
                <i style="margin-top: -5px;" class="las la-donate la-2x"></i>
              </button>
              <span class="ml-2" style="color: white;">
                <font-awesome-icon
                  title="Total de Items"
                  :icon="['fas', 'shopping-cart']"
                />
                {{ this.countItems }}
              </span>
            </form>
          </div>
          <div class="card-body" id="item_shop">
            <div class="table-responsive">
              <table class="table table-sm">
                <thead class="thead-light">
                  <tr>
                    <th width="20%">NOMBRE</th>
                    <th width="22%">PRECIO</th>
                    <th width="23%">CANTIDAD</th>
                    <th width="20%">SUBTOTAL</th>
                    <th width="15%">ACCION</th>
                  </tr>
                </thead>
              </table>
              <table class="table table-sm tableBodyScroll">
                <tbody>
                  <tr
                    v-for="(cart, index) in this.shoping_cart"
                    :key="cart.details.id"
                    v-bind:id="'tr_' + cart.details.id"
                  >
                    <td v-tooltip="cart.details.name" width="20%">
                      {{ cart.details.name.substr(0, 25) }}...
                    </td>
                    <td width="22%">
                      $ {{ number_format(cart.details.price) }}
                    </td>
                    <td width="23%">
                      <div
                        class="btn-group"
                        role="group"
                        aria-label="Basic example"
                      >
                        <button
                          type="button"
                          @click="
                            SetQuantity(
                              index,
                              parseInt(cart.quantity) - 1,
                              cart,
                              '-',
                            )
                          "
                          name="button"
                        >
                          -
                        </button>
                        <input
                          style="text-align: center;"
                          :id="'ManualQuantity_' + cart.details.id"
                          @change="
                            SetQuantityManual(
                              index,
                              (quantity = $event.target.value),
                              cart,
                            )
                          "
                          size="3"
                          min="1"
                          type="text"
                          :value="cart.quantity"
                        />
                        <button
                          type="button"
                          @click="
                            SetQuantity(
                              index,
                              parseInt(cart.quantity) + 1,
                              cart,
                              '+',
                            )
                          "
                          name="button"
                        >
                          +
                        </button>
                      </div>
                    </td>
                    <td width="20%">
                      $ {{ number_format(cart.details.price * cart.quantity) }}
                    </td>
                    <td width="15%">
                      <button
                        v-tooltip="'ELIMINAR'"
                        class="btn btn-default btn-sm"
                        type="button"
                        @click="
                          ShopDeleteItem(
                            cart.details.code,
                            cart.details.id,
                            cart.quantity,
                            index,
                          )
                        "
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="card" style="margin: 5px 0px 0px 0px; padding: 1px;">
          <div class="row no-gutters">
            <div class="col-md-4">
              <button
                @click="ShopGo()"
                @shortkey="ShopGo()"
                v-shortkey="['ctrl', 'z']"
                style="float: left; height: 64px; width: 97%;"
                class="btn btn-default ml-2 mt-1"
                type="button"
                name="button"
              >
                <font-awesome-icon :icon="['fas', 'shopping-cart']" size="2x" />
                <h5 style="color: white;">PROCESAR</h5>
              </button>
              <button
                @click="cancelPurchase(false)"
                @shortkey="cancelPurchase(false)"
                v-shortkey="['ctrl', 'x']"
                style="float: left; height: 64px; width: 97%;"
                class="btn btn-default ml-2 mt-1 mb-2"
                type="button"
                name="button"
              >
                <font-awesome-icon :icon="['fas', 'window-close']" size="2x" />
                <h5 style="color: white;">CANCELAR</h5>
              </button>
            </div>
            <div class="col-md-8" style="padding: 15px;">
              <table class="table table-sm mt-2">
                <tbody>
                  <tr>
                    <td><h5 style="color: #807b7b;">TOTAL</h5></td>
                    <td width="60%">
                      <h2 style="color: #807b7b;">
                        $ {{ number_format(this.amount) }}
                      </h2>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <div class="row no-gutters">
                      <div class="col-md-4">
                        <button
                          @click="Clients()"
                          type="button"
                          class="btn btn-default btn-block"
                        >
                          CLIENTES
                        </button>
                      </div>
                      <div class="col-md-4">
                        <button
                          @click="Items()"
                          type="button"
                          class="btn btn-default btn-block"
                        >
                          ITEMS
                        </button>
                      </div>
                      <!--<div class="col-md-3">
                            <button  type="button" class="btn btn-default btn-block">CAJA</button>
                          </div>-->
                      <div class="col-md-4">
                        <button
                          type="button"
                          @click="NewExpenses()"
                          class="btn btn-default btn-block"
                        >
                          GASTOS
                        </button>
                      </div>
                    </div>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6" style="padding: 2px 2px 2px 2px;">
        <div class="card">
          <div class="card-header bg-primary" style="color: white;">
            <div class="form-group mx-1 mt-1">
              <select
                @change="UpdateUrl"
                style="width: 200px;"
                class="form-control"
                v-model="category"
              >
                <option value="all" selected>TODAS LAS CATEGORIAS</option>
                <option
                  v-for="category in this.categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div class="input-group mx-1 mt-1">
              <select
                style="width: 110px;"
                class="form-control"
                v-model="filter"
              >
                <option value="name">NOMBRE</option>
                <option value="code">CODIGO</option>
              </select>
            </div>
            <div class="input-group mx-1 mt-1">
              <input
                v-model="texto"
                v-on:keyup="Search()"
                type="text"
                class="form-control"
                placeholder="CODE / NAME"
              />
            </div>
            <div class="input-group ml-2 mt-2">
              <font-awesome-icon :icon="['fas', 'coins']" />
              &nbsp;
              {{ search_items }}
            </div>
          </div>
          <div class="card-body">
            <ul
              class="nav nav-tabs panel-tabs"
              id="myTab"
              role="tablist"
              style="margin-top: -13px;"
            >
              <li class="nav-item">
                <a
                  class="nav-link active"
                  id="items-tab"
                  data-toggle="tab"
                  href="#items"
                  role="tab"
                  aria-controls="items"
                  aria-selected="true"
                >
                  ITEMS
                </a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  id="profile-tab"
                  data-toggle="tab"
                  href="#profile"
                  role="tab"
                  aria-controls="profile"
                  aria-selected="false"
                >
                  ULTIMAS VENTAS
                </a>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div
                class="tab-pane fade show active"
                id="items"
                role="tabpanel"
                aria-labelledby="items-tab"
              >
                <div class="row no-gutters mt-1">
                  <div class="col-md-12">
                    <div
                      class="alert alert-dismissible alert-warning"
                      v-if="search_result"
                    >
                      <button type="button" class="close" data-dismiss="alert">
                        &times;
                      </button>
                      <h5 class="alert-heading">Sin resultados!</h5>
                      <p class="mb-0">La busqueda no arrojo resultados.</p>
                    </div>
                  </div>
                  <div
                    class="col-md-3"
                    v-for="item in this.items"
                    :key="item.id"
                  >
                    <div class="card text-black">
                      <div class="p-2" :title="item.name">
                        {{ item.name.substr(0, 12) }}...
                      </div>
                      <picture>
                        <center>
                          <span class="notify-badge" :id="'s_' + item.id">
                            {{ item.stock }} uds
                          </span>
                          <img
                            v-tooltip="'AGREGAR AL CARRITO'"
                            @click="AddForCode(item)"
                            :src="
                              item.photo ? item.photo : '/img/item_default.png'
                            "
                            class="img-fluid img-thumbnail mx-auto"
                            :alt="item.name"
                          />
                        </center>
                      </picture>
                      <div class="p-2">
                        <button
                          v-tooltip="'AGREGAR AL CARRITO'"
                          @click="AddForCode(item)"
                          class="btn btn-default btn-sm btn-block"
                          type="button"
                          name="button"
                        >
                          $ {{ number_format(item.price) }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <infinite-loading
                  ref="infiniteLoading"
                  @infinite="infiniteHandler"
                >
                  <span slot="no-more">&nbsp;</span>
                </infinite-loading>
              </div>
              <div
                class="tab-pane fade"
                id="profile"
                role="tabpanel"
                aria-labelledby="profile-tab"
              >
                <sales
                  ref="sales"
                  :containerC="true"
                  :hidden="true"
                  margin="margin-top:-60px;"
                ></sales>
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
import clients from '../clients/index'
import items from '../items/index'
import sales from '../sales/index'
import NewExpense from '../expenses/new-expense'
import InfiniteLoading from 'vue-infinite-loading'
import { Form, HasError, AlertError } from 'vform'
import Autocomplete from 'vuejs-auto-complete'
import $ from 'jquery'

window.Form = Form

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

export default {
  components: {
    InfiniteLoading,
    clients,
    items,
    sales,
    Autocomplete,
    NewExpense,
  },
  data() {
    return {
      bussy: true,
      isBoxOpen: false,
      id_sale: 0,
      company_billing: 0,
      printer: 0,
      optional_printer: 0,
      voucher: '0',
      page: 1,
      alert_shop: false,
      shop_id: 0,
      usersSession: {},
      adminID: 0,
      isFindSii: false,
      shop_saves: {},
      commit_shop: '',
      formC: new Form({
        email: '',
        runCompany: '',
        city: '',
        company: '',
        municipality: '',
        activity: '',
        address: '',
        companyOrPerson: 2,
      }),
      form: new Form({
        itemName: '',
        itemPrice: '',
        itemQuantity: 1,
      }),
      isInvoice: false, // si es factura
      typeDocument: 0, // 0 procesar 1 boleta 2 factura
      urlInfinite: '/item/category/all',
      categories: {},
      countItems: 0,
      amount: 0,
      shoping_cart: [],
      type_discount: 0,
      discount: '',
      discountCLP: 0,
      items: [],
      texto: '',
      filter: 'name',
      category: 'all',
      search_result: false,
      search_items: 0,
      code: '',
      clientRUN: '',
      clientVD: '',
      ClientID: '',
      clientName: '',
      clientLast_name: '',
      clientEmail: '',
      clientPhone: '',
      signo: '',
      TotalToPay: '',
      PayType: 1,
      SendMail: 0,
      ButtonRegCompany: false,
      clientShowData: true,
      rutSinPuntos: '',
      /// PAGE CONFIG
    }
  },
  created() {
    this.GeneralConfigCharge()
    this.getCategories()
    this.GetPageConfig()
    this.GetShopID()
    this.checkIsadmin()
    this.getShops()
    this.dailyStatus()
  },
  mounted() {
    $('#NewExpenseModal').on('show.bs.modal', function (event) {
      $("#expense_input_date").val(moment().format().slice(0,16))
    })
    if (localStorage.getItem('carrito')) {
      try {
        this.shoping_cart = JSON.parse(localStorage.getItem('carrito'))
        this.getCountItems()
        this.getTotal()
      } catch (e) {
        localStorage.removeItem('carrito')
      }
    }

    var barcode = ''
    var that = this
    $(document).keydown(function (e) {
      var codigo = that.code
      if (codigo == '') {
        var code = e.keyCode ? e.keyCode : e.which
        if (code == 13) {
          // Enter key hit
          that.code = barcode
          that.AddForCode(that.code)
          barcode = ''
        }
        if (code == 122) {
          // Enter key hit
          that.code = barcode
          that.AddForCode(that.code)
          barcode = ''
        } else {
          barcode = barcode + String.fromCharCode(code)
        }
      } else {
        barcode = ''
      }
    })
  },
  computed: {
    shops() {
      return this.shop_saves
    },
    vuelto() {
      if (this.TotalToPay < this.amount - this.discountCLP) {
        return 0
      } else {
        return this.TotalToPay - (this.amount - this.discountCLP)
      }
    },
  },
  methods: {
    today() {
      return moment().format('L')
    },
    async dailyCreate() {
      await axios
        .post('/daily', {
          input_initial_daily: $('#input_initial_daily').val(),
        })
        .then((response) => {
          console.log('response .then', response)
          if (response.data.status) {
            console.log('consola status', response)
            this.isBoxOpen = true
            this.$swal.fire(response.data.msg, '', 'success')
            this.bussy = false
          }
        })
    },
    async dailyStatus() {
      await axios.get('/daily').then((response) => {
        if (response.data.status) {
          this.isBoxOpen = true
          this.bussy = false
        } else {
          this.isBoxOpen = false
          this.bussy = false
        }
      })
    },
    openBoxBtn: function (message = false) {
      if (!this.isBoxOpen) {
        this.$swal
          .fire({
            title: 'No existe una caja inicial para este turno.',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            html:
              '<p>Ingrese el monto inicial. </p> <div class="col-md-6 offset-md-3 input-group input-group-md"><div class="input-group-prepend"><span class="input-group-text" id="inputGroup-sizing-md">$</span> </div> <input id="input_initial_daily" type="text" style="font-size:18px;" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg"></div>',
          })
          .then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
              this.dailyCreate()
            }
          })
        $('#input_initial_daily').focus()
        return
      } else {
        if (message) {
          this.$swal
            .fire({
              //title: "Quieres cerrar el turno?",
              html:
                "<iframe width='460px' height='600px' src='/daily-summary-turn'></iframe>",
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'CERRAR CAJA',
              cancelButtonText: 'CANCELAR',
            })
            .then((result) => {
              if (result.isConfirmed) {
                axios.get('/daily-close-turn').then((response) => {
                  if (response.status) {
                    ;(this.isBoxOpen = false),
                      this.$swal.fire(
                        'Caja Cerrada!',
                        'La caja ha sido cerrada exitosamente.',
                        'success',
                      )
                  }
                })
              }
        })
        }
      }
    },

    ShopGo: function () {
      {
        jQuery.noConflict()
        if (this.countItems == 0) {
          this.$swal.fire('No hay items en el carrito')
          return
        } else {
          this.openBoxBtn()
        }
      }

      if (this.isBoxOpen) {
        $('#ShopCartProccess').modal('show')
      }
    },
    async GeneralConfigCharge() {
      await axios.post('/general_config_get').then((response) => {
        //console.log(response)
        this.company_billing = response.data.company_billing
        this.printer = response.data.printer
        this.optional_printer = response.data.optional_printer
      })
    },
    async getShops() {
      await axios.get('get-shop').then((response) => {
        this.shop_saves = response.data
      })
    },
    async ReturnSale(token, isDelete) {
      await axios.post('return-shop/' + token).then((response) => {
        var items = response.data
        var shop = []

        if (this.shoping_cart.length) {
          this.$swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text:
              'Existe un una venta en proceso, para cargar esta venta antes debes guardar o finalizar la anterior!',
          })
        } else {
          items.forEach(function (valor, indice, array) {
            shop.push({
              details: {
                id: valor['fk_id_item'],
                code: valor['code'],
                name: valor['name'],
                price: valor['price'],
                stock: null,
              },
              quantity: valor['quantity'],
            })
          })

          if (isDelete) {
            this.$swal
              .fire({
                title: 'Estas seguro?',
                text:
                  'Se eliminara esta venta y sus items volveran al stock general!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar venta!',
              })
              .then((result) => {
                if (result.value) {
                  this.shoping_cart = shop
                  this.setlocalStorage(this.shoping_cart)
                  this.cancelPurchase(true)
                  this.getCountItems()
                  this.getShops()
                  this.getTotal()
                  this.commit_shop = ''
                  this.HardReset()
                  $('#SaveShop').modal('hide')
                  this.$swal.fire(
                    'Eliminada!',
                    'La venta se ha eliminado.',
                    'success',
                  )
                }
              })
          } else {
            this.shoping_cart = shop
            this.setlocalStorage(this.shoping_cart)
            this.getCountItems()
            this.getShops()
            this.getTotal()
            this.commit_shop = ''
            $('#SaveShop').modal('hide')
          }
        }
      })
    },
    async SaveShop() {
      if (this.shoping_cart.length) {
        await axios
          .post('save-shop/' + this.commit_shop, this.shoping_cart)
          .then((response) => {
            if (response.data.name == true) {
              this.alert_shop = true
            } else {
              this.alert_shop = false
              this.shoping_cart = []
              localStorage.removeItem('carrito')
              this.getShops()
              this.getTotal()
            }
            //console.log(response);
          })
      } else {
        this.$swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'No existen items en su carrito!',
        })
      }
    },
    async SessionChange(userID) {
      var password = $('#password_' + userID).val()
      if (password) {
        await axios
          .get('/change/session/' + userID + '/password/' + password)
          .then((response) => {
            if (response.data.status == false) {
              this.$swal.fire({
                icon: 'error',
                title: 'Oops...',
                text:
                  'La contraseña ingresada no es correcta, intente nuevamente!',
              })
            } else {
              this.HardReset()
              this.$router.go()
            }
          })
      } else {
        this.$swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Debe Ingresar su contraseña.',
        })
      }
    },
    SelectedUser(user) {
      $('#user_' + user.id).show('SLOW')
    },
    async checkIsadmin() {
      await axios.get('/check-auth').then((response) => {
        this.adminID = response.data.id
      })
    },
    async GetShopID() {
      await axios.get('/check-auth').then((response) => {
        this.shop_id = response.data.shop
        //console.log(this.shop_id);
      })
      this.ListUsers()
    },
    async ListUsers() {
      await axios.get('/get/users/shop/' + this.shop_id).then((response) => {
        this.usersSession = response.data
        //console.log(response);
      })
    },
    ModalChangeWidth(id) {
      if (id == 1) {
        $('#modalDocument').removeClass('modal-xl')
        $('#modalDocument').addClass('modal-lg')
      } else {
        $('#modalDocument').removeClass('modal-lg')
        $('#modalDocument').addClass('modal-xl')
      }
    },
    NewExpenses() {
      jQuery.noConflict()
      $('#NewExpenseModal').modal('show')
    },
    async GetPageConfig() {
      await axios.post('/general_config_get_filter').then((response) => {
        this.typeDocument = response.data.default_document_type
        this.PayType = response.data.default_payment_method
        if (this.typeDocument == 2 || this.typeDocument == 3) {
          this.isInvoice = true
          this.ButtonRegCompany = true
        }
      })
    },
    RutF: function (rut) {
      var actual = rut.replace(/^0+/, '')
      if (actual != '' && actual.length > 1) {
        var sinPuntos = actual.replace(/\./g, '')
        var actualLimpio = sinPuntos.replace(/-/g, '')
        var inicio = actualLimpio.substring(0, actualLimpio.length - 1)
        var rutPuntos = ''
        var i = 0
        var j = 1
        for (i = inicio.length - 1; i >= 0; i--) {
          var letra = inicio.charAt(i)
          rutPuntos = letra + rutPuntos
          if (j % 3 == 0 && j <= inicio.length - 1) {
            rutPuntos = '.' + rutPuntos
          }
          j++
        }
        var dv = actualLimpio.substring(actualLimpio.length - 1)
        rutPuntos = rutPuntos + '-' + dv
      }
      return rutPuntos
    },
    formateaRut() {
      if (this.formC.runCompany.length >= 2) {
        this.rutSinPuntos = this.RutF(this.formC.runCompany)
        this.formC.runCompany = this.RutF(this.rutSinPuntos)
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
    },
    saveCompany(msg = true) {
      if (
        !this.formC.runCompany ||
        !this.formC.city ||
        !this.formC.company ||
        !this.formC.municipality ||
        !this.formC.activity ||
        !this.formC.address
      ) {
        this.$swal.fire(
          'Debe completar todos los campos del formulario EMPRESA',
        )
        return
      }
      this.formC.post('/update/company/' + this.ClientID).then((response) => {
        //console.log(response);
        if (response.data == 'success') {
          if (msg == true) {
            this.$swal.fire('Empresa actualizada!', '', 'success')
          }
        } else {
          this.$swal.fire('El rut empresa ingresado no es valido!', '', 'error')
        }
      })
    },
    async siiRut() {
      await axios.post('/sii/' + this.formC.runCompany).then((response) => {
        //console.log(response);
        if (response.data.rut == false) {
          this.$swal.fire('El rut ingresado no es valido!', '', 'error')
        } else {
          this.formC.company = response.data.razonSocial
          if (!response.data.actividades.length) {
            this.formC.activity = ''
            this.isFindSii = false
            this.formC.companyOrPerson = 1
            return
          } else {
            this.formC.activity = ''
            this.isFindSii = true
            this.formC.companyOrPerson = 2
          }
          let options = ''
          $.each(response.data.actividades, function (key, value) {
            options =
              options +
              '<option value="' +
              value.giro +
              '">' +
              value.giro +
              '</option>'
          })

          setTimeout(function () {
            $('#activity').append(options)
          }, 1000)
        }
      })
    },
    GetAmount() {
      return this.number_format(this.amount)
    },
    updateInvoice: function () {
      if (this.typeDocument == 0) {
        this.isInvoice = false
        this.ButtonRegCompany = false
      } else if (this.typeDocument == 1) {
        this.isInvoice = false
        this.ButtonRegCompany = false
      } else {
        this.isInvoice = true
        this.ButtonRegCompany = true
      }
    },
    async setXHRValue(obj) {
      this.ClientID = obj.value
      await axios.post('/clients/search/' + obj.value).then((response) => {
        //console.log(response);
        this.clientRUN = response.data.run
        this.clientVD = response.data.verifying_digit
        this.clientName = response.data.name
        this.clientLast_name = response.data.last_name
        this.clientEmail = response.data.email
        this.clientPhone = response.data.phone
        this.formC.email = response.data.email
        this.formC.runCompany = response.data.runCompany
        this.formC.city = response.data.city
        this.formC.company = response.data.company
        this.formC.municipality = response.data.municipality
        this.formC.activity = response.data.activity
        this.formC.address = response.data.address
        this.formC.companyOrPerson = response.data.companyOrPerson
      })
    },
    async getCategories() {
      await axios.post('/select/categories').then((response) => {
        //console.log(response);
        this.categories = response.data
      })
    },
    SearchClear: function () {
      this.clientRUN = ''
      this.clientVD = ''
      this.ClientID = ''
      this.clientName = ''
      this.clientLast_name = ''
      this.clientEmail = ''
      this.clientPhone = ''
      //this.$refs.autocomplete.clear();
      $('.autoC').val('')
      this.$emit('clear')
    },
    formatDisplay(result) {
      return result.name + ' ' + result.last_name
    },
    setlocalStorage: function (array) {
      let parsed = JSON.stringify(array)
      localStorage.setItem('carrito', parsed)
    },
    Clients: function () {
      jQuery.noConflict()
      $('#Clients').modal('show')
    },
    Items: function () {
      jQuery.noConflict()
      $('#Items').modal('show')
    },

    itemManual: function () {
      jQuery.noConflict()
      $('#modalItemManual').modal('show')
      setTimeout(function () {
        $('#itemName').focus()
      }, 500)
    },
    InsertManualItemToCart: function (
      item_variable = false,
      name = false,
      price = false,
      quantity = false,
    ) {
      if (item_variable == false) {
        if (!this.form.itemName) {
          this.form.errors.set('itemName', 'Debe ingresar un nombre')
        } else {
          this.form.errors.clear('itemName')
        }
        if (!this.form.itemPrice) {
          this.form.errors.set('itemPrice', 'Debe ingresar un precio')
        } else {
          if (!isNaN(this.form.itemPrice)) {
            this.form.errors.clear('itemPrice')
          } else {
            this.form.errors.set('itemPrice', 'El campo debe ser numerico')
          }
        }
        if (!this.form.itemQuantity) {
          this.form.errors.set('itemQuantity', 'Debe ingresar una cantidad')
        } else {
          this.form.errors.clear('itemQuantity')
        }
      }

      if (this.form.errors.any() == false) {
        this.shoping_cart.push({
          details: {
            id: null,
            code: null,
            name: this.form.itemName ? this.form.itemName : name,
            price: this.form.itemPrice ? this.form.itemPrice : price,
            stock: null,
          },
          quantity: this.form.itemQuantity ? this.form.itemQuantity : quantity,
        })
        $('tbody').animate(
          { scrollTop: parseInt($('tbody')[3].scrollHeight) },
          'slow',
        )
        this.setlocalStorage(this.shoping_cart)
        jQuery.noConflict()
        $('#modalItemManual').modal('hide')
        this.form.reset()
        this.getCountItems()
        this.getTotal()
      }
    },
    checkDiscount: function () {
      this.getTotal()
      if (this.type_discount == 0) {
        this.discount = '0'
        this.discountCLP = '0'
        //this.signo = '';
      }
      if (this.type_discount == 2) {
        // DESCUENTO POR PORCENTAJE
        let discount = Math.round((this.amount * this.discount) / 100 / 10) * 10
        this.discountCLP = discount
        //this.signo = '% ';
        //this.amount = this.amount - discount;
      }
    },
    AddForCode: async function (item) {
      if (item.type_price == 2) {
        this.$swal.fire({
          input: 'text',
          inputPlaceholder: 'INGRESE EL PRECIO $',
          inputAttributes: {
            autocapitalize: 'off',
            id: 'newPrice',
          },
          showCancelButton: true,
          confirmButtonText: 'ENTER',
          cancelButtonText: 'CANCELAR',
          html:
            '<div class="card"> <div class="card-body"> <table class="" style="width:100%; table-layout:fixed;"> <tr> <td><button onclick="keyadd(1)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">1</button></td><td><button onclick="keyadd(2)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">2</button></td><td><button onclick="keyadd(3)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">3</button></td></tr><tr> <td><button onclick="keyadd(4)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">4</button></td><td><button onclick="keyadd(5)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">5</button></td><td><button onclick="keyadd(6)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">6</button></td></tr><tr> <td><button onclick="keyadd(7)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">7</button></td><td><button onclick="keyadd(8)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">8</button></td><td><button onclick="keyadd(9)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">9</button></td></tr><tr> <td><button onclick="keyadd(0)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">0</button></td><td><button onclick="keyadd(0,2)" class="btn btn-primary" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">00</button></td><td><button onclick="keyadd(0,3)" class="btn btn-info" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">000</button></td></tr><tr> <td colspan="2"><button onclick="keyadd(-1)" class="btn btn-danger" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">CE</button></td><td><button onclick="keyadd(-2)" class="btn btn-warning" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">DEL</button></td></tr></table></div></div>',
          showLoaderOnConfirm: true,
          preConfirm: (price) => {
            if (price <= 0) {
              this.$swal.showValidationMessage('Precio debe ser mayor que 0.')
            } else {
              this.InsertManualItemToCart(true, item.name, price, 1)
              $('tbody').animate(
                { scrollTop: parseInt($('tbody')[3].scrollHeight) },
                'slow',
              )
            }
          },
        })
      } else {
        if (item.code) {
          this.code = item.code
        }
        if (this.code == '') {
          this.$swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Debe ingresar un codigo',
            showConfirmButton: false,
            timer: 1500,
          })
          return
        } else {
          await axios.post('/pos/' + this.code).then((response) => {
            if (response.data.item == false) {
              this.$swal.fire({
                position: 'center',
                icon: 'error',
                title: 'El codigo Ingresado no existe.',
                showConfirmButton: false,
                timer: 1500,
              })
              this.code = ''
              $('#inputCode').val('')
              $('#inputCode').focus()
              return
            }
            if (this.DecrementStock(response.data.item)) {
              if (response.data.item.type_price === 2) {
                this.$swal.fire({
                  input: 'text',
                  inputPlaceholder: 'INGRESE EL PRECIO $',
                  inputAttributes: {
                    autocapitalize: 'off',
                    id: 'newPrice',
                  },
                  showCancelButton: true,
                  confirmButtonText: 'ENTER',
                  cancelButtonText: 'CANCELAR',
                  html:
                    '<div class="card"> <div class="card-body"> <table class="" style="width:100%; table-layout:fixed;"> <tr> <td><button onclick="keyadd(1)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">1</button></td><td><button onclick="keyadd(2)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">2</button></td><td><button onclick="keyadd(3)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">3</button></td></tr><tr> <td><button onclick="keyadd(4)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">4</button></td><td><button onclick="keyadd(5)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">5</button></td><td><button onclick="keyadd(6)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">6</button></td></tr><tr> <td><button onclick="keyadd(7)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">7</button></td><td><button onclick="keyadd(8)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">8</button></td><td><button onclick="keyadd(9)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">9</button></td></tr><tr> <td><button onclick="keyadd(0)" class="btn btn-default" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">0</button></td><td><button onclick="keyadd(0,2)" class="btn btn-primary" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">00</button></td><td><button onclick="keyadd(0,3)" class="btn btn-info" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">000</button></td></tr><tr> <td colspan="2"><button onclick="keyadd(-1)" class="btn btn-danger" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">CE</button></td><td><button onclick="keyadd(-2)" class="btn btn-warning" style="font-size: 18px; width:100%; height:50px;" type="button" name="button">DEL</button></td></tr></table></div></div>',
                  showLoaderOnConfirm: true,
                  preConfirm: (price) => {
                    if (price <= 0) {
                      this.$swal.showValidationMessage(
                        'Precio debe ser mayor que 0.',
                      )
                    } else {
                      this.InsertManualItemToCart(
                        true,
                        response.data.item.name,
                        price,
                        1,
                      )
                      $('tbody').animate(
                        { scrollTop: parseInt($('tbody')[3].scrollHeight) },
                        'slow',
                      )
                    }
                  },
                })
              } else {
                this.code = ''
                $('#inputCode').focus()
                this.AddCarrito(response.data.item)
              }
            } else {
              this.$swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Sin Stock',
                showConfirmButton: false,
                timer: 1500,
              })
              $('#inputCode').val('')
              $('#inputCode').focus()
            }
          })
        }
      }
    },
    ProcessOrder: async function () {
      if (!this.discount) {
        this.discount = 0
      }
      // PUSH SALES
      if (this.countItems == 0) {
        this.$swal.fire('No hay items en el carrito')
        return
      }
      if (this.company_billing == 0 && !this.voucher) {
        this.$swal.fire('Ingrese el numero de folio antes de procesar.')
        return
      }
      if (this.typeDocument == 0) {
        await this.insertSales(
          true,
          this.PayType,
          false,
          this.voucher,
          false,
          this.typeDocument,
        )
        await this.handleOriginalPrintFlow('470')
      }
      // BOLETA
      else if (this.typeDocument == 1) {
        if (!this.ClientID) {
          this.ClientID = 0
        }
        try {
          if (this.TotalToPay == 0) {
            this.TotalToPay = this.amount
          }
          if (this.company_billing == 0) {
            //console.log('voucher', this.voucher)
            await this.insertSales(
              true,
              this.PayType,
              false,
              this.voucher,
              false,
              1,
            )
            this.type_discount = 0
            this.typeDocument = 0
            this.PayType = 1
            this.isInvoice = false
            this.TotalToPay = 0
            this.SendMail = 0
            this.search = ''
            this.$emit('clear')
            this.SearchClear()
            await this.handleOriginalPrintFlow('650')

            return
          } else {
            await axios({
              url:
                '/ticket/' +
                this.TotalToPay +
                '/' +
                this.PayType +
                '/' +
                this.discount +
                '/' +
                this.ClientID,
              method: 'post',
              data: this.shoping_cart,
            }).then((response) => {
              //console.log(response);
              // your action after success
              if (response.data.error) {
                this.$swal.fire(response.data.error, '', 'error')
              } else {
                if (response.data.page_config == false) {
                  this.$swal.fire(
                    'Debe completar la configuracion de la API para emitir un documento.!',
                    '',
                    'error',
                  )
                  return
                } else {
                  $('#generatedDocument').html(
                    '<iframe id="iframe" src="' +
                      response.data.urlPDF +
                      '" frameborder="0" height="600" width="100%"></iframe>',
                  )
                  $('#profile-tab').trigger('click')
                  this.ModalChangeWidth(2)
                  this.insertSales(
                    false,
                    this.PayType,
                    response.data.urlPDF,
                    response.data.idFolio,
                    response.data.informedSii,
                    1,
                  )
                  this.type_discount = 0
                  this.typeDocument = 0
                  this.PayType = 1
                  this.isInvoice = false
                  this.TotalToPay = 0
                  this.SendMail = 0
                  this.search = ''
                  this.$emit('clear')
                  this.SearchClear()
                }
              }
            })
          }
        } catch (e) {
          //console.log(e);
          this.$swal.fire(
            'Ocurrio un error al generar el documento.!',
            '',
            'error',
          )

          return
        }
      }
      // FACTURA
      else if (this.typeDocument == 2 || this.typeDocument == 3) {
        /*let rowsTotal= $('.tableBodyScroll >tbody >tr').length;
            if(rowsTotal >= 16){
              this.$swal.fire(
                'La factura debe tener un maximo de 16 filas!',
                '',
                'error'
              )

                return;
              }*/
        if (!this.ClientID && this.company_billing != 0) {
          this.$swal.fire('Debe asociar un Cliente!', '', 'error')
          //this.$refs.sales.userData();
          return
        }
        if (
          !this.formC.email ||
          !this.formC.runCompany ||
          !this.formC.city ||
          !this.formC.company ||
          !this.formC.municipality ||
          !this.formC.activity ||
          !this.formC.address ||
          !this.formC.companyOrPerson
        ) {
          if (this.company_billing != 0) {
            this.$swal.fire(
              'Debe completar todos los campos del formulario empresa',
            )
            return
          }
        }

        if (this.company_billing == 0 && !this.voucher) {
          this.$swal.fire('Ingrese el numero de folio antes de procesar.')
          return
        }

        if (this.company_billing != 0) {
          this.saveCompany(false)
        }

        let msgError = false
        let quotation = 0
        if (this.TotalToPay == 0) {
          this.TotalToPay = this.amount
        }
        if (this.typeDocument == 3) {
          quotation = 1
        }

        if (this.company_billing == 0) {
          await this.insertSales(
            true,
            this.PayType,
            false,
            this.voucher,
            false,
            2,
          )
          this.formC.reset()
          this.type_discount = 0
          this.typeDocument = 0
          this.PayType = 1
          this.isInvoice = false
          this.TotalToPay = 0
          this.SendMail = 0
          this.search = ''
          this.$emit('clear')
          this.SearchClear()
          await this.handleOriginalPrintFlow('470')
          return
        } else {
          await axios({
            url:
              '/invoice/' +
              this.TotalToPay +
              '/' +
              this.SendMail +
              '/' +
              this.PayType +
              '/' +
              this.discount +
              '/' +
              this.ClientID +
              '/' +
              quotation,
            method: 'post',
            data: this.shoping_cart,
          }).then((response) => {
            // your action after success
            if (response.data.company == 'incomplete') {
              msgError =
                'Debe completar el formulario empresa y guardar antes de generar un documento'
            } else {
              //console.log(response);
              if (response.data.error) {
                this.$swal.fire(response.data.error, '', 'error')
              } else {
                if (response.data.page_config == false) {
                  this.$swal.fire(
                    'Debe completar la configuracion de la API!',
                    '',
                    'error',
                  )
                  return
                } else {
                  $('#generatedDocument').html(
                    '<iframe src="' +
                      response.data.urlPDF +
                      '" frameborder="0" height="600" width="100%"></iframe>',
                  )
                  $('#profile-tab').trigger('click')
                  this.ModalChangeWidth(2)
                  this.insertSales(
                    false,
                    this.PayType,
                    response.data.urlPDF,
                    response.data.idFolio,
                    response.data.informedSii,
                    this.typeDocument,
                  )
                  this.formC.reset()
                  this.type_discount = 0
                  this.typeDocument = 0
                  this.PayType = 1
                  this.isInvoice = false
                  this.TotalToPay = 0
                  this.SendMail = 0
                  this.search = ''
                  this.$emit('clear')
                  this.SearchClear()
                }
              }
            }
          })
          if (msgError) {
            this.$swal.fire(msgError, '', 'error')
          }
        }
      }
    },
    async handleOriginalPrintFlow(previewHeight = '470') {
      try {
        const response = await axios.post('/sales/' + this.id_sale + '/print/original', {
          source: 'POS_AUTO',
        })

        const data = response.data
        if (data.agent_dispatched) {
          this.$swal.fire({
            position: 'top-center',
            icon: 'success',
            title: 'Voucher enviado a impresora local',
            showConfirmButton: false,
            timer: 1500,
          })
          return
        }
        if (data.agent_attempted) {
          this.$swal.fire(
            data.agent_message || 'No se pudo enviar el voucher a la impresora local.',
            '',
            'error',
          )
          return
        }

        if (this.printer === 0 || this.printer === '0') {
          if (this.optional_printer === 0 || this.optional_printer === '0') {
            this.$swal.fire({
              position: 'top-center',
              icon: 'success',
              title: 'Venta Procesada',
              showConfirmButton: false,
              timer: 1500,
            })
            return
          }

          await fetch(data.local_url).then((r) => r.json())
          this.$swal.fire({
            position: 'top-center',
            icon: 'success',
            title: 'Voucher enviado a impresora',
            showConfirmButton: false,
            timer: 1500,
          })
          return
        }

        this.$swal.fire({
          html:
            '<iframe src="' +
            data.preview_url +
            '#&zoom=180" width="100%" height="' +
            previewHeight +
            '" ></iframe>',
          showCloseButton: false,
          showCancelButton: false,
          focusConfirm: false,
          confirmButtonText: '<i class="fa fa-thumbs-up"></i> CERRAR',
          confirmButtonAriaLabel: 'Thumbs up, great!',
        })
      } catch (error) {
        const message =
          (error.response && error.response.data && error.response.data.message) ||
          'No fue posible imprimir el voucher.'
        this.$swal.fire(message, '', 'error')
      }
    },
    insertSales: async function (
      hide = true,
      PayType = false,
      urlPDF = false,
      folio = false,
      informedSii = false,
      docType = false,
    ) {
      await axios
        .post('/sales', {
          discount_type: this.type_discount,
          discount: this.discount,
          amount: this.amount,
          fk_cliente_id: this.ClientID,
          urlpdf: urlPDF,
          folio: folio,
          type_document: docType,
          informedSii: informedSii,
          payment_method: this.PayType,
        })

        .then(async (response) => {
          this.id_sale = response.data.id
          await axios.post('/items-to-sale', {
            fk_sales_id: this.id_sale,
            items: this.shoping_cart,
          })
          /* this.shoping_cart.forEach(miFuncion);
          async function miFuncion(elemento, indice) {x
            await axios.post("/sales/items", {
              fk_sales_id: response.data.id,
              code: elemento.details.code,
              name_item: elemento.details.name,
              price: elemento.details.price,
              quantity: elemento.quantity,
            });
          } */
        })
      if (hide == true) {
        $('#ShopCartProccess').modal('hide')
      }
      this.HardReset()
      this.SearchClear()
      this.GetPageConfig()
      // ACTUALIZAR LAS VENTAS EN EL COMPONENTE
      this.$refs.sales.getSales()
    },
    AddCarrito: async function (item) {
      if (this.DecrementStock(item)) {
        --item.stock
        await axios.post('/update/stock/' + item.code + '/1/2')
        $('#s_' + item.id).html(item.stock + ' uds')
        // El sku significa Stock Keeping Unit y es único para cada producto.
        const locationInCart = this.shoping_cart.findIndex((p) => {
          return p.details.id === item.id
        })
        // Si locationInCart devuelve -1 indica que no encontro una coincidencia
        if (locationInCart === -1) {
          this.shoping_cart.push({
            details: item,
            quantity: 1,
          })
          this.countItems++
        } else {
          this.shoping_cart[locationInCart].quantity++
          this.countItems++
        }
        this.setlocalStorage(this.shoping_cart)
        $('tbody').animate(
          { scrollTop: parseInt($('tbody')[3].scrollHeight) },
          'slow',
        )
        this.getTotal()
      } else {
        this.$swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Sin Stock',
          showConfirmButton: false,
          timer: 1500,
        })
        $('#inputCode').val('')
        $('#inputCode').focus()
      }
    },
    DecrementStock(item) {
      if (item.stock <= 0) {
        return false
      } else {
        return true
      }
    },
    ShopDeleteItem: async function (code, id, quantity, index) {
      this.shoping_cart.splice(index, 1)
      if (code) {
        await axios
          .post('/update/stock/' + code + '/' + quantity + '/1')
          .then((response) => {
            $('#s_' + id).html(response.data.stock + ' uds')
          })
      }
      this.setlocalStorage(this.shoping_cart)
      this.getCountItems()
      this.getTotal()
    },
    async SetQuantityManual(index, quantity, cart) {
      if (!cart.details.code) {
        this.$swal.fire({
          icon: 'warning',
          title: 'ATENCIÓN',
          html: 'Los items de precio variable no pueden incrementarse.',
          showConfirmButton: true,
        })
        return
      }
      await axios.post('/stock/' + cart.details.code).then(async (response) => {
        let stock = parseInt(response.data.stock) + parseInt(cart.quantity)
        if (quantity > stock) {
          $('#ManualQuantity_' + cart.details.id).val(cart.quantity)
          this.$swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Sin Stock',
            showConfirmButton: false,
            timer: 1500,
          })
          $('#inputCode').val('')
          $('#inputCode').focus()
          return
        } else {
          let Remainingstock = parseInt(stock) - parseInt(quantity)
          await axios
            .post('/set/stock/' + cart.details.id + '/' + Remainingstock)
            .then((response) => {
              $('#s_' + cart.details.id).html(Remainingstock + ' uds')
              this.shoping_cart[index].quantity = parseInt(quantity)
              this.setlocalStorage(this.shoping_cart)
              this.getCountItems()
              this.getTotal()
            })
        }
      })
    },
    SetQuantity: async function (
      index,
      quantity,
      cart = false,
      operation = false,
    ) {
      if (!cart.details.code) {
        this.$swal.fire({
          icon: 'warning',
          title: 'ATENCIÓN',
          html: 'Los items de precio variable no pueden incrementarse.',
          showConfirmButton: true,
        })
        return
      }
      if (quantity == 0) {
        return
      }

      if (operation == '-') {
        await axios
          .post('/stock/' + cart.details.code)
          .then(async (response) => {
            // URL //update/stock/CODIGO/CANTIDAD/OPERACION' // 1 SUMA 2 RESTA
            await axios
              .post('/update/stock/' + cart.details.code + '/1/1')
              .then((response) => {
                $('#s_' + cart.details.id).html(response.data.stock + ' uds')
                this.shoping_cart[index].quantity = quantity
                this.setlocalStorage(this.shoping_cart)
                this.getCountItems()
                this.getTotal()
              })
          })
      } else {
        await axios
          .post('/stock/' + cart.details.code)
          .then(async (response) => {
            if (response.data.stock == 0) {
              this.$swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Sin Stock',
                showConfirmButton: false,
                timer: 1500,
              })
              $('#inputCode').val('')
              $('#inputCode').focus()
            } else {
              await axios
                .post('/update/stock/' + cart.details.code + '/1/2')
                .then((response) => {
                  $('#s_' + cart.details.id).html(response.data.stock + ' uds')
                  this.shoping_cart[index].quantity = quantity
                  this.setlocalStorage(this.shoping_cart)
                  this.getCountItems()
                  this.getTotal()
                })
            }
          })
      }
    },
    getitems: async function () {
      await axios.post('/items/get').then((response) => {
        this.search_items = response.data.total
        this.items = response.data.data
      })
    },
    getCountItems: function () {
      var count = 0
      this.shoping_cart.forEach(miFuncion)
      function miFuncion(elemento, indice) {
        count += parseInt(elemento.quantity)
      }
      this.countItems = count
    },
    getTotal: function () {
      if (this.shoping_cart.lenght == 0) {
        this.amount = 0
      } else {
        var total = 0
        this.shoping_cart.forEach(miFuncion)
        function miFuncion(elemento, indice) {
          total += parseInt(elemento.details.price * elemento.quantity)
        }
        this.amount = total
      }
    },
    cancelPurchase(whitOutMsg) {
      if (this.countItems == 0) {
        this.$swal.fire('No hay items en el carrito')
      } else {
        if (whitOutMsg) {
          this.shoping_cart.forEach(miFuncion)
          async function miFuncion(elemento, indice) {
            await axios
              .post(
                '/update/stock/' +
                  elemento.details.code +
                  '/' +
                  elemento.quantity +
                  '/1',
              )
              .then((response) => {
                $('#s_' + elemento.details.id).html(
                  response.data.stock + ' uds',
                )
              })
          }
        } else {
          this.$swal
            .fire({
              title: 'Estas seguro?',
              text: 'Se eliminaran todos los items del carrito!',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#28a745',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Si, cancelar venta!',
            })
            .then((result) => {
              if (result.value) {
                this.shoping_cart.forEach(miFuncion)
                async function miFuncion(elemento, indice) {
                  await axios
                    .post(
                      '/update/stock/' +
                        elemento.details.code +
                        '/' +
                        elemento.quantity +
                        '/1',
                    )
                    .then((response) => {
                      $('#s_' + elemento.details.id).html(
                        response.data.stock + ' uds',
                      )
                    })
                }
                this.$swal.fire(
                  'Venta Anulada!',
                  'Su carrito esta vacio.',
                  'success',
                )
                this.HardReset()
              }
            })
        }
      }
    },
    HardReset: function () {
      this.shoping_cart = []
      this.setlocalStorage(this.shoping_cart)
      this.countItems = 0
      this.amount = 0
      this.type_discount = 0
      this.discount = ''
      this.discountCLP = 0
      this.TotalToPay = 0
      this.voucher = '0'
      this.search = ''
      this.$emit('clear')
    },
    async UpdateUrl() {
      this.page = 1
      this.urlInfinite = '/item/category/' + this.category
      await axios
        .get(this.urlInfinite + '?page=' + this.page)
        .then((response) => {
          this.search_items = response.data.total
          this.items = response.data.data
        })
      this.$refs.infiniteLoading.stateChanger.reset()
    },
    infiniteHandler: async function ($state) {
      let limit = this.items.length + 1
      await axios
        .get(this.urlInfinite + '?page=' + this.page)
        .then((response) => {
          //console.log(response)
          this.search_items = response.data.total
          //console.log(response);
          this.loadMore($state, response)
        })
    },
    loadMore: function ($state, response) {
      if (this.page == 1) {
        this.items = []
      }
      if (response.data.data.length) {
        this.items = this.items.concat(response.data.data)
        setTimeout(() => {
          $state.loaded()
        }, 1000)
        this.page = this.page + 1
        if (response.data.total == this.items.length) {
          $state.complete()
        }
      } else {
        $state.complete()
      }
    },
    Search: async function () {
      if (this.texto == false) {
        this.search_result = false
        this.UpdateUrl()
        return
      }
      if (this.category == 'all') {
        let url = '/pos/' + this.filter + '/' + this.texto
        await axios.post(url).then((response) => {
          if (response.data.total == 0) {
            this.items = []
            this.search_result = true
          } else {
            this.search_result = false
            this.urlInfinite = url
            this.search_items = response.data.total
            this.items = response.data.data
          }
        })
      } else {
        let url = '/pos/' + this.category + '/' + this.filter + '/' + this.texto
        await axios.post(url).then((response) => {
          //console.log(response);
          if (response.data.total == 0) {
            this.items = []
            this.search_result = true
          } else {
            this.search_result = false
            this.urlInfinite = url
            this.search_items = response.data.total
            this.items = response.data.data
          }
        })
      }
    },
  },
}
</script>

<style scoped>
.iframe {
  position: relative;
}
.iframe1 .ratio {
  display: block;
  width: 100%;
  height: 550px;
}
.iframe1 iframe {
  position: relative;
  top: 0;
  left: 0;
  width: 100%;
  height: 550px;
}
.modal-xl {
  max-width: 90% !important;
}
.tableBodyScroll tbody {
  display: block;
  height: calc(100vh - 220px);
  max-height: calc(100vh - 426px);
  overflow-y: scroll;
}

.tableBodyScrollModal tbody {
  display: block;
  height: 330px;
  max-height: 450px;
  overflow-y: scroll;
}

.tableBodyScroll thead,
tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}

/* Landscape phone to portrait tablet */
@media (max-width: 767px) {
  .tableBodyScroll thead,
  tbody tr {
    display: table;
    width: 100%;
    table-layout: auto;
  }
  .modal-lg {
    max-width: 100% !important;
  }
}
/* Landscape phones and down */
@media (max-width: 480px) {
  .tableBodyScroll thead,
  tbody tr {
    display: table;
    width: 100%;
    table-layout: auto;
  }
  .modal-lg {
    max-width: 100% !important;
  }
}

.modal-lg {
  max-width: 50%;
}

/** code by webdevtrick ( https://webdevtrick.com ) **/
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-thumb {
  border-radius: 30px;
  background: -webkit-gradient(
    linear,
    left top,
    left bottom,
    from(#ff8a00),
    to(#da1b60)
  );
  box-shadow: inset 2px 2px 2px rgba(255, 255, 255, 0.25),
    inset -2px -2px 2px rgba(0, 0, 0, 0.25);
}

.btn-group-xs > .btn,
.btn-xs {
  padding: 0.25rem 0.4rem;
  font-size: 0.875rem;
  line-height: 0.5;
  border-radius: 0.2rem;
}
.notify-badge {
  position: absolute;
  left: 5px;
  top: 40px;
  background: #ab35dc;
  text-align: center;
  border-radius: 0px 0px 10px 0px;
  color: white;
  padding: 5px 10px;
  font-size: 12px;
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

.tooltip[x-placement^='top'] {
  margin-bottom: 5px;
}

.tooltip[x-placement^='top'] .tooltip-arrow {
  border-width: 5px 5px 0 5px;
  border-left-color: transparent !important;
  border-right-color: transparent !important;
  border-bottom-color: transparent !important;
  bottom: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}

.tooltip[x-placement^='bottom'] {
  margin-top: 5px;
}

.tooltip[x-placement^='bottom'] .tooltip-arrow {
  border-width: 0 5px 5px 5px;
  border-left-color: transparent !important;
  border-right-color: transparent !important;
  border-top-color: transparent !important;
  top: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}

.tooltip[x-placement^='right'] {
  margin-left: 5px;
}

.tooltip[x-placement^='right'] .tooltip-arrow {
  border-width: 5px 5px 5px 0;
  border-left-color: transparent !important;
  border-top-color: transparent !important;
  border-bottom-color: transparent !important;
  left: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}

.tooltip[x-placement^='left'] {
  margin-right: 5px;
}

.tooltip[x-placement^='left'] .tooltip-arrow {
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
  box-shadow: 0 5px 30px rgba(black, 0.1);
}

.tooltip.popover .popover-arrow {
  border-color: #f9f9f9;
}

.tooltip[aria-hidden='true'] {
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.15s, visibility 0.15s;
}

.tooltip[aria-hidden='false'] {
  visibility: visible;
  opacity: 1;
  transition: opacity 0.15s;
}

input[type='radio'] {
  transform: scale(1.5);
}
.panel-tabs {
  position: relative;
  bottom: 0px;
  clear: both;
  border-bottom: 1px solid transparent;
}

.panel-tabs > li {
  float: left;
  margin-bottom: -1px;
}

.panel-tabs > li > a {
  margin-right: 2px;
  margin-top: 4px;
  line-height: 0.85;
  border: 1px solid transparent;
  border-radius: 4px 4px 0 0;
}

.panel-tabs > li > a:hover {
  border-color: transparent;
  color: #49304c;
  background-color: transparent;
}

.panel-primary > .panel-heading {
  color: #ffffff;
  background-color: #000000;
  border-color: #000000;
}

.panel-primary {
  border-color: #000000;
}

.panel-tabs > li.active > a,
.panel-tabs > li.active > a:hover,
.panel-tabs > li.active > a:focus {
  color: #ffffff;
  cursor: default;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  background-color: rgba(255, 255, 255, 0.23);
  border-bottom-color: transparent;
}

.tab-pane {
  height: calc(100vh - 220px);
  overflow-y: scroll;
}

/* iframe {
  width: 400px;
  height: 400px;
  border: 0;
} */
.zoom {
  -webkit-transform: scale(2); /* Safari and Chrome */
  transform: scale(2); /* zoom factor - prefix if needed */
  width: 200px; /* half of iframe width */
  height: 200px; /* half of iframe height */
  margin: 100px 0 0 100px; /* reposition iframe */
  overflow: hidden; /* remove big-ass scrollbars */
}
</style>
