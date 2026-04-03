<template>
  <div>
    
  </div>
</template>

<script>
export default {
  props: {
    categories: []
  },
  methods: {
    maysFirst(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
  },
  mounted() {
    console.log("mall-categories");
  },
};
</script>
