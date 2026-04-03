<template>
  <div class="container-fluid">
    <table class="table table-sm ml-2 mt-2" >
      <tr>
        <td>PLAN</td>
        <td><span class="badge badge-primary" style="font-size:12px;">{{ this.name }}</span></td>
        <td>CLIENTES</td>
        <td><span class="badge badge-success" style="font-size:12px;">{{ clientstotal }} / {{ this.clients_max }}</span></td>
        <td>PROVEDORES</td>
        <td><span class="badge badge-info" style="font-size:12px;">0/500</span></td>
        <td>CATEGORIAS</td>
        <td><span class="badge badge-danger" style="font-size:12px;">10/10</span></td>
        <td>ITEMS</td>
        <td><span class="badge badge-danger" style="font-size:12px;">50/2500</span></td>
      </tr>
    </table>
  </div>
</template>

<script>
    export default {

      props: ['clientstotal'],

      data() {
          return {
            name: '',
            clients_max: 0,
          }
        },

        mounted() {
            axios.get('/get/plan')
            .then(response => {
              this.name = response.data.name;
              this.clients_max = response.data.clients_max;
              this.clientstotal = response.data.clients;
            });
        }
    }
</script>
