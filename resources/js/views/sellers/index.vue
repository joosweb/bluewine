<template>
  <div class="ks-column ks-page">
    <!-- Modal -->
    <div class="modal fade" id="newSeller" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">NUEVO VENDEDOR</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editmode ? EditSellerSave() : newSellerSave()">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div v-if="error">
                   <b>Por favor, corrija el(los) siguiente(s) error(es):</b><hr>
                   <div class="alert alert-danger" role="alert">
                     <ul>
                       <li v-for="error in errors">{{ error }}</li>
                     </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">RUN</label>
                  <input type="text" v-model="run" v-on:keyup="formateaRut()" class="form-control" >
                  <small id="emailHelp" class="form-text text-muted">Ingrese su rut sin puntos ni guiones.</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">NOMBRE</label>
                  <input type="text" v-model="name" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">APELLIDOS</label>
                  <input type="text" v-model="last_name" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">EMAIL</label>
                  <input type="email" v-model="email" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputPassword1">CONTRASEÑA</label>
                  <input type="password" v-model="password" class="form-control">
                  <small v-if="editmode" class="form-text text-muted">Si se deja en blanco se mantendra la contraseña actual</small>
                  <small v-if="!editmode" class="form-text text-muted">Si se deja en blanco el sistema le generara una.</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputPassword1">ESTADO</label>
                  <select v-model="status" class="form-control" name="">
                    <option value="1">ACTIVADO</option>
                    <option value="0">DESACTIVADO</option>
                  </select>
                  <small id="emailHelp" class="form-text text-muted">Si esta desactivado el vendedor no podrá iniciar sesión.</small>
                </div>
              </div>
              <!--<div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="exampleInputPassword1">IMG PERFIL.</label>
                      <input type="file" class="form-control-file">
                    </div>
                  </div>
                </div>
              </div>-->
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <div class="checkbox">
                         <label class="checkbox-bootstrap checkbox-lg">
                             <input v-model="sendmail" type="checkbox">
                             <span class="checkbox-placeholder"></span>
                             Enviar email <hr>
                             <small id="emailHelp" class="form-text text-muted">Se enviara un email con los datos de acceso al sistema.</small>
                         </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
            <button type="submit" class="btn btn-default">GUARDAR</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <div class="ks-page-header">
        <section class="ks-title-and-subtitle">
            <div class="ks-title-block">
                <h3 class="ks-main-title">App / Vendedores</h3>
                <div class="ks-sub-title">Sección para crear, editar, eliminar vendedores.</div>
            </div>
        </section>
    </div>
        <div class="ks-page-content">
          <div class="ks-page-content-body tables-page">
              <div class="ks-nav-body-wrapper">
                  <div class="container ks-rows-section">
                      <div class="row">
                          <div class="col-md-12 mt-4">
                              <div class="card panel panel-default panel-table">
                                <div class="card-block" style="padding:15px;">
                                  <form class="form-inline">
                                  <div style="input-group">
                                     <button class="form-control mb-2 mr-sm-2">{{ this.sellers.total ? this.sellers.total : 0 }} / {{ maxsellers }} </button>
                                  </div>
                                  <select class="form-control mb-2 mr-sm-2">
                                    <option value="run" selected>RUN</option>
                                    <!--<option value="T2.name">NOMBRE</option>-->
                                  </select>
                                  <div class="input-group">
                                    <input @keyup="searchResult" v-model="text" type="text" class="form-control mb-2 mr-sm-2" autofocus>
                                  </div>
                                  <div class="input-group mb-2 mr-sm-2">
                                    <button @click="newSellerModal()" type="button" class="btn btn-default" name="button">NUEVO VENDEDOR</button>
                                  </div>
                                  <div class="input-group mb-2 mr-sm-2">
                                    <button @click="getSellers" type="button" class="btn btn-default" name="button">ACTUALIZAR</button>
                                  </div>
                                </form>
                                <hr>
                                  <div class="row">
                                    <div class="col-md-4" v-for="s in this.sellers.data">
                                      <div class="card">
                                        <div class="card-header bg-primary">
                                          <div class="float-left">
                                            <div class="badge badge-info">
                                              {{ s.name }}
                                            </div>
                                          </div>
                                          <div class="float-right">
                                            <button @click="editSeller(s)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                            <button @click="deleteSeller(s)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                          </div>
                                        </div>
                                        <div class="card-body">
                                          <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                              <thead class="thead-light">
                                                <tr>
                                                  <th>IMAGEN DE PERFIL</th>
                                                  <td>
                                                    <img width="100" :src="s.photo ? s.photo : '/img/user_default.jpeg'" class="img-thumbnail" alt="">
                                                  </td>
                                                </tr>
                                                <tr>
                                                  <th>RUN</th><td>{{ s.run }}-{{ s.verifying_digit }}</td>
                                                </tr>
                                                <tr>
                                                  <th>NOMBRE</th><td>{{ s.name }}</td>
                                                </tr>
                                                <tr>
                                                  <th>APELLIDOS</th><td>{{ s.last_name }}</td>
                                                </tr>
                                                <tr>
                                                  <th>EMAIL</th><td>{{ s.email }}</td>
                                                </tr>
                                                <tr>
                                                  <th>TELEFONO</th><td>{{ s.phone }}</td>
                                                </tr>
                                                <tr>
                                                  <th>FECHA INGRESO</th><td>{{ dateFormatCreated(s.created_at) }}</td>
                                                </tr>
                                                <tr>
                                                  <th>ESTADO</th><td v-html="statusFormat(s.status)"></td>
                                                </tr>
                                              </thead>
                                            </table>
                                          </div>
                                        </div>
                                        <div class="card-footer">
                                          <small class="text-muted">Ultima actualizacion : {{ dateFormatUpdated(s.updated_at) }}</small>
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
    import $ from 'jquery'
    
    export default {
        data() {
          return {
             editmode:false,
             sellers:{},
             error:false,
             errors: [],
             run:'',
             name:'',
             last_name:'',
             email:'',
             password:'',
             status:1,
             sendmail: false,
             text:'',
          }
        },
        mounted() {
            this.getSellers();
            //console.log('Component mounted.')
        },
        computed: {
          maxsellers(){
            return this.$store.getters.getPlanSellersMax;
          }
        },
        methods: {
          searchResult: function() {
            var run = this.text;
            if(!run) {
              run = "vacio";
              this.getSellers();
              return;
            }
            this.$Progress.start()
            axios.post('/get_seller/' + run)
              .then(response => {
                //console.log(response);
                this.sellers = response.data;
                this.$Progress.finish()
            });
          },
          formReset(){
            this.id = '';
            this.run = '';
            this.name = '';
            this.last_name = '';
            this.email = '';
            this.password = '';
            this.status = 1;
            this.sendmail = false;
          },
          EditSellerSave() {
            this.errors = [];
            let formdata = new FormData();
            formdata.append('run', this.run);
            formdata.append('name', this.name);
            formdata.append('last_name', this.last_name);
            formdata.append('email', this.email);
            formdata.append('password', this.password);
            formdata.append('status', this.status);
            formdata.append('sendmail', this.sendmail);
            axios.post('/update_sellers/'+this.id, formdata).then(response=>{
              if(response.data.errors){
                try {
                  this.errors.push(response.data.errors.email[0]);
                } catch (e) {}
                try {
                  this.errors.push(response.data.errors.run[0]);
                } catch (e) {}
                try {
                  this.errors.push(response.data.errors.name[0]);
                } catch (e) {}
                this.error = true;
              }
              else {
                this.error = false;
                if(!this.password){
                  this.getSellers();
                  $("#newSeller").modal("hide");
                  this.$swal.fire({
                    title: 'Editado',
                    icon: 'success',
                    html:
                      'El vendedor <b>'+this.name+' '+this.last_name+'</b> ha sido editado exitosamente.</b> <hr>',
                    showConfirmButton: false,
                    timer: 1500
                  });
                  this.formReset();
                }
                else {
                  this.getSellers();
                  $("#newSeller").modal("hide");
                  this.$swal.fire({
                    title: 'Creado',
                    icon: 'success',
                    html:
                      'El vendedor ha sido editado exitosamente. <br> Email : <b>'+this.email+'</b> <br> Contraseña : <b>'+this.password+'</b> <hr>',
                      showCloseButton: true,
                      focusConfirm: false,
                      confirmButtonText:
                        'OK',
                  });
                  this.formReset();
                }
              }
            });
          },
          editSeller(s) {
            this.errors = [];
            this.error = false;
            this.editmode = true;
            this.id = s.id;
            this.run = this.RutF(s.run+s.verifying_digit);
            this.name = s.name;
            this.last_name = s.last_name;
            this.email = s.email;
            this.status = s.status;
            this.sendmail = s.sendmail;
            jQuery.noConflict();
            $("#newSeller").modal("show");
          },
          deleteSeller(s){
            axios.post('/get_sales/'+s.id).then(response =>{
              this.$swal.fire({
                title: 'Esta seguro(a)?',
                html: "Las ventas de <b>"+s.name.toUpperCase()+" "+s.last_name.toUpperCase()+"</b> también seran eliminadas, si desea conservar las ventas para los reportes se recomienda <b>DESACTIVAR</b> la cuenta en lugar de eliminar. <hr> Se Eliminaran : <b>"+response.data+"</b> ventas.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!'
                }).then((result) => {
                  if (result.value) {
                    axios.delete('/delete_seller/'+s.id).then(response=>{
                      this.getSellers();
                      this.$swal.fire(
                        'Eliminado!',
                        'El vendedor ha sido eliminado.',
                        'success'
                      )
                     });
                   }
                });
            });
          },
          newSellerSave(){
            this.errors = [];
            let formdata = new FormData();
            formdata.append('run', this.run);
            formdata.append('name', this.name);
            formdata.append('last_name', this.last_name);
            formdata.append('email', this.email);
            formdata.append('password', this.password);
            formdata.append('status', this.status);
            formdata.append('sendmail', this.sendmail);
            axios.post('/set_sellers', formdata).then(response=>{
              if(response.data.errors){
                try {
                  this.errors.push(response.data.errors.email[0]);
                } catch (e) {}
                try {
                  this.errors.push(response.data.errors.run[0]);
                } catch (e) {}
                try {
                  this.errors.push(response.data.errors.name[0]);
                } catch (e) {}
                this.error = true;
              }
              else {
                if(response.data.success == false){
                  this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: 'Ha llegado al maximo de vendedores<br> correspondiente a su plan!',
                  })
                  $("#newSeller").modal("hide");
                }
                else {
                  this.error = false;
                  if(!this.password){
                    this.getSellers();
                    $("#newSeller").modal("hide");
                    this.$swal.fire({
                      title: 'Creado',
                      icon: 'success',
                      html:
                        'El vendedor ha sido creado exitosamente. <br> Email : <b>'+this.email+'</b> <br> Contraseña : <b>'+response.data.password+'</b> ',
                      showCloseButton: true,
                      focusConfirm: false,
                      confirmButtonText:
                        'OK',
                    });
                    this.formReset();
                  }
                  else {
                    this.getSellers();
                    $("#newSeller").modal("hide");
                    this.$swal.fire({
                      title: 'Creado',
                      icon: 'success',
                      html:
                        'El vendedor ha sido creado exitosamente. <br> Email : <b>'+this.email+'</b> <br> Contraseña : <b>'+this.password+'</b> ',
                      showCloseButton: true,
                      focusConfirm: false,
                      confirmButtonText:
                        'OK',
                    });
                    this.formReset();
                  }
                }
              }
            });
          },
          newSellerModal(){
            jQuery.noConflict();
            this.formReset();
            this.editmode = false;
            $("#newSeller").modal("show");
          },
          dateFormatUpdated(d) {
            moment.locale('es');
            return moment(d).fromNow();
          },
          dateFormatCreated(d) {
            moment.locale('es');
            return moment(d).format('LLLL');
          },
          statusFormat(status) {
            if(status == 1){
              return '<span class="badge badge-success">Activado</span>';
            }
            else {
              return '<span class="badge badge-danger">Desactivado</span>';
            }
          },
          getSellers() {
            axios.post('/get_sellers').then(response =>{
              //console.log(response);
              this.sellers = response.data;
            });
          },
          formateaRut() {
            if(this.run.length >= 2) {
              let rutSinPuntos;
              this.rutSinPuntos = this.RutF(this.run);
              this.run = this.RutF(this.rutSinPuntos);
            }
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
          }
        }
    }
</script>
<style media="screen">
:root {
  /* larger checkbox */
}
:root label.checkbox-bootstrap input[type=checkbox] {
  /* hide original check box */
  opacity: 0;
  position: absolute;
  /* find the nearest span with checkbox-placeholder class and draw custom checkbox */
  /* draw checkmark before the span placeholder when original hidden input is checked */
  /* disabled checkbox style */
  /* disabled and checked checkbox style */
  /* when the checkbox is focused with tab key show dots arround */
}
:root label.checkbox-bootstrap input[type=checkbox] + span.checkbox-placeholder {
  width: 14px;
  height: 14px;
  border: 1px solid;
  border-radius: 3px;
  /*checkbox border color*/
  border-color: #737373;
  display: inline-block;
  cursor: pointer;
  margin: 0 7px 0 -20px;
  vertical-align: middle;
  text-align: center;
}
:root label.checkbox-bootstrap input[type=checkbox]:checked + span.checkbox-placeholder {
  background: #0ccce4;
}
:root label.checkbox-bootstrap input[type=checkbox]:checked + span.checkbox-placeholder:before {
  display: inline-block;
  position: relative;
  vertical-align: text-top;
  width: 5px;
  height: 9px;
  /*checkmark arrow color*/
  border: solid white;
  border-width: 0 2px 2px 0;
  /*can be done with post css autoprefixer*/
  -webkit-transform: rotate(45deg);
  -moz-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  -o-transform: rotate(45deg);
  transform: rotate(45deg);
  content: "";
}
:root label.checkbox-bootstrap input[type=checkbox]:disabled + span.checkbox-placeholder {
  background: #ececec;
  border-color: #c3c2c2;
}
:root label.checkbox-bootstrap input[type=checkbox]:checked:disabled + span.checkbox-placeholder {
  background: #d6d6d6;
  border-color: #bdbdbd;
}
:root label.checkbox-bootstrap input[type=checkbox]:focus:not(:hover) + span.checkbox-placeholder {
  outline: 1px dotted black;
}
:root label.checkbox-bootstrap.checkbox-lg input[type=checkbox] + span.checkbox-placeholder {
  width: 26px;
  height: 26px;
  border: 2px solid;
  border-radius: 5px;
  margin-left:1px;
  /*checkbox border color*/
  border-color: #737373;
}
:root label.checkbox-bootstrap.checkbox-lg input[type=checkbox]:checked + span.checkbox-placeholder:before {
  width: 9px;
  height: 15px;
  /*checkmark arrow color*/
  border: solid white;
  border-width: 0 3px 3px 0;
}



</style>
