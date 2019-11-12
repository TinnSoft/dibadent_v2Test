<template>
  <q-card flat>
    <q-card-section class="bg-light-blue-4 text-white">
      <div class="text-h6">{{kValue}}</div>
      <div class="text-subtitle2">{{title}}</div>
    </q-card-section>
  </q-card>
</template>

<script type="text/javascript">
export default {
  name: "kCard",
  props: ["title", "total", "backgroundColor", "iconName", "subtitle"],
  computed: {
    kValue() {
      return this.total;
    }
  },
  data() {
    return {
      options: {
        separator: "."
      }
    };
  }
};
</script>
