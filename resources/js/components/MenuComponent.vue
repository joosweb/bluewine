<template>
  <!-- BEGIN DEFAULT SIDEBAR -->
  <div class="ks-column ks-sidebar ks-info">
  <div class="ks-wrapper ks-sidebar-wrapper">
      <ul class="nav nav-pills nav-stacked">
        <li class="nav-item ks-user dropdown">
            <a class="nav-link dropdown-toggle"  href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <img :src="'/'+userphoto" width="36" height="36" class="ks-avatar rounded-circle">
                <div class="ks-info">
                    <div class="ks-name">{{ username }}</div>
                    <div class="ks-text">{{ usertypename }}</div>
                </div>
            </a>
            <div class="dropdown-menu">
                <router-link class="dropdown-item" to="/config">
                <span class="la la-user ks-icon"></span>
                <span>Configuración</span></router-link>
                <a class="dropdown-item" href="/logout">
                    <span class="la la-sign-out ks-icon" aria-hidden="true"></span>
                    <span>Salir</span>
                </a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <router-link class="nav-link" to="/"><span class="ks-icon la la-home"></span>
            <span>INICIO</span></router-link>
          </li>
          <li class="nav-item dropdown" v-if="isAdmin">
              <a  class="nav-link dropdown-toggle"  href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span class="la la-user ks-icon"></span>
                  <span>USUARIOS</span>
              </a>
              <div class="dropdown-menu">
                  <router-link class="dropdown-item" to="/clients"><span class="ks-icon la la-user-plus"></span>
                  <span>CLIENTES</span></router-link>
                  <router-link class="dropdown-item" to="/providers"><span class="ks-icon la la-users"></span>
                  <span>PROVEEDORES</span></router-link>
                  <router-link class="dropdown-item" to="/sellers"><span class="ks-icon la la-th-list"></span>
                  <span>VENDEDORES</span></router-link>
              </div>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle"  href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span class="ks-icon la la-cart-arrow-down"></span>
                  <span>PUNTO DE VENTA</span>
              </a>
              <div class="dropdown-menu">
                  <router-link class="dropdown-item" to="/pos"><span class="ks-icon la la-cart-arrow-down"></span>
                  <span>POS</span></router-link>
                  <router-link v-if="isAdmin" class="dropdown-item" to="/categories"><span class="ks-icon la la-th-list"></span>
                  <span>CATEGORIAS</span></router-link>
                  <router-link v-if="isAdmin" class="dropdown-item" to="/items"><span class="ks-icon la la-reorder"></span>
                  <span>ITEMS</span></router-link>
              </div>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle"  href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span class="ks-icon la la-bar-chart-o"></span>
                  <span>REPORTES</span>
              </a>
              <div class="dropdown-menu">
                  <router-link v-if="isAdmin" class="dropdown-item" to="/purchases"><span class="ks-icon la la-dollar"></span>
                  <span>COMPRAS</span></router-link>
                  <router-link class="dropdown-item" to="/sales"><span class="ks-icon la la-dollar"></span>
                  <span>VENTAS</span></router-link>
                  <router-link class="dropdown-item" to="/expenses"><span class="ks-icon la la-th-list"></span>
                  <span>GASTOS</span></router-link>
                  <router-link class="dropdown-item" to="/products-report"><span class="ks-icon la la-cube"></span>
                  <span>PRODUCTOS</span></router-link>
                  <router-link class="dropdown-item" to="/boxs"><span class="ks-icon la la-cash-register"></span>
                  <span>CAJA</span></router-link>
              </div>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/config">
                <span class="ks-icon la la-cog" aria-hidden="true"></span>
                <span>CONFIGURACIÓN</span>
            </router-link>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/logout">
                <span class="la la-sign-out ks-icon" aria-hidden="true"></span>
                <span>SALIR</span>
            </a>
          </li>
      </ul>

  </div>
  </div>
  <!-- END DEFAULT SIDEBAR -->
</template>

<script>
export default {
    data() {
      return {
        isAdmin:false,
      }
    },
    computed: {
      usertypename(){ //final output from here
        let type = this.$store.getters.getUserType;
            if(type == 1) {
              this.isAdmin = true;
              return 'ADMINISTRADOR';
            }
            else {
              this.isAdmin = false;
              return 'VENDEDOR';
         }
      },
      username(){
            return this.$store.getters.getUserName;
      },
      userphoto(){
            return this.$store.getters.getUserPhoto;
      }
    }
}
</script>

<style lang="css" scoped>
</style>
