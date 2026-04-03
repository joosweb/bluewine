<template>
  <nav class="navbar ks-navbar">
      <!-- BEGIN HEADER INNER -->
  <!-- BEGIN LOGO -->
  <div href="#" class="navbar-brand">
  <!-- BEGIN RESPONSIVE SIDEBAR TOGGLER -->
  <a href="#" class="ks-sidebar-toggle"><i class="ks-icon la la-bars" aria-hidden="true"></i></a>
  <a href="#" class="ks-sidebar-mobile-toggle"><i class="ks-icon la la-bars" aria-hidden="true"></i></a>
  <!-- END RESPONSIVE SIDEBAR TOGGLER -->

  <div class="ks-navbar-logo">
      <a href="#!" class="ks-logo">{{ title }}</a>
  </div>
  </div>
  <!-- END LOGO -->
  <!-- BEGIN MENUS -->
  <div class="ks-wrapper">
      <nav class="nav navbar-nav">
          <!-- BEGIN NAVBAR MENU -->
          <div class="ks-navbar-menu">
              <span class="badge badge-info">{{ usertype }} </span>
              <span class="badge badge-success ml-2">{{ UserName }} {{ UserLastName }}</span>
          </div>
          <!-- END NAVBAR MENU -->

          <!-- BEGIN NAVBAR ACTIONS -->
          <div class="ks-navbar-actions">
            <!-- BEGIN NAVBAR LANGUAGES -->
              <span class="badge badge-success" style="width:200px;">
                <h2 style="color:white;"><show-time></show-time></h2>
              </span>
            <!-- END NAVBAR LANGUAGES -->
             <!--BEGIN NAVBAR ACTION BUTTON-->
              <div v-if="!userplan" class="nav-item nav-link btn-action-block">
                  <a class="btn btn-danger" href="#">
                      <span class="ks-action">ACTUALIZA TU CUENTA</span>
                      <span class="ks-description">Estas en la version FREE</span>
                  </a>
              </div>
             <!--END NAVBAR ACTION BUTTON-->

              <notifications v-if="this.$store.getters.getconfigNotification"></notifications>
              <!-- BEGIN NAVBAR USER -->
              <div class="nav-item dropdown ks-user">
                  <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                      <span class="ks-avatar">
                          <img :src="'/'+UserPhoto" width="36" height="36">
                      </span>
                      <span class="ks-info">
                          <span class="ks-name">{{ UserName.substr(0, 19) }}</span>
                          <span class="ks-description">{{ usertype }}</span>
                      </span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="Preview">
                    <router-link class="dropdown-item" to="/config">
                    <span class="la la-user ks-icon"></span>
                      <span>Configuración</span></router-link>

                      <a class="dropdown-item" href="#">
                          <span class="la la-question-circle ks-icon" aria-hidden="true"></span>
                          <span>Ayuda</span>
                      </a>
                      <a class="dropdown-item" href="/logout">
                          <span class="la la-sign-out ks-icon" aria-hidden="true"></span>
                          <span>Salir</span>
                      </a>
                  </div>
              </div>
              <!-- END NAVBAR USER -->
          </div>
          <!-- END NAVBAR ACTIONS -->
      </nav>

      <!-- BEGIN NAVBAR ACTIONS TOGGLER -->
      <nav class="nav navbar-nav ks-navbar-actions-toggle">
          <a class="nav-item nav-link" href="#">
              <span class="la la-ellipsis-h ks-icon ks-open"></span>
              <span class="la la-close ks-icon ks-close"></span>
          </a>
      </nav>
      <!-- END NAVBAR ACTIONS TOGGLER -->

      <!-- BEGIN NAVBAR MENU TOGGLER -->
      <nav class="nav navbar-nav ks-navbar-menu-toggle">
          <a class="nav-item nav-link" href="#">
              <span class="la la-th ks-icon ks-open"></span>
              <span class="la la-close ks-icon ks-close"></span>
          </a>
      </nav>
      <!-- END NAVBAR MENU TOGGLER -->
  </div>
  <!-- END MENUS -->
  <!-- END HEADER INNER -->
  </nav>
</template>

<script>
    export default {
        computed: {
        title(){ //final output from here
            return this.$store.getters.getTitleFromGetters;
        },
        userplan(){
          if(this.$store.getters.getPlanName == 'Free'){
            return 0;
          }
          else{
            return 1;
          }
  			},
        usertype(){ //final output from here
          let type = this.$store.getters.getUserType;
              if(type == 1) {
                return 'ADMINISTRADOR';
              }
              else {
                return 'VENDEDOR';
            }
        },
        UserName(){
              return this.$store.getters.getUserName;
        },
        UserLastName() {
          return this.$store.getters.getUserLastName;
        },
        UserPhoto(){
              return this.$store.getters.getUserPhoto;
          }
        },
        mounted () {
          this.$store.dispatch("GetNameFromDB");
          this.$store.dispatch("GetTypeUserFromDB");
          this.$store.dispatch("GetPlanFromDB");
        }
    }
</script>
