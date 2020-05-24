<template>
  <q-page padding>
    <q-toolbar :glossy="$q.theme === 'mat'" :inverted="$q.theme === 'ios'" color="grey">
      <q-toolbar-title class="text-weight-bold text-blue">CONFIGURACIÓN DE PUNTOS</q-toolbar-title>
    </q-toolbar>
    <div class="q-gutter-y-md column" style="max-width: 700px">
      <q-card flat>
        <q-tabs
          :value="defaultTab"
          active-color="primary"
          indicator-color="primary"
          class="text-grey"
          dense
          align="justify"
          narrow-indicator
        >
          <q-tab name="levels" icon="thumb_up" label="Niveles" @click="setTabName('levels')" />
          <q-tab name="promo" icon="update" label="CARGAR" @click="setTabName('promo')" />
        </q-tabs>

        <q-separator />

        <q-tab-panels
          :value="defaultTab"
          animated
          transition-prev="jump-up"
          transition-next="jump-up"
        >
          <q-tab-panel name="levels">
            <kLevels></kLevels>
          </q-tab-panel>

          <q-tab-panel name="promo">
            <kLoad></kLoad>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import kLevels from "./levels.vue";
import kLoad from "./loadPoints.vue";
import store from "../../store";

export default {
  middleware: "auth",
  components: {
    kLevels,
    kLoad
  },
  data() {
    return {
      splitterModel: 20
    };
  },
  computed: {
    defaultTab() {
      return this.$store.getters["admin/defaultTabPoints"];
    }
  },
  methods: {
    setTabName(val) {
      this.$store.dispatch("admin/setDefaultTabPoints", val)
    }
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 
</style>
