<template>
  <q-card flat>
    <q-card-section class="bg-grey-1 text-blue">
      <div class="text-h3 text-center">{{kValue}}</div>
      <div class="text-caption text-grey text-center">{{title}}</div>
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
