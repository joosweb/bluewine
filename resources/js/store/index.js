export default {
  state: {
    titlePOS: 'POS-OSAN',
    notifications: 0,
    fk_id_user_type: 1,
    name: '',
    last_name: '',
    photo: '',
    email: '',
    name_p: 'Free',
    clients_max: 0,
    items_max: 0,
    prov_max: 0,
    cat_max: 0,
    sellers_max: 0,
    price: 0,
  },

  getters: {
    getTitleFromGetters(state) {
      //take parameter state
      return state.titlePOS
    },
    getconfigNotification(state) {
      //take parameter state
      return state.notifications
    },
    getUserType(state) {
      return state.fk_id_user_type
    },
    getUserName(state) {
      return state.name
    },
    getUserLastName(state) {
      return state.last_name
    },
    getPlanClientsMax(state) {
      return state.clients_max
    },
    getPlanCatsMax(state) {
      return state.cat_max
    },
    getPlanSellersMax(state) {
      return state.sellers_max
    },
    getPlanItemsMax(state) {
      return state.items_max
    },
    getPlanProvidersMax(state) {
      return state.prov_max
    },
    getPlanPrice(state) {
      return state.price
    },
    getPlanName(state) {
      return state.name_p
    },
    getUserPhoto(state) {
      if (!state.photo) {
        return 'img/user_default.jpeg'
      } else {
        return state.photo
      }
    },
  },
  actions: {
    GetNameFromDB(context) {
      axios
        .post('/general_config_name_pos')
        .then((response) => {
          context.commit('title', response.data.name_ecommerce) //categories will be run from mutation
        })
        .catch(() => {
          console.log('Error........')
        })
    },
    GetTypeUserFromDB(context) {
      //console.log(context);
      axios
        .post('/profile_config_get')
        .then((response) => {
          context.commit('UserProfile', response.data) //categories will be run from mutation
        })
        .catch(() => {
          console.log('Error........')
        })
    },
    GetPlanFromDB(context) {
      //console.log(context);
      axios
        .post('/get/plan')
        .then((response) => {
          //console.log(response);
          context.commit('UserPlan', response.data) //categories will be run from mutation
        })
        .catch(() => {
          console.log('Error........')
        })
    },
  },
  mutations: {
    title(state, data) {
      state.titlePOS = data
    },
    setname(state, data) {
      state.name = data
    },
    setphoto(state, data) {
      state.photo = data
    },
    UserPlan(state, data) {
      state.name_p = data.name
      state.clients_max = data.clients_max
      state.items_max = data.items_max
      state.prov_max = data.prov_max
      state.cat_max = data.cat_max
      state.sellers_max = data.sellers_max
      state.price = data.price
    },
    UserProfile(state, data) {
      state.fk_id_user_type = data.fk_id_user_type
      state.name = data.name
      state.last_name = data.last_name
      state.photo = data.photo
      state.email = data.email
    },
  },
}
