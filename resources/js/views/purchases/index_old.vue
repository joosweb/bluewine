<template>
  <div class="container-fluid mt-4">
    <!-- Modal -->
    <div class="modal fade" id="detailsPurchases" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">DETALLES</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">PROVEEDOR</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">ITEMS</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">DOCUMENTOS</a>
            </li>
          </ul>
          <div class="tab-content" id="myTabContent">
            <div v-for="pv in this.pd.purchase" class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="table-responsive">
              <table class="table table-bordered table-sm table-hover mt-2">
                  <tbody>
                    <tr>
                      <th>RUN</th>
                      <td>{{ pv.run }}-{{ pv.verifying_digit }}</td>
                    </tr>
                    <tr>
                      <th>NOMBRE</th>
                      <td>{{ pv.name }} {{ pv.last_name }}</td>
                    </tr>
                    <tr>
                      <th>EMAIL</th>
                      <td>{{ pv.email }}</td>
                    </tr>
                    <tr>
                      <th>TELEFONO</th>
                      <td>{{ pv.phone }}</td>
                    </tr>
                  </tbody>
              </table>
            </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
              <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover mt-2">
                  <thead>
                    <th>NOMBRE</th>
                    <th>CODIGO</th>
                    <th>CANTIDAD</th>
                    <th>STOCK</th>
                  </thead>
                  <tbody>
                    <tr v-for="it in this.pd.purchase_item">
                      <td>{{ it.name }}</td>
                      <td>{{ it.code }}</td>
                      <td>{{ it.quantity }}</td>
                      <td>{{ it.stock }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
              <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover mt-2">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>URL</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="doc,index in this.pd.docs">
                      <td>{{ ++index }}</td>
                      <td>
                        <a class="btn btn-default" :href="doc.link" target="_blank">DOCUMENTO</a>
                     </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="CreateUpdatePurchase" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              <button title="QUITAR COLUMNA" @click="inputSet(1)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'minus']" /></button>
              <button title="AGREGAR COLUMNA" @click="inputSet(2)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'plus']" /></button>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="uploadFile()">
          <div class="modal-body">
            <div v-if="errors.length" class="alert alert-danger" role="alert">
            <ul>
               <li v-for="error in errors">{{ error }}</li>
             </ul>
            </div>
            <div class="modal-body">
              <div class="input-group mb-3">
                <div class="table-responsive" style="margin-top:-17px;">
                  <p class="blockquote-footer" style="font-size:12px;">Puede escanear sus productos o ingresarlos manualmente.</p>
                  <table class="table table-sm table-hover">
                    <tr v-for="c,index in i" :key="index" :id="index">
                      <td width="20%" class="img" :id="'i_'+index">
                      </td>
                      <td width="45%">
                        <input v-model="code[index]" v-on:change="checkCode(index, $event.target.value)" class="form-control" v-on:keydown.enter.prevent type="text"  placeholder="ESCANEAR">
                      </td>
                      <td width="30%">
                        <input type="text" v-model="quantity[index]" class="form-control" v-on:keydown.enter.prevent size="10"  placeholder="UD'S">
                      </td>
                      <td width="5%">
                        <button @click="RemoveRow(index)" class="btn btn-default btn-sm mt-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4"></td>
                    </tr>
                  </table>
                </div>
              </div>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user">Proveedor</i></span>
                </div>
                <select class="form-control" v-model="provider" required>
                  <option value="false">SELECCIONE PROVEEDOR</option>
                  <option v-for="p in this.providers" :value="p.id">{{ p.name.toUpperCase() }} {{ p.last_name.toUpperCase() }}</option>
                </select>
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-user"> Neto </i></span>
                  </div>
                  <input v-model="neto" v-on:keydown.enter.prevent type="number" class="form-control" placeholder="$">
              </div>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="width:120px;" id="basic-addon1"><i class="fas fa-envelope-square">Comentario</i> </span>
                </div>
                <textarea v-model="description" v-on:keydown.enter.prevent type="email" class="form-control"></textarea>
              </div>
              <div class="input-group mb-3">
                <div class="card">
                  <div class="card-body">
                    <p class="blockquote-footer" style="font-size:12px;">MAX 500KB POR ARCHIVO</p>
                    <input type="file" class="form-control-file" id="uploadFile" ref="file" multiple="multiple">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default" name="button">GUARDAR</button>
            <button type="button" class="btn btn-default"  data-dismiss="modal">CERRAR</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div class="ks-page-header">
        <section class="ks-title-and-subtitle mt-2">
            <div class="ks-title-block">
                <h3 class="ks-main-title">App / Compras</h3>
                <div class="ks-sub-title">Sección para reportes de compras.</div>
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
                                <div class="card-block" style="padding:15px;"><hr>
                                    <form class="form-inline" @submit.prevent="">
                                    <div style="input-group">
                                       <button class="form-control mb-2 mr-sm-2">TOTAL {{ this.purchases.total }}</button>
                                    </div>
                                    <select class="form-control mb-2 mr-sm-2" v-model="filter">
                                      <option value="run" selected>RUN</option>
                                      <!--<option value="T2.name">NOMBRE</option>-->
                                    </select>
                                    <div class="input-group">
                                      <input @keyup="" v-model="inputValue" type="text" class="form-control mb-2 mr-sm-2" autofocus>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button @click="newPurchase()" type="button" class="btn btn-default" name="button">NUEVA COMPRA</button>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button @click="" type="button" class="btn btn-default" name="button">ACTUALIZAR</button>
                                    </div>
                                  </form>
                                  <hr>
                                  <table class="table table-sm table-hover">
                                    <thead class="thead-light">
                                      <tr>
                                        <th>RUN PROVEEDOR</th>
                                        <th>NETO</th>
                                        <th>IVA</th>
                                        <th>TOTAL</th>
                                        <th>FECHA</th>
                                        <th>ACCION</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="p, index in this.purchases.data">
                                        <td>{{ p.run }}</td>
                                        <td>$ {{ number_format(p.neto) }}</td>
                                        <td>$ {{ number_format(p.neto * 0.19) }}</td>
                                        <td>$ {{ number_format(parseInt(p.neto) + parseInt(p.neto * 0.19)) }}</td>
                                        <td>{{ p.created_at }}</td>
                                        <td>
                                          <button type="button" @click="ShowDetails(p.pid)" name="button"><font-awesome-icon :icon="['fas', 'eye']" /></button>
                                          <button type="button" @click="EditPurchase(p.pid)" name="button"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                          <button type="button" @click="DeletePurchase(p.pid)" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <hr>
                                  <pagination :limit="6" :data="purchases" @pagination-change-page="getPurchases">
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
  import VueSweetalert2 from 'vue-sweetalert2';
  Vue.use(VueSweetalert2);

  export default {
      data() {
        return {
          editmode: false,
          codeError: false,
          purchases: {},
          pd: {},
          errors: [],
          providers: {},
          provider: false,
          neto: 0,
          description: '',
          code: [],
          quantity: [],
          filter: 'run',
          inputValue: '',
          i: 1,
        }
      },
      created() {
        this.getPurchases();
      },
      methods:{
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
          getPurchases(page = 1){
            axios.post('/purchases/get?page='+page)
            .then(response =>{
              this.purchases = response.data;
            });
          },
          inputSet(operator) {
            if(operator == 1){
              if(this.i == 1)
              {
                return;
              }
              else {
                --this.i;
              }
            }
            else {
              ++this.i;
            }
          },
          reset() {
            this.provider = false;
            this.neto = 0;
            this.description = '';
            this.i = 1;
            this.code = [];
            this.quantity = [];
            $(".img").html('');
          },
          checkCode(index, code) {
            axios.get('/check/code/'+code)
            .then(response => {
              if(response.data.item == 0){
                $('#i_'+index).html('<img title="EL CODIGO INGRESADO NO EXISTE EN ITEMS, POR FAVOR INGRESE EL ITEM ANTES DE INGRESAR UNA COMPRA" width="35" src="https://nonperfect.files.wordpress.com/2009/11/atencion.png" alt="">');
                this.codeError = true;
              }
              else {
                $('#i_'+index).html('<img title="EL CODIGO INGRESADO ES CORRECTO" width="35" src="https://findicons.com/files/icons/2117/nuove/128/camera_test.png" alt="">');
                this.codeError = false;
              }
            });
          },
          newPurchase() {
            jQuery.noConflict();
            this.GetProviders();
            this.editmode = false;
            $("#CreateUpdatePurchase").modal('show');
          },
          RemoveRow(index){
            $("#"+index).remove();
          },
          GetProviders() {
            axios.post('/purchases/get/providers')
            .then(response => {
              this.providers = response.data;
            });
          },
          uploadFile(){

            this.errors = [];

            if(this.code.length != this.quantity.length){
              this.errors.push('El campo codigo y/o unidades no puede estar vacio.');
            }

            if (!this.provider) {
              this.errors.push('Seleccione un proveedor');
            }

            if (!this.neto) {
              this.errors.push('El campo neto no debe estar vacio')
            }

            if(this.codeError == true){
              this.errors.push('Por favor ingrese un codigo valido.')
            }

            if(this.errors.length >= 1){
              return;
            }

            let formData = new FormData();

            formData.append('neto', this.neto);
            formData.append('description', this.description);
            formData.append('provider', this.provider);

            for( var i = 0; i < this.code.length; i++ ){
                let code = this.code[i];
                formData.append('code[' + i + ']', code);
            }

            for( var i = 0; i < this.quantity.length; i++ ){
                let quantity = this.quantity[i];
                formData.append('quantity[' + i + ']', quantity);
            }

            for( var i = 0; i < this.$refs.file.files.length; i++ ){
                let file = this.$refs.file.files[i];
                if(file.size < 524000){
                  formData.append('files[' + i + ']', file);
                }
                else {
                  this.$swal.fire({
                    title: 'Error',
                    icon: 'error',
                    html:
                      'El archivo <strong>'+file.name+'</strong> supero el limite de 500kb.',
                    showCloseButton: false,
                    focusConfirm: true,
                    confirmButtonText:
                      'OK',
                  })
                 return;
               }
            }

            axios.post( '/purchases', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
              }
            ).then(response => {
              console.log(response);
              this.$swal.fire({
                title: 'Compra Agredada',
                icon: 'success',
                html:
                  'La compra se ha ingresado exitosamente.',
                showCloseButton: false,
                focusConfirm: true,
                confirmButtonText:
                  'OK',
              })
              document.getElementById("uploadFile").value = [];
              jQuery.noConflict();
              this.reset();
              this.getPurchases();
              $("#CreateUpdatePurchase").modal('hide');
            })
            .catch(function(error){
              //console.log(error);
            });
         },
         EditPurchase(purchaseID){
           this.editmode = true;
         },
         DeletePurchase(purchaseID) {
           this.$swal.fire({
              title: 'Esta seguro?',
              text: "No podrás revertir esto!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#28a745',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Si, eliminar!'
              }).then((result) => {
                if (result.value) {
                  axios.delete('/purchases/delete/'+purchaseID)
                  .then(response => {
                    this.$swal.fire(
                      'Compra eliminada!',
                      'La compra ha sido eliminada exitosamente.',
                      'success'
                    )
                    this.getPurchases();
                  });
                }
            });
         },
         ShowDetails(purchaseID){
           jQuery.noConflict();
           axios.post('/purchases/details/'+purchaseID)
           .then(response => {
             //console.log(response);
             this.pd = response.data;
           });
           $("#detailsPurchases").modal('show');
         }
      },
      mounted() {
          console.log('Component mounted.')
      }
  }
</script>
