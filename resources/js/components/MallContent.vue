<template>
  <div>
    <div class="header-search" style="margin-left:px;">
      <form>
        <select v-model="category" style="width: 32%" class="input-select">
          <option value="" selected>Todas las categorias</option>
          <option :value="c.id" :key="c.id" v-for="c in this.categories">
            {{ maysFirst(c.name.toLowerCase()) }}
          </option>
        </select>
        <input v-model="text_to_search" style="width: 32%" class="input" placeholder="Busca aquí" />
        <button @click="search()" class="search-btn">Buscar</button>
      </form>
    </div>
    <div
      class="col-md-2 col-xs-6"
      :key="i.id"
      v-for="i in this.user_items.data"
    >
      <div class="product" style="height:350px">
        <div class="product-img">
          <img
            :src="i.photo ? '/' + i.photo : '/img/item_default.png'"
            alt=""
          />
        </div>
        <div class="product-body">
          <p class="product-category">{{ i.category_name }}</p>
          <h3 class="product-name">
            <a href="#">{{ i.name.slice(0, 12) }}...</a>
          </h3>
          <h4 class="product-price">$ {{ number_format(String(i.price)) }}</h4>
          <div class="product-rating">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
          </div>
          <!-- <div class="product-btns">
                <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
                <button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">add to compare</span></button>
                <button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
            </div> -->
        </div>
      </div>
    </div>
    <div class="row container">
      <div class="col-md-12">
        <pagination
          :limit=9
          :data="user_items"
          @pagination-change-page="getItems"
        ></pagination>
      </div>
    </div>
  </div>
</template>

<script>
import { number_format } from '../utils/number_format'

export default {
  props: {
    categories: [],
    items: {},
    mall: "",
  },
  data() {
    return {
      category: "",
      text_to_search: "",
      idMall: "",
      user_items: this.items,
    };
  },
  methods: {
    number_format,
    search() {
      alert(this.category);
    },
    maysFirst(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    getItems(page = 1, category = '') {
      axios
        .get("/mall/pagination/items?page=" + page + "&category="+category+"&mall_id=" + this.mall)
        .then((response) => {
          this.user_items = response.data;
        });
    }
  },
};
</script>
