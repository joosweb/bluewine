/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


window.Vue = require('vue');

Vue.component('app', require('./components/AppComponent.vue').default);
Vue.component('menu-point-of-sale', require('./components/MenuComponent.vue').default);
Vue.component('nav-menu', require('./components/NavbarComponent.vue').default);
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('show-time', require('./components/TimeComponent.vue').default);
Vue.component('plans-user', require('./components/PlansComponent.vue').default);
Vue.component('notifications', require('./components/NotificationComponent.vue').default);
Vue.component('cssconfig', require('./components/CssConfigComponent.vue').default);

// MALL - SHOP -

Vue.component('mall-categories', require('./components/MallCategories.vue').default);
Vue.component('mall-categories-responsive', require('./components/MallCategoriesResponsive.vue').default);
Vue.component('mall-best-seller', require('./components/MallBestSeller.vue').default);
Vue.component('mall-header', require('./components/MallHeader.vue').default);
Vue.component('mall-content', require('./components/MallContent.vue').default);


import router from './routes'

import DigitalClock from "vue-digital-clock";

import VueProgressBar from 'vue-progressbar';

import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas);

Vue.component('font-awesome-icon', FontAwesomeIcon);

const options = {
  color: '#33FF46',
  failedColor: '#874b4b',
  thickness: '5px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
  autoRevert: true,
  location: 'top',
  inverse: false
}

Vue.use(require('vue-shortkey'))
Vue.use(VueProgressBar, options)

//support vuex
import Vuex from 'vuex'
Vue.use(Vuex)
import storeData from "./store/index"

const store = new Vuex.Store(
   storeData
)

import VTooltip from 'v-tooltip'

Vue.use(VTooltip)

let newVue = new Vue({});
// Intercept all requests
  window.axios.interceptors.request.use(
    (config) => {
     // do something before sending requests
     newVue.$Progress.start()
      return config
    },
    (error) => {
      return Promise.reject(error);
    }
  );

// Intercept all responses
window.axios.interceptors.response.use(
  (response) => {
   // when success from server
    newVue.$Progress.finish()
    return response;
  },

  (error) => {
   // when error from server
    newVue.$Progress.fail()
    return Promise.reject(error);
  }
);

const app = new Vue({
    el: '#app',
    router,
    store, //vuex
});
