<template>
  <!-- BEGIN NAVBAR MESSAGES -->
  <div class="nav-item dropdown ks-messages">
      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <span class="la la-envelope ks-icon" aria-hidden="true">
              <span id="badge" class="badge badge-pill badge-info">{{ quantity }}</span>
          </span>
          <span class="ks-text">Mensajes</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="Preview">
          <section class="ks-tabs-actions">
              <a href="#"><i class="la la-plus ks-icon"></i></a>
              <a href="#"><i class="la la-search ks-icon"></i></a>
          </section>
          <ul class="nav nav-tabs ks-nav-tabs ks-info" role="tablist">
              <li class="nav-item">
                  <a class="nav-link active" href="#" data-toggle="tab" data-target="#ks-navbar-messages-inbox" role="tab">Mensajes</a>
              </li>
          </ul>
          <div class="tab-content">
              <div class="tab-pane ks-messages-tab active" id="ks-navbar-messages-inbox" role="tabpanel">
                  <div class="ks-wrapper" style="overflow:scroll; height:245px;">
                      <a href="#" class="ks-message" v-for="n in this.notifications">
                          <div class="ks-avatar">
                              <img src="http://icons.iconarchive.com/icons/tatice/cristal-intense/256/Exclamation-icon.png" width="36" height="36">
                          </div>
                          <div class="ks-info">
                              <div class="ks-user-name">Alerta de Stock</div>
                              <div class="ks-text">
                                {{ n.message.substr(0, 40) }}
                              </div>
                              <div class="ks-datetime">1 minute ago</div>
                          </div>
                      </a>
                  </div>
                  <div class="ks-view-all">
                      <a href="#">Ver Todas</a>
                  </div>
              </div>

          </div>
      </div>
  </div>
  <!-- END NAVBAR MESSAGES -->
</template>

<script>
    import Vue from 'vue'
    import VueTimers from 'vue-timers'
    Vue.use(VueTimers)

    export default {
      data() {
        return {
          count: 0,
          notifications: {},
          photo: '',
        }
      },
      mounted() {
        this.countNotifications();
        this.getNotifications();
        this.photoProfile();
      },
      timers: {
        log: { time: 1000 /*10000 = 10 segundos*/, autostart: true, repeat: false }
      },
      computed:{
        quantity() {
          if(this.count >= 1){
          $("#badge").addClass("green");
            return this.count;
          }
          else {
          $("#badge").removeClass("green");
            return 0;
          }
          return this.count;
        }
      },
      methods: {
        photoProfile(){
          axios.post('/getPhotoProfile').then(response =>{
            //console.log(response);
            this.photo = response.data;
          });
        },
        countNotifications(){
          axios.post('/getCountNotifications').then(response => {
            this.count = response.data;
          });
        },
        getNotifications(){
          axios.post('/getNotifications').then(response => {
            //console.log(response);
            this.notifications = response.data;
          });

        },
        log () {
          this.countNotifications();
          this.getNotifications();
          this.photoProfile();
        }
      }
    }
</script>
<style media="screen">
  .green {
    background-color: #26d42c;
  }
  .blue {
    background-color: ##42a5f5;
  }
</style>
