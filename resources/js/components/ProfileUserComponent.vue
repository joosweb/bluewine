<template>
  <div class="containter">
    <table class="table table-sm table-hover">
      <thead class="thead-light">
        <tr>
          <th>IMAGEN DE PERFIL</th>
          <td>
            <img width="100" :src="'/img/user_default.jpeg'" class="img-thumbnail" alt="">
          </td>
        </tr>
        <tr>
          <th>RUN</th><td>16.405.757-7</td>
        </tr>
        <tr>
          <th>NOMBRE</th><td>Jose</td>
        </tr>
        <tr>
          <th>APELLIDOS</th><td>Osses</td>
        </tr>
        <tr>
          <th>EMAIL</th><td>jooswebs@gmail.com</td>
        </tr>
        <tr>
          <th>TELEFONO</th><td>+56984858473</td>
        </tr>
        <tr>
          <th>FECHA INGRESO</th><td>2020-05-01</td>
        </tr>
        <tr>
          <th>ESTADO</th><td>OK</td>
        </tr>
      </thead>
    </table>
  </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
