<template>
  <div class="">
    <form class="" @submit.prevent="editmode ? UpdateForm() : SaveForm()" method="post">
      <div class="form-group">
        <label for=""><h5>MONTO</h5> </label>
        <input required type="number" ref="amount" v-model="amount" class="form-control" name="" placeholder="0">
      </div>
      <div class="form-group">
        <label for=""><h5>COMENTARIO</h5> </label>
        <textarea required name="name" v-model="commentary" class="form-control" rows="4" cols="80"></textarea>
      </div>
      <div class="form-group" v-if="isAdmin">
        <label for=""><h5>FECHA</h5> </label>
        <input type="datetime-local" name="created_at" id="expense_input_date" class="form-control">
      </div>
      <div class="form-group">
        <button class="btn btn-default" type="submit" name="button">GUARDAR</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
      </div>
    </form>
  </div>
</template>

<script>
    import Vue from 'vue'
    import moment from 'moment'
    import VueSweetalert2 from 'vue-sweetalert2';
    import $ from 'jquery'
    
    Vue.use(VueSweetalert2);

    export default {
        data() {
          return {
            isAdmin: false,
            editmode: false,
            id:null,
            amount: null,
            commentary: '',
            created_at: '',
          }
        },
        mounted() {
            jQuery.noConflict();
            this.checkAdmin();
            //this.dateNow();
            //console.log('Component mounted.');
        },
        methods: {
          checkAdmin() {
    			  axios.get('/check-auth').then(response=>{
    						this.isAdmin = response.data.isadmin;
    				});
          },
          setProps(id, amount, commentary, created_at) {
            this.editmode   = true;
            this.id         = id;
            this.amount     = amount;
            this.commentary = commentary;
            //this.created_at = moment().format('YYYY-MM-DDTHH:mm:ss')
          },
          ResetForm() {
            this.id     = null;
            this.amount = null;
            this.commentary = '';
            //this.created_at = moment().format().slice(0,16);
          },
          UpdateForm(){
            let formData = new FormData();
            formData.append('amount', this.amount);
            formData.append('commentary', this.commentary);
            formData.append('created_at', this.created_at);
            axios.post('/udpate-expense/' + this.id, formData)
              .then(response=>{
                $("#NewExpenseModal").modal("hide");
                this.$emit('updatingScore');
                this.$swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Actualizado',
                  html: 'El gasto ha sido actualizado exitosamente.',
                  showConfirmButton: true,
                  timer: 1500
                })
              });
          },
          SaveForm() {
            let formData = new FormData();
            formData.append('amount', this.amount);
            formData.append('commentary', this.commentary);
            formData.append('created_at', this.created_at);
            axios.post('/new-expense', formData).then(response => {
              if(response.data.status == true) {
                $("#NewExpenseModal").modal("hide");
                this.$emit('updatingScore')
                this.ResetForm();
                this.$swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Ingresado',
                  html: 'El gasto ha sido ingresado exitosamente.',
                  showConfirmButton: true,
                  timer: 1500
                })
              }
              else {
                //console.log(response);
              }
            });
          }
        }
    }
</script>
