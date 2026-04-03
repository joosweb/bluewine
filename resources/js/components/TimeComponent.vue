<template>
  <digital-clock :twelveHour="true" :displaySeconds="false" :blink="true" />
</template>

<script>

import DigitalClock from "vue-digital-clock";

export default {
  components: {
    DigitalClock
  }
};
</script>
