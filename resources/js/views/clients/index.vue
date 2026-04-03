<template>
  <div class="" v-if="hidden">
    <div class="col-md-12" >
        <div class="card panel panel-default panel-table">
              <!-- Modal -->
              <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalTitle">Modal title</h5>
                      <button type="button" class="close" onclick='$("#exampleModal").modal("hide")' aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <form  @submit.prevent="editmode ? updateClient() : createClient()">
                      <div id="modalUserShow" style="display:none;">
                        <div class="modal-body">
                          <table class="table view-client">
                            <tr>
                              <th>RUN</th>
                              <td>{{ form.run }}</td>
                            </tr>
                            <tr>
                              <th>DIGITO VERIFICADOR</th>
                              <td>{{ form.verifying_digit }}</td>
                            </tr>
                            <tr>
                              <th>NOMBRE</th>
                              <td>{{ form.name }}</td>
                            </tr>
                            <tr>
                              <th>APELLIDOS</th>
                              <td>{{ form.last_name }}</td>
                            </tr>
                            <tr>
                              <th>EMAIL</th>
                              <td>{{ form.email }}</td>
                            </tr>
                            <tr>
                              <th>TELEFONO</th>
                              <td>{{ form.phone }}</td>
                            </tr>
                            <tr>
                              <th>FECHA DE CREACION</th>
                              <td>{{ form.created_at }} <br> {{ since(form.created_at) }}</td>
                            </tr>
                            <tr>
                              <th>ULTIMA ACTUALIZACION</th>
                              <td>{{ form.updated_at ? form.updated_at : '-----------' }} <br> {{ since(form.updated_at) }}</td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    <div class="modal-body" id="modalUserEdit">
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> RUN </i></span>
                        </div>
                        <input v-if="!editmode" id="run" v-model="form.run" v-on:keyup="formateaRut()" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('run') }" placeholder="INGRESE RUT">
                        <input readonly v-if="editmode" id="run" v-model="form.run" v-on:keyup="formateaRut()" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('run') }" placeholder="INGRESE RUT">
                        <input style="text-align: center; font-size:12px;" readonly  v-if="editmode" v-model="form.verifying_digit" class="ml-2" type="text" name="" value="">
                        <has-error :form="form" field="run"></has-error>
                      </div>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> Nombre </i></span>
                        </div>
                        <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Ingrese Nombre">
                        <has-error :form="form" field="name"></has-error>
                      </div>
                      <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> Apellidos </i></span>
                          </div>
                          <input v-model="form.last_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }" placeholder="Ingrese Apellidos">
                          <has-error :form="form" field="last_name"></has-error>
                      </div>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-envelope-square"> Email </i> </span>
                        </div>
                        <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Ingrese un Email">
                        <has-error :form="form" field="email"></has-error>
                      </div>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-phone-square-alt"> Telefono </i></span>
                        </div>
                        <input v-model="form.phone" type="text" class="form-control" id="exampleInputEmail1" :class="{ 'is-invalid': form.errors.has('phone') }" placeholder="Ingrese Telefono">
                        <has-error :form="form" field="phone"></has-error>
                      </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default ks-rounded" @click="CloseModal()">Cerrar</button>
                      <button id="btnUpdate" v-if="editmode" type="submit" class="btn btn-default ks-rounded">Guardar Cambios</button>
                      <button id="btnSave" v-if="!editmode" type="submit" class="btn btn-default ks-rounded">Crear Usuario</button>
                    </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- end modal -->
                  <div class="card-block" style="padding:15px;">
                      <div class="row">
                        <div class="col-md-2">
                        <div style="input-group">
                           <button class="form-control mb-2 mr-sm-2">{{ this.clientsData.total }} / {{ clientsmax }}</button>
                        </div>
                          </div>
                        <div class="col-md-2">
                                <select class="form-control" v-model="filter" title="FILTRAR POR: ">
                                  <option value="run">RUN</option>
                                  <option value="name">NOMBRE</option>
                                  <option value="last_name">APELLIDOS</option>
                                  <option value="email">EMAIL</option>
                                </select>
                          </div>
                          <div class="col-md-2">
                              <input style="" class="form-control" id="textField" v-on:keyup="searchResult" type="text" v-model="texto">
                          </div>
                          <div class="col-md-3">
                              <button class="btn btn-default btn-block" @click="newClient()"><font-awesome-icon :icon="['fas', 'user']" /> NUEVO CLIENTE </button>
                          </div>
                          <div class="col-md-3">
                            <button class="btn btn-default btn-block" @click="getResults"><font-awesome-icon :icon="['fas', 'sync']" /> ACTUALIZAR</button>
                          </div>
                          </div>
                          <hr>
                          <div class="table-responsive">
                          <table class="table table-sm table-bordered table-hover">
                          <thead class="thead-light">
                          <tr>
                              <th>RUN</th>
                              <th>EMAIL</th>
                              <th>NOMBRE</th>
                              <th>APELLIDOS</th>
                              <th>ACCIÓN</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr v-for="client, index in clientsData.data" :key="client.id" v-bind:id="'tr_'+client.id">
                              <td v-bind:id="'run_'+client.id">{{ client.run }}</td>
                              <td v-bind:id="'email_'+client.id">{{ client.email }}</td>
                              <td v-bind:id="'name_'+client.id">{{ client.name }}</td>
                              <td v-bind:id="'last_name_'+client.id">{{ client.last_name }}</td>
                              <td>
                                <button class="btn btn-default btn-sm" type="button"  @click="showClient(client)"><font-awesome-icon :icon="['fas', 'eye']" /> </button>
                                <button class="btn btn-default btn-sm" type="button"  @click="editClient(client)"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                <button class="btn btn-default btn-sm" type="button"  @click="deleteClient(client, index)" name="button"><font-awesome-icon :icon="['fas', 'trash']" /> </button>
                              </td>
                          </tr>
                          </tbody>
                      </table>
                <hr>
                <pagination :limit="6" :data="clientsData" @pagination-change-page="getResults">
                </pagination>
              </div>
            </div>
        </div>
    </div>
  </div>
  <div class="ks-column ks-page mt-4" v-else-if="!hidden">
    <div class="ks-page-header">
        <section class="ks-title-and-subtitle">
            <div class="ks-title-block">
                <h3 class="ks-main-title">App / Clientes</h3>
                <div class="ks-sub-title">Sección para crear, editar y eliminar clientes.</div>
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
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                      <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="modalTitle">Modal title</h5>
                                            <button type="button" class="close" onclick='$("#exampleModal").modal("hide")' aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <form  @submit.prevent="editmode ? updateClient() : createClient()">
                                            <div id="modalUserShow" style="display:none;">
                                              <div class="modal-body">
                                                <table class="table view-client">
                                                  <tr>
                                                    <th>RUN</th>
                                                    <td>{{ form.run }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>DIGITO VERIFICADOR</th>
                                                    <td>{{ form.verifying_digit }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>NOMBRE</th>
                                                    <td>{{ form.name }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>APELLIDOS</th>
                                                    <td>{{ form.last_name }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>EMAIL</th>
                                                    <td>{{ form.email }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>TELEFONO</th>
                                                    <td>{{ form.phone }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>FECHA DE CREACION</th>
                                                    <td>{{ form.created_at }} <br> {{ since(form.created_at) }}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>ULTIMA ACTUALIZACION</th>
                                                    <td>{{ form.updated_at ? form.updated_at : '-----------' }} <br> {{ since(form.updated_at) }}</td>
                                                  </tr>
                                                </table>
                                              </div>
                                            </div>
                                          <div class="modal-body" id="modalUserEdit">
                                            <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> RUN </i></span>
                                              </div>
                                              <input v-if="!editmode" id="run" v-model="form.run" v-on:keyup="formateaRut()" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('run') }" placeholder="INGRESE RUT">
                                              <input readonly v-if="editmode" id="run" v-model="form.run" v-on:keyup="formateaRut()" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('run') }" placeholder="INGRESE RUT">
                                              <input style="text-align: center; font-size:12px;" readonly  v-if="editmode" v-model="form.verifying_digit" class="ml-2" type="text" name="" value="">
                                              <has-error :form="form" field="run"></has-error>
                                            </div>
                                            <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> Nombre </i></span>
                                              </div>
                                              <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Ingrese Nombre">
                                              <has-error :form="form" field="name"></has-error>
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> Apellidos </i></span>
                                                </div>
                                                <input v-model="form.last_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }" placeholder="Ingrese Apellidos">
                                                <has-error :form="form" field="last_name"></has-error>
                                            </div>
                                            <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-envelope-square"> Email </i> </span>
                                              </div>
                                              <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Ingrese un Email">
                                              <has-error :form="form" field="email"></has-error>
                                            </div>
                                            <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-phone-square-alt"> Telefono </i></span>
                                              </div>
                                              <input v-model="form.phone" type="text" class="form-control" id="exampleInputEmail1" :class="{ 'is-invalid': form.errors.has('phone') }" placeholder="Ingrese Telefono">
                                              <has-error :form="form" field="phone"></has-error>
                                            </div>

                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-default ks-rounded" @click="CloseModal()">Cerrar</button>
                                            <button id="btnUpdate" v-if="editmode" type="submit" class="btn btn-default ks-rounded">Guardar Cambios</button>
                                            <button id="btnSave" v-if="!editmode" type="submit" class="btn btn-default ks-rounded">Crear Usuario</button>
                                          </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- end modal -->
                                        <div class="card-block" style="padding:15px;">
                                            <div class="row">
                                              <div class="col-md-2">
                                              <div style="input-group">
                                                 <button class="form-control mb-2 mr-sm-2">{{ this.clientsData.total }} / {{ clientsmax }}</button>
                                              </div>
                                                </div>
                                              <div class="col-md-2">
                                                      <select class="form-control" v-model="filter" title="FILTRAR POR: ">
                                                        <option value="run">RUN</option>
                                                        <option value="name">NOMBRE</option>
                                                        <option value="last_name">APELLIDOS</option>
                                                        <option value="email">EMAIL</option>
                                                      </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input style="" class="form-control" id="textField" v-on:keyup="searchResult" type="text" v-model="texto">
                                                </div>
                                                <div class="col-md-3">
                                                    <button class="btn btn-default btn-block" @click="newClient()"><font-awesome-icon :icon="['fas', 'user']" /> NUEVO CLIENTE </button>
                                                </div>
                                                <div class="col-md-3">
                                                  <button class="btn btn-default btn-block" @click="getResults"><font-awesome-icon :icon="['fas', 'sync']" /> ACTUALIZAR</button>
                                                </div>
                                                </div>
                                                <hr>
                                                <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-hover">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>RUN</th>
                                                    <th>EMAIL</th>
                                                    <th>NOMBRE</th>
                                                    <th>APELLIDOS</th>
                                                    <th>ACCIÓN</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="client, index in clientsData.data" :key="client.id" v-bind:id="'tr_'+client.id">
                                                    <td v-bind:id="'run_'+client.id">{{ client.run }}</td>
                                                    <td v-bind:id="'email_'+client.id">{{ client.email }}</td>
                                                    <td v-bind:id="'name_'+client.id">{{ client.name }}</td>
                                                    <td v-bind:id="'last_name_'+client.id">{{ client.last_name }}</td>
                                                    <td>
                                                      <button class="btn btn-default btn-sm" type="button"  @click="showClient(client)"><font-awesome-icon :icon="['fas', 'eye']" /> </button>
                                                      <button class="btn btn-default btn-sm" type="button"  @click="editClient(client)"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                                      <button class="btn btn-default btn-sm" type="button"  @click="deleteClient(client, index)" name="button"><font-awesome-icon :icon="['fas', 'trash']" /> </button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                      <hr>
                                      <pagination :limit="6" :data="clientsData" @pagination-change-page="getResults">
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
    import Vue from 'vue'
    import moment from 'moment'

    import VueSweetalert2 from 'vue-sweetalert2';
    import { Form, HasError, AlertError } from 'vform'
    import $ from 'jquery'

    window.Form = Form;

    Vue.component(HasError.name, HasError)
    Vue.component(AlertError.name, AlertError)

    Vue.use(VueSweetalert2);

    export default {
      props: {
      hidden: Boolean,
      },
      data() {
          return {
            // Our data object that holds the Laravel paginator data
            clientsTotal: 0,
            clientsData: {},
            editmode: false,
            filter: "run",
            texto: null,
            fromChild: '',
            form: new Form({
                    id:'',
                    run: '',
                    verifying_digit: '',
                    last_name: '',
                    name : '',
                    email: '',
                    phone: '',
                    created_at: '',
                    updated_at: '',
                    rutSinPuntos: '',
            }),
          }
        },

        mounted() {
          // Fetch initial results
          this.getResults();
          $("#textField").focus();
        },
        computed:{
          clientsmax(){
            return this.$store.getters.getPlanClientsMax;
          }
        },
        methods: {
          since: function (d) {
            moment.locale('es');
            return moment(d).fromNow();
          },
          dgv: function (T)    //digito verificador
          {
                var M=0,S=1;
          	  for(;T;T=Math.floor(T/10))
                S=(S+T%10*(9-M++%6))%11;
          	  //return S?S-1:'k';

                return S?S-1:'k';
          },
          RutF: function (rut) {

              var actual = rut.replace(/^0+/, "");
              if (actual != '' && actual.length > 1) {
                  var sinPuntos = actual.replace(/\./g, "");
                  var actualLimpio = sinPuntos.replace(/-/g, "");
                  var inicio = actualLimpio.substring(0, actualLimpio.length - 1);
                  var rutPuntos = "";
                  var i = 0;
                  var j = 1;
                  for (i = inicio.length - 1; i >= 0; i--) {
                      var letra = inicio.charAt(i);
                      rutPuntos = letra + rutPuntos;
                      if (j % 3 == 0 && j <= inicio.length - 1) {
                          rutPuntos = "." + rutPuntos;
                      }
                      j++;
                  }
                  var dv = actualLimpio.substring(actualLimpio.length - 1);
                  rutPuntos = rutPuntos + "-" + dv;
              }
              return rutPuntos;
          },
          formateaRut: function() {
            if(this.form.run.length >= 2) {
              this.rutSinPuntos = this.RutF(this.form.run);
              this.form.run = this.RutF(this.rutSinPuntos);
            }
          },
          updateClient: function() {

            //console.log(form);
            this.form.put('/update/clients/'+this.form.id)
                .then(response => {
                      $("#exampleModal").modal('hide');
                      axios.get('get/clients/id/' + this.form.id)
                      .then(response => {
                        this.clientsData.data.map((value, index) => {
                          if(value.id == response.data[0].id)
                          {
                            this.clientsData.data[index] = response.data[0];
                          }
                        })

                        $("#tr_"+this.form.id).addClass('table-success');
                        $("#run_"+this.form.id).html(this.form.run);
                        $("#last_name_"+this.form.id).html(this.form.last_name);
                        $("#name_"+this.form.id).html(this.form.name);
                        $("#email_"+this.form.id).html(this.form.email);

                        this.form.reset();

                        this.$swal.fire({
                          title: 'Actualizado',
                          icon: 'success',
                          html: 'El Cliente <strong>'+this.form.name+'</strong> ha sido actualizado.',
                          showCloseButton: true,
                          focusConfirm: false,
                          confirmButtonText:
                            'OK',
                        })
                        //console.log(response.data[0].);
                        /*setTimeout(function() {
                          $('body').addClass('modal-open');
                        }, 1000);*/
                        });
                  })

          },
          searchResult: function() {
            var value = this.texto;
            var filter = this.filter;
            if(!value) {
              value = "vacio";
              this.getResults();
              return;
            }

            axios.post('get/clients/'+filter+'/' + value)
              .then(response => {
                this.clientsData = response.data;

              });
          },
          CloseModal: function() {
            $("#exampleModal").modal("hide");
            /*setTimeout(function() {
              $('body').addClass('modal-open');
            }, 500);*/
          },
          newClient: function() {
            jQuery.noConflict();
            this.editmode = false;
            this.form.reset();
            $("#btnSave").show();
            $("#modalUserEdit").show();
            $("#modalUserShow").hide();
            $("#exampleModal").modal('show');
            $("#modalTitle").html("Crear Usuario");
            setTimeout(function(){
             $("#run").focus();
             }, 500);
          },
          editClient: function(user) {
            //console.log(user);
            jQuery.noConflict();
            this.editmode = true;
            this.form.reset();
            $("#btnSave").show();
            $("#modalUserEdit").show();
            $("#modalUserShow").hide();
            $("#exampleModal").modal('show');
            $("#modalTitle").html("Editar Usuario");
            this.form.fill(user);
          },
          showClient: function(user) {
            this.form.fill(user);
            jQuery.noConflict();
            this.buttonSave = false;
            this.editmode = false;
            $("#btnUpdate").hide();
            $("#btnSave").hide();
            $("#exampleModal").modal('show');
            $("#modalTitle").html("Detalles del Usuario");
            $("#modalUserEdit").hide();
            $("#modalUserShow").show();

          },
          createClient: function() {

            //console.log(this.form);
            this.form.post('/post/clients')
                .then(response => {
                    if(response.data.success == false) {
                      this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: 'Ha llegado al maximo de clientes<br> correspondiente a su plan!',
                      });
                      $('#exampleModal').modal('hide');

                    }
                    else {
                      $('#exampleModal').modal('hide');
                      this.$swal.fire({
                        title: 'Creado',
                        icon: 'success',
                        html:
                          'El Cliente <strong>'+this.form.name+'</strong> ha sido creado.',
                        showCloseButton: true,
                        focusConfirm: false,
                        confirmButtonText:
                          'OK',
                      })
                      /*setTimeout(function() {
                        $('body').addClass('modal-open');
                      }, 1000);*/
                      this.getResults();

                    }
                })
                .catch((response) => {
                  //console.log(response);

                });
          },
          deleteClient: function(client, index) {

            this.$swal.fire({
              title: 'Esta seguro?',
              text: "No podrás revertir esto.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#28a745',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Si, Eliminar!'
              }).then((result) => {
              if (result.value) {
                axios.delete('/delete/clients/' + client.id)
                .then(response => {
                  this.clientsData.data.splice(index, 1);
                  --this.clientsData.total;
                  this.$swal.fire({
                    title: 'Eliminado',
                    icon: 'success',
                    html:
                      'El Cliente <strong>'+client.name+'</strong> ha sido eliminado.',
                    showCloseButton: true,
                    focusConfirm: false,
                    confirmButtonText:
                      'OK',
                  })
                  this.clientsTotal = this.clientsData.total;
                  /*setTimeout(function() {
                    $('body').addClass('modal-open');
                  }, 1000);*/
                });
              }
            })

          },
          // Our method to GET results from a Laravel endpoint
          getResults(page = 1) {

            axios.post('/get/clients?page=' + page)
              .then(response => {
                //console.log(response);
                this.clientsData = response.data;
                this.clientsTotal = this.clientsData.total;
                //console.log();

              });
          }
       }
    }
</script>
<style scoped>
.view-client th {
  font-size: 14px !important;
  color: black !important;
}
.btn-group-xs > .btn, .btn-xs {
  padding: .25rem .4rem;
  font-size: .875rem;
  line-height: .5;
  border-radius: .2rem;
}
.pagination { justify-content: center!important; }
</style>
