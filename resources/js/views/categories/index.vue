<template>
  <div class="ks-column ks-page mt-4">
    <!-- Modal -->
    <div class="modal fade" id="CreateUpdateCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editmode ? 'EDITAR CATEGORIA' : 'CREAR CATEGORIA'}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editmode ? UpdateCategory() : CreateCategory()">
          <div class="modal-body">
            <div class="form-group">
              <label for="exampleInputEmail1">NOMBRE DE LA CATEGORIA</label>
              <input v-model="form.name" id="nameCategory" type="text" class="form-control">
              <has-error :form="form" field="name"></has-error>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
            <button type="submit" class="btn btn-default">GUARDAR</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- END MODAL-->
    <div class="ks-page-header">
        <section class="ks-title-and-subtitle">
            <div class="ks-title-block">
                <h3 class="ks-main-title">App / Categorias</h3>
                <div class="ks-sub-title">Sección para crear, editar y eliminar categorias.</div>
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
                                    <form class="form-inline" @submit.prevent="">
                                    <div style="input-group">
                                       <button class="form-control mb-2 mr-sm-2">{{ TotalCategories }}/{{ limitcategories }}</button>
                                    </div>
                                    <select class="form-control mb-2 mr-sm-2" v-model="filter">
                                      <option value="name">NOMBRE</option>
                                      <!--<option value="T2.name">NOMBRE</option>-->
                                    </select>
                                    <div class="input-group">
                                      <input @keyup="searchItem" v-model="inputValue"  type="text" class="form-control mb-2 mr-sm-2" autofocus>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button @click="ModalNewCategory" type="button" class="btn btn-default btn-block" name="button">NUEVA CATEGORIA</button>
                                    </div>
                                    <div class="input-group mb-2 mr-sm-2">
                                      <button @click="getCategories"  type="button" class="btn btn-default btn-block" name="button">ACTUALIZAR</button>
                                    </div>
                                  </form>
                                  <hr>
                                  <div class="table-responsive">
                                    <table class="table table-sm">
                                      <thead class="thead-light">
                                        <tr>
                                          <th>NOMBRE</th>
                                          <th>ITEMS EN CATEGORIA</th>
                                          <th>FECHA DE CREACION</th>
                                          <th>FECHA DE ACTUALIZACIÓN</th>
                                          <th>ACCIÓN</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr v-for="category, index in this.categories.data" :key="category.id" v-bind:id="'tr_'+category.id">
                                          <td><span style="width:100%;" class="btn btn-default btn-sm">{{ category.name }}</span></td>
                                          <td><span class="btn btn-default btn-sm" v-bind:id="'count_'+category.id">{{ category.count  }}</span></td>
                                          <td>{{ category.created_at }}</td>
                                          <td>{{ category.updated_at }}</td>
                                          <td>
                                            <div class="btn-group" role="group">
                                            <button title="EDITAR" @click="EditCategory(category)" class="btn btn-default btn-sm mr-sm-1" type="button" name="button"><font-awesome-icon :icon="['fas', 'edit']" /></button>
                                            <button title="ELIMINAR" @click="DeleteCategory(category, index)" class="btn btn-default btn-sm" type="button" name="button"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                                          </div>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <hr>
                                    <pagination :limit="6" :data="categories" @pagination-change-page="getCategories">
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
  </div>
</template>

<script>
    import Vue from 'vue'
    import VueSweetalert2 from 'vue-sweetalert2'
    import { Form, HasError, AlertError } from 'vform'
    import $ from 'jquery'

    window.Form = Form;

    Vue.component(HasError.name, HasError)
    Vue.component(AlertError.name, AlertError)

    Vue.use(VueSweetalert2);

    export default {
        data() {
          return {
            categories: {},
            filter: 'name',
            inputValue: '',
            editmode: false,
            nameCategory: '',
            form: new Form({
                    id: '',
                    name:'',
            }),
          }
        },
        created() {
          this.getCategories();
        },
        mounted() {
            //console.log('Component mounted.')
            $('#CreateUpdateCategory').on('shown.bs.modal', function (e) {
              $('#nameCategory').focus();
            });
        },
        computed: {
          limitcategories(){
            return this.$store.getters.getPlanCatsMax;
          },
          TotalCategories() {
            return this.categories.total;
          }
        },
        methods: {
          getCategories(page = 1){
            axios.post('/categories/get?page=' + page)
            .then(response => {
              //console.log(response);
              this.categories = response.data;
            });
          },
          ModalNewCategory(){
            jQuery.noConflict();
            this.form.reset();
            this.editmode = false;
            $("#CreateUpdateCategory").modal('show');
          },
          CreateCategory() {
            this.form.post('/categories')
            .then(response => {
              //console.log(response);
              if(response.data.success == false) {
                this.$swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  html: 'Ha llegado al maximo de categorias<br> correspondiente a su plan!',
                });
                $('#CreateUpdateCategory').modal('hide');
              }else {
                this.getCategories();
                let Category = this.form.name.toUpperCase();
                $('#CreateUpdateCategory').modal('hide');
                this.$swal.fire({
                  title: 'Categoria creada',
                  icon: 'success',
                  html:
                    'La categoria <strong>'+Category+'</strong> ha sido creada.',
                  showCloseButton: false,
                  focusConfirm: false,
                  confirmButtonText:
                    'OK',
                })
              }
            })
          },
          EditCategory(category){
            jQuery.noConflict();
            this.editmode = true;
            this.form.reset();
            this.form.fill(category);
            $("#CreateUpdateCategory").modal('show');
          },
          UpdateCategory() {
            this.form.put('/categories/'+this.form.id)
            .then(response => {
              this.getCategories();
              $("#CreateUpdateCategory").modal('hide');
              this.$swal.fire({
                title: 'Actualizado',
                icon: 'success',
                html: 'La categoria <strong>'+this.form.name+'</strong> ha sido actualizado.',
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText:
                  'OK',
              })
              $("#tr_"+this.form.id).addClass('table-success');
              this.form.reset();
            });
          },
          DeleteCategory(category, index) {
            let quantity = $("#count_"+category.id).html();
            this.$swal.fire({
              title: 'Esta seguro?',
              html: "Esta categoria contiene <b>"+quantity+"</b> item(s), si elimina esta categoria también se eliminaran estos items.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#28a745',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Si, Eliminar!'
              }).then((result) => {
              if (result.value) {
                this.form.delete('/categories/'+category.id)
                .then(response => {
                this.categories.data.splice(index, 1);
                --this.categories.total;
                this.$swal.fire({
                  title: 'Eliminada',
                  icon: 'success',
                  html:
                    'La categoria <strong>'+category.name+'</strong> ha sido eliminada.',
                  showCloseButton: true,
                  focusConfirm: false,
                  confirmButtonText:
                    'OK',
                })
                });
              }
            })
          },
          searchItem() {
            if(!this.inputValue){
              this.getCategories();
              return;
            }
            axios.post('/categories/'+ this.inputValue)
            .then(response => {
                this.categories = response.data;
            });
          }
        }
    }
</script>

<style scope>
.pagination {
  justify-content: center!important;
}
</style>
