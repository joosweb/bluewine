<template>
    <link rel="stylesheet" type="text/css" :href="'/assets/styles/themes/' + this.css ">
</template>

<script>
export default {
  data(){
    return {
      css:'voodoo.css',
    }
  },
  methods:{
    CssConfig(){
      axios.post('/general_config_get').then(response =>{
        //console.log(response);
        this.css = response.data.default_css;
      });
    },
  },
  mounted(){
    this.CssConfig();
  }
}
</script>

<style lang="css" scoped>
</style>
