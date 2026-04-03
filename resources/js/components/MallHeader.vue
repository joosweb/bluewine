<template>
  <!-- TOP HEADER -->
  <div id="top-header">
    <div class="container">
      <ul class="header-links pull-left">
        <li>
          <a href="#"><i class="fa fa-phone"></i> {{ this.user_data.phone}}</a>
        </li>
        <li>
          <a href="#"><i class="fa fa-envelope-o"></i>{{ this.user_data.email}}</a>
        </li>
        <li>
          <a href="#"><i class="fa fa-map-marker"></i> {{ this.user_data.address}}</a>
        </li>
      </ul>
      <!-- <ul class="header-links pull-right">
        <li>
          <a href="#"><i class="fa fa-dollar"></i> USD</a>
        </li>
        <li>
          <a href="#"><i class="fa fa-user-o"></i> My Account</a>
        </li>
      </ul> -->
    </div>
  </div>
  <!-- /TOP HEADER -->
</template>

<script>
export default {
  props: {
    user_data: [],
  }
};
</script>