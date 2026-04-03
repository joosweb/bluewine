<template>
  <div class="" v-if="hidden">
    <!-- Modal -->
    <div
      class="modal fade"
      id="CreateUpdateItem"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editmode ? "EDITAR ITEM" : "CREAR ITEM" }}
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
          <form @submit.prevent="editmode ? editPost() : newItemPost()">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">NOMBRE</label>
                    <input
                      v-model="form.name"
                      type="text"
                      class="form-control"
                    />
                    <has-error :form="form" field="name"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">CATEGORIA</label>
                    <select v-model="form.category_id" class="form-control">
                      <option value="">SELECCIONE UNA CATEGORIA</option>
                      <option
                        v-for="category in this.categories"
                        :key="category.id"
                        :value="category.id"
                      >
                        {{ category.name }}
                      </option>
                    </select>
                    <has-error :form="form" field="category_id"></has-error>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">PRECIO COMPRA</label>
                    <input
                      v-model="form.purchase_price"
                      type="number"
                      class="form-control"
                    />
                    <has-error :form="form" field="purchase_price"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">PRECIO</label>
                    <input
                      v-model="form.price"
                      type="number"
                      class="form-control"
                    />
                    <has-error :form="form" field="price"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">STOCK</label>
                    <input
                      v-model="form.stock"
                      type="number"
                      class="form-control"
                    />
                    <has-error :form="form" field="stock"></has-error>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">CODIGO</label>
                    <input
                      v-on:keydown.enter.prevent
                      v-model="form.code"
                      type="text"
                      class="form-control"
                    />
                    <button
                      @click="SkuGenerator"
                      type="button"
                      class="mt-1"
                      name="button"
                    >
                      GENERAR CODIGO
                    </button>
                    <small id="emailHelp" class="form-text text-muted"
                      >Utilize su lector de codigo de barras, genere
                      automaticamente o ingrese manualmente.</small
                    >
                    <has-error :form="form" field="code"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">TIPO DE ITEM</label>
                    <select
                      class="form-control"
                      v-model="form.type_price"
                      name=""
                    >
                      <option value="1">PRECIO FIJO</option>
                      <option value="2">PRECIO VARIABLE</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">IMAGEN</label>
                    <input
                      type="file"
                      accept="image/*"
                      id="uploadFile"
                      @change="uploadPhoto"
                      class="form-control-file"
                    />
                    <has-error :form="form" field="img"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <img
                    v-if="form.photo"
                    class="img-thumbnail mt-2"
                    width="150px"
                    :src="form.photo"
                    alt=""
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
              >
                CERRAR
              </button>
              <button type="submit" class="btn btn-default">GUARDAR</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="barcodeModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              <form class="form-inline" action="index.html" method="post">
                <div class="form-group">
                  <div class="btn-group" role="group">
                    <button
                      class="btn btn-default mr-sm-1"
                      type="button"
                      name="button"
                    >
                      {{ iterator }}
                    </button>
                    <input
                      class="form-control mr-sm-1"
                      type="text"
                      @keyup="UpdateIterator"
                      placeholder="INGRESO MANUAL"
                      v-model="inputIterator"
                    />
                    <button
                      class="btn btn-default mr-sm-1"
                      title="DISMINUIR"
                      @click="iterator--"
                      type="button"
                    >
                      -
                    </button>
                    <button
                      class="btn btn-default mr-sm-1"
                      title="AUMENTAR"
                      @click="iterator++"
                      type="button"
                    >
                      +
                    </button>
                    <button
                      class="btn btn-default mr-sm-1"
                      title="IMPRIMIR"
                      @click="PrintBardCode()"
                      type="button"
                    >
                      <font-awesome-icon :icon="['fas', 'print']" />&nbsp;
                      IMPRIMIR
                    </button>
                  </div>
                  <hr />
                </div>
              </form>
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
          <div
            class="modal-body"
            id="bardCodeZone"
            style="height: 500px; overflow: scroll"
          >
            <div class="row">
              <div
                class="col-md-4"
                v-for="(n, index) in this.iterator"
                :key="index"
              >
                <barcode height="50" v-bind:value="bardcode">
                  Show this if the rendering fails.
                </barcode>
              </div>
            </div>
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
    <div class="col-md-12">
      <div class="card panel panel-default panel-table">
        <div class="card-block" style="padding: 15px">
          <form class="form-inline" @submit.prevent="">
            <div style="input-group">
              <button class="form-control mb-2 mr-sm-2">
                {{ TotalItems }} / {{ limititems }}
              </button>
            </div>
            <label class="sr-only" for="inlineFormInputName2">Name</label>
            <select class="form-control mb-2 mr-sm-2" v-model="filter">
              <option value="code" selected>CODIGO</option>
              <option value="name">NOMBRE</option>
              <!--<option value="T2.name">NOMBRE</option>-->
            </select>
            <div class="input-group">
              <input
                @keyup="searchItem"
                v-model="inputValue"
                type="text"
                class="form-control mb-2 mr-sm-2"
                autofocus
              />
            </div>
            <div class="input-group mb-2 mr-sm-2">
              <button
                @click="newItem()"
                type="button"
                class="btn btn-default"
                name="button"
              >
                NUEVO ITEM
              </button>
            </div>
            <div class="input-group mb-2 mr-sm-2">
              <button
                @click="getItems"
                type="button"
                class="btn btn-default"
                name="button"
              >
                ACTUALIZAR
              </button>
            </div>
          </form>
          <hr />
          <div class="table-responsive">
            <table
              class="table table-sm table-bordered table-hover"
              style="table-layout: auto"
            >
              <thead class="thead-light">
                <tr>
                  <th>IMAGEN</th>
                  <th>CODIGO</th>
                  <th>NOMBRE</th>
                  <th>CATEGORIA</th>
                  <th>PRECIO COMPRA</th>
                  <th>PRECIO</th>
                  <th>STOCK</th>
                  <th>ACCIÓN</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in this.items.data"
                  :key="item.id"
                  v-bind:id="'tr_' + item.id"
                >
                  <td v-bind:id="'photo_' + item.id">
                    <img
                      width="50"
                      :src="item.photo"
                      class="rounded-circle"
                      alt=""
                    />
                  </td>
                  <td v-bind:id="'code_' + item.id">
                    <div style="font-size: 12px" class="badge badge-default">
                      {{ item.code }}
                    </div>
                  </td>
                  <td v-bind:id="'name_' + item.id">{{ item.name }}</td>
                  <td v-bind:id="'category_id_' + item.id">
                    <div class="badge badge-secondary">{{ item.cn }}</div>
                  </td>
                  <td width="10%" v-bind:id="'' + item.id">
                    $ {{ number_format(item.price) }}
                  </td>
                  <td v-bind:id="'stock_' + item.id">{{ item.stock }}</td>
                  <td>
                    <div class="btn-group" role="group">
                      <button
                        title="IMPRIMIR"
                        @click="Bardcode(item.code)"
                        class="btn btn-default btn-sm mr-sm-1"
                        type="button"
                        name="button"
                      >
                        <font-awesome-icon :icon="['fas', 'print']" />
                      </button>
                      <button
                        title="EDITAR"
                        @click="EditItem(item)"
                        class="btn btn-default btn-sm mr-sm-1"
                        type="button"
                        name="button"
                      >
                        <font-awesome-icon :icon="['fas', 'edit']" />
                      </button>
                      <!--<button title="DETALLES" @click="" class="mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>-->
                      <button
                        title="ELIMINAR"
                        @click="DeleteItem(item.id, index)"
                        class="btn btn-default btn-sm"
                        type="button"
                        name="button"
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" />
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="withoutResults">
                  <td colspan="7">
                    <div
                      class="alert alert-info alert-dismissible fade show"
                      role="alert"
                    >
                      <strong>Sin resultados!</strong> no se encontraron
                      resultados en la busqueda.
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <hr />
            <pagination
              :limit="6"
              :data="items"
              @pagination-change-page="getItems"
            >
            </pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="ks-column ks-page mt-4" v-else-if="!hidden">
    <!-- Modal -->
    <div
      class="modal fade"
      id="CreateUpdateItem"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editmode ? "EDITAR ITEM" : "CREAR ITEM" }}
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
          <form @submit.prevent="editmode ? editPost() : newItemPost()">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">NOMBRE</label>
                    <input
                      v-model="form.name"
                      type="text"
                      class="form-control"
                    />
                    <has-error :form="form" field="name"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">CATEGORIA</label>
                    <select v-model="form.category_id" class="form-control">
                      <option value="">SELECCIONE UNA CATEGORIA</option>
                      <option
                        v-for="category in this.categories"
                        :key="category.id"
                        :value="category.id"
                      >
                        {{ category.name }}
                      </option>
                    </select>
                    <has-error :form="form" field="category_id"></has-error>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">PRECIO COMPRA</label>
                    <input
                      v-model="form.purchase_price"
                      type="number"
                      class="form-control"
                    />
                    <has-error :form="form" field="purchase_price"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">PRECIO</label>
                    <input
                      v-model="form.price"
                      type="number"
                      class="form-control"
                    />
                    <has-error :form="form" field="price"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">STOCK</label>
                    <input
                      v-model="form.stock"
                      type="number"
                      class="form-control"
                    />
                    <has-error :form="form" field="stock"></has-error>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">TIPO DE ITEM</label>
                    <select
                      class="form-control"
                      v-model="form.type_price"
                      name=""
                    >
                      <option value="1">PRECIO FIJO</option>
                      <option value="2">PRECIO VARIABLE</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">IMAGEN</label>
                    <input
                      type="file"
                      accept="image/*"
                      id="uploadFile"
                      @change="uploadPhoto"
                      class="form-control-file"
                    />
                    <has-error :form="form" field="img"></has-error>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">CODIGO</label>
                    <input
                      v-on:keydown.enter.prevent
                      v-model="form.code"
                      type="text"
                      class="form-control"
                    />
                    <button
                      @click="SkuGenerator"
                      type="button"
                      class="mt-1"
                      name="button"
                    >
                      GENERAR CODIGO
                    </button>
                    <small id="emailHelp" class="form-text text-muted"
                      >Utilize su lector de codigo de barras, genere
                      automaticamente o ingrese manualmente.</small
                    >
                    <has-error :form="form" field="code"></has-error>
                  </div>
                </div>

                <div class="col-md-6">
                  <img
                    v-if="form.photo"
                    class="img-thumbnail mt-2"
                    width="150px"
                    :src="form.photo"
                    alt=""
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
              >
                CERRAR
              </button>
              <button type="submit" class="btn btn-default">GUARDAR</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="barcodeModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              <form class="form-inline" action="index.html" method="post">
                <div class="form-group">
                  <div class="btn-group" role="group">
                    <button
                      class="btn btn-default mr-sm-1"
                      type="button"
                      name="button"
                    >
                      {{ iterator }}
                    </button>
                    <input
                      class="form-control mr-sm-1"
                      type="text"
                      @keyup="UpdateIterator"
                      placeholder="INGRESO MANUAL"
                      v-model="inputIterator"
                    />
                    <button
                      class="btn btn-default mr-sm-1"
                      title="DISMINUIR"
                      @click="iterator--"
                      type="button"
                    >
                      -
                    </button>
                    <button
                      class="btn btn-default mr-sm-1"
                      title="AUMENTAR"
                      @click="iterator++"
                      type="button"
                    >
                      +
                    </button>
                    <button
                      class="btn btn-default mr-sm-1"
                      title="IMPRIMIR"
                      @click="PrintBardCode()"
                      type="button"
                    >
                      <font-awesome-icon :icon="['fas', 'print']" />&nbsp;
                      IMPRIMIR
                    </button>
                  </div>
                  <hr />
                </div>
              </form>
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
          <div
            class="modal-body"
            id="bardCodeZone"
            style="height: 500px; overflow: scroll"
          >
            <div class="row">
              <div
                class="col-md-4"
                v-for="(n, index) in this.iterator"
                :key="index"
              >
                <barcode height="50" v-bind:value="bardcode">
                  Show this if the rendering fails.
                </barcode>
              </div>
            </div>
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
    <div class="ks-page-header">
      <section class="ks-title-and-subtitle">
        <div class="ks-title-block">
          <h3 class="ks-main-title">App / Items</h3>
          <div class="ks-sub-title">
            Sección crear, editar y eliminar Items.
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
                  <div class="card-block" style="padding: 15px">
                    <form class="form-inline" @submit.prevent="">
                      <div style="input-group">
                        <button class="form-control mb-2 mr-sm-2">
                          {{ TotalItems }} / {{ limititems }}
                        </button>
                      </div>
                      <label class="sr-only" for="inlineFormInputName2"
                        >Name</label
                      >
                      <select
                        class="form-control mb-2 mr-sm-2"
                        v-model="filter"
                      >
                        <option value="code" selected>CODIGO</option>
                        <option value="name">NOMBRE</option>
                        <!--<option value="T2.name">NOMBRE</option>-->
                      </select>
                      <div class="input-group">
                        <input
                          @keyup="searchItem"
                          v-model="inputValue"
                          type="text"
                          class="form-control mb-2 mr-sm-2"
                          autofocus
                        />
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button
                          @click="newItem()"
                          type="button"
                          class="btn btn-default"
                          name="button"
                        >
                          NUEVO ITEM
                        </button>
                      </div>
                      <div class="input-group mb-2 mr-sm-2">
                        <button
                          @click="getItems"
                          type="button"
                          class="btn btn-default"
                          name="button"
                        >
                          ACTUALIZAR
                        </button>
                      </div>
                    </form>
                    <hr />
                    <div class="table-responsive">
                      <table
                        class="table table-sm table-bordered table-hover"
                        style="table-layout: auto"
                      >
                        <thead class="thead-light">
                          <tr>
                            <th>IMAGEN</th>
                            <th>CODIGO</th>
                            <th>NOMBRE</th>
                            <th>CATEGORIA</th>
                            <th>PRECIO COMPRA</th>
                            <th>PRECIO</th>
                            <th>STOCK</th>
                            <th>ACCIÓN</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(item, index) in this.items.data"
                            :key="item.id"
                            v-bind:id="'tr_' + item.id"
                          >
                            <td v-bind:id="'photo_' + item.id">
                              <img
                                width="50"
                                :src="item.photo"
                                class="rounded-circle"
                                alt=""
                              />
                            </td>
                            <td v-bind:id="'code_' + item.id">
                              <div
                                style="font-size: 12px"
                                class="badge badge-default"
                              >
                                {{ item.code }}
                              </div>
                            </td>
                            <td v-bind:id="'name_' + item.id">
                              {{ item.name }}
                            </td>
                            <td v-bind:id="'category_id_' + item.id">
                              <div class="badge badge-secondary">
                                {{ item.cn }}
                              </div>
                            </td>
                            <td
                              width="10%"
                              v-bind:id="'purchase_price_' + item.id"
                            >
                              $ {{ number_format(item.purchase_price) }}
                            </td>
                            <td width="10%" v-bind:id="'price_' + item.id">
                              $ {{ number_format(item.price) }}
                            </td>
                            <td v-bind:id="'stock_' + item.id">
                              {{ item.stock }}
                            </td>
                            <td>
                              <div class="btn-group" role="group">
                                <button
                                  title="IMPRIMIR"
                                  @click="Bardcode(item.code)"
                                  class="btn btn-default btn-sm mr-sm-1"
                                  type="button"
                                  name="button"
                                >
                                  <font-awesome-icon :icon="['fas', 'print']" />
                                </button>
                                <button
                                  title="EDITAR"
                                  @click="EditItem(item)"
                                  class="btn btn-default btn-sm mr-sm-1"
                                  type="button"
                                  name="button"
                                >
                                  <font-awesome-icon :icon="['fas', 'edit']" />
                                </button>
                                <!--<button title="DETALLES" @click="" class="mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>-->
                                <button
                                  title="ELIMINAR"
                                  @click="DeleteItem(item.id, index)"
                                  class="btn btn-default btn-sm"
                                  type="button"
                                  name="button"
                                >
                                  <font-awesome-icon :icon="['fas', 'trash']" />
                                </button>
                              </div>
                            </td>
                          </tr>
                          <tr v-if="withoutResults">
                            <td colspan="7">
                              <div
                                class="
                                  alert alert-info alert-dismissible
                                  fade
                                  show
                                "
                                role="alert"
                              >
                                <strong>Sin resultados!</strong> no se
                                encontraron resultados en la busqueda.
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <hr />
                      <pagination
                        :limit="6"
                        :data="items"
                        @pagination-change-page="getItems"
                      >
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
</template>

<script>
import Vue from "vue";
import VueBarcode from "vue-barcode";

import VueSweetalert2 from "vue-sweetalert2";
import { Form, HasError, AlertError } from "vform";
import $ from "jquery";

window.Form = Form;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

Vue.use(VueSweetalert2);

export default {
  props: {
    hidden: Boolean,
  },
  components: {
    barcode: VueBarcode,
  },
  data() {
    return {
      items: {},
      categories: {},
      inputValue: "",
      filter: "code",
      withoutResults: false,
      bardcode: 0,
      iterator: 15,
      inputIterator: null,
      editmode: false,
      form: new Form({
        id: "",
        code: "",
        name: "",
        category_id: "",
        proveedor_id: "",
        price: "",
        purchase_price: "",
        type_price: 1,
        photo: "",
        stock: "",
      }),
    };
  },
  created() {
    this.getItems();
    this.getCategories();
  },
  mounted() {
    //console.log('Component mounted.')
  },
  computed: {
    limititems() {
      return this.$store.getters.getPlanItemsMax;
    },
    TotalItems() {
      return this.items.total;
    },
  },
  methods: {
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
    SkuGenerator() {
      this.form.code = parseInt(Math.random() * 1000000000, 10);
    },
    getCategories() {
      axios.post("/select/categories").then((response) => {
        //console.log(response);
        this.categories = response.data;
      });
    },
    getItems(page = 1) {
      axios.post("/item?page=" + page).then((response) => {
        //console.log(response.data);
        this.items = response.data;
      });
    },
    uploadPhoto(e) {
      let file = e.target.files[0];
      let reader = new FileReader();

      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          //console.log('RESULT', reader.result)
          this.form.photo = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },
    newItem() {
      jQuery.noConflict();
      this.form.reset();
      this.editmode = false;
      $("#CreateUpdateItem").modal("show");
    },
    EditItem: function (item) {
      jQuery.noConflict();
      this.editmode = true;
      this.form.fill(item);
      $("#CreateUpdateItem").modal("show");
    },
    newItemPost() {
      this.form.post("items/post").then((response) => {
        //console.log(response);
        if (response.data.success == false) {
          this.$swal.fire({
            icon: "error",
            title: "Oops...",
            html: "Ha llegado al maximo de items<br> correspondiente a su plan!",
          });
          $("#CreateUpdateItem").modal("hide");
        } else {
          this.getItems();
          $("#CreateUpdateItem").modal("hide");
          this.$swal.fire({
            title: "Creado",
            icon: "success",
            html:
              "El Item <strong>" + this.form.name + "</strong> ha sido creado.",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: "OK",
          });
        }
      });
    },
    editPost() {
      this.form.put("/update/item/" + this.form.id).then((response) => {
        axios.get("/item/id/" + this.form.id).then((response) => {
          console.log(response);
          this.items.data.map((value, index) => {
            if (value.id == response.data[0].id) {
              this.items.data[index] = response.data[0];
            }
          });

          $("#CreateUpdateItem").modal("hide");
          $("#tr_" + this.form.id).addClass("table-success");
          $("#name_" + this.form.id).html(response.data[0].name);
          $("#code_" + this.form.id).html(
            '<div style="font-size:12px;" class="badge badge-default">' +
              response.data[0].code +
              "<div>"
          );
          /*$("#code_"+this.form.id).html('<svg id="barcode"></svg>');*/
          $("#photo_" + this.form.id).html(
            '<img width="50" src="' +
              response.data[0].photo +
              '" class="rounded-circle" alt="">'
          );
          $("#category_id_" + this.form.id).html(
            '<div class="badge badge-secondary">' +
              response.data[0].cn +
              "</div>"
          );
          $("#price_" + this.form.id).html(
            "$ " + this.number_format(response.data[0].price)
          );
          $("#stock_" + this.form.id).html(response.data[0].stock);

          /*JsBarcode("#barcode", response.data[0].code , {
                    format: "CODE128",
                    height: 30,
                    fontSize: 13
                });*/

          this.form.reset();

          this.$swal.fire({
            title: "Actualizado",
            icon: "success",
            html:
              "El item <strong>" +
              this.form.name +
              "</strong> ha sido actualizado.",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: "OK",
          });
        });
      });
    },
    DeleteItem(id, index) {
      axios.post("/get_sales_id/" + id).then((response) => {
        this.$swal
          .fire({
            title: "Esta seguro?",
            html:
              "Eliminar un item puede traer una inconsistencia en los reportes de ventas, solo se recomienda eliminar si no hay ventas relacionadas a este item, de lo contrario se hara bajo su responsabilidad.! <hr> Ventas relacionadas: " +
              response.data +
              " ",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, Eliminar!",
          })
          .then((result) => {
            if (result.value) {
              axios.delete("/items/delete/" + id).then((response) => {
                this.items.data.splice(index, 1);
                --this.items.total;
                this.getItems();
                this.$swal.fire(
                  "Eliminado!",
                  "El item ha sido eliminado.",
                  "success"
                );
              });
            }
          });
      });
    },
    UpdateIterator() {
      let value = parseInt(this.inputIterator);
      if (!value) {
        this.iterator = 9;
      } else {
        this.iterator = parseInt(this.inputIterator);
      }
    },
    Bardcode(code) {
      this.bardcode = code;
      jQuery.noConflict();
      $("#barcodeModal").modal("show");
    },
    PrintBardCode() {
      // Get HTML to print from element
      const prtHtml = document.getElementById("bardCodeZone").innerHTML;

      // Get all stylesheets HTML
      let stylesHtml = "";
      for (const node of [
        ...document.querySelectorAll('link[rel="stylesheet"], style'),
      ]) {
        stylesHtml += node.outerHTML;
      }

      // Open the print window
      const WinPrint = window.open(
        "",
        "",
        "left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0"
      );

      WinPrint.document.write(`<!DOCTYPE html>
            <html>
              <head>
                ${stylesHtml}
              </head>
              <body>
                ${prtHtml}
              </body>
            </html>`);

      WinPrint.document.close();
      WinPrint.focus();
      WinPrint.print();
      WinPrint.close();
    },
    searchItem() {
      if (this.inputValue == false) {
        this.withoutResults = false;
        return this.getItems();
      }
      axios
        .get("item/" + this.filter + "/" + this.inputValue)
        .then((response) => {
          if (response.data.total == 0) {
            this.items = {};
            this.withoutResults = true;
          } else {
            this.withoutResults = false;
            this.items = response.data;
          }
        });
    },
  },
};
</script>

<style scoped>
.pagination {
  justify-content: center !important;
}
</style>
