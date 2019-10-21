<template>
  <q-card flat>
    <!-- <q-chip dense  :color="backgroundColor" text-color="white" icon="star">Total {{title}}</q-chip>
    <q-card>
      <q-card-section>
        <div class="text-h6">{{kValue}}</div>
      </q-card-section>
    </q-card>
    </q-card>-->
    <q-card-section class="bg-blue-grey-6 text-white">
      <div class="text-h6">{{kValue}}</div>
      <div class="text-subtitle2">Total {{title}}</div>
    </q-card-section>
  </q-card>
</template>

<script type="text/javascript">
export default {
  name: "kCard",
  props: ["title", "total", "backgroundColor", "iconName", "subtitle"],
  computed: {
    kValue() {
      return accounting.formatMoney(this.total);
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
