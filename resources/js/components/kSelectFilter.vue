<template>
  <q-select
    v-model="model"
    v-bind="$attrs"
    v-on="$listeners"
    ref="select"
    @filter="filterFunc"
    :options="opt"
  >
    <template v-slot:no-option>
      <q-item>
        <q-item-section class="text-grey">Sin Resultados</q-item-section>
      </q-item>
    </template>
  </q-select>
</template>
<script>
export default {
  name: "kSelectFilter",
  inheritAttrs: false,
  props: {
    selfFilter: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      model: null,
      options: Object.freeze(this.$attrs.options)
    };
  },
  methods: {
    // use this default filter function
    filterFn(val, update) {
      if (!this.selfFilter) {
        return;
      }

      update(() => {
        try {
          const needle = val.toLowerCase();
          this.options = this.$attrs.options.filter(
            v => v.label.toLowerCase().indexOf(needle) > -1
          );
        } catch (error) {}
      });
    },
    filterFunc(v, u, a) {
      this.selfFilter ? this.filterFn(v, u) : this.$emit("filter", v, u, a);
    }
  },
  computed: {
    opt() {
      return this.selfFilter ? this.options : this.$attrs.options;
    }
  }
};
</script>

<style>
</style>
