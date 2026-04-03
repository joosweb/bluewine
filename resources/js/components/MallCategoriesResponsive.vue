<template>
  <ul class="main-nav nav navbar-nav">
    <!-- <li class="active"><a href="#">Home</a></li> -->
    <li :value="c.id" :key="c.id" v-for="c in this.categories">
      <a href="#">{{ maysFirst(c.name.toLowerCase()) }}</a>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    categories: [],
  },
  methods: {
    maysFirst(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
  },
  mounted() {
    console.log("mall-categorires-responsive");
  },
};
</script>

