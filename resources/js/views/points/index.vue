<template>
  <q-page padding>
    <q-toolbar :glossy="$q.theme === 'mat'" :inverted="$q.theme === 'ios'" color="grey">
      <q-toolbar-title class="text-weight-bold text-blue">CONFIGURACIÓN DE PUNTOS</q-toolbar-title>
    </q-toolbar>
    <div class="q-gutter-y-md column" style="max-width: 700px">
      <q-splitter v-model="splitterModel" style="height: 700px">
        <template v-slot:before>
          <q-tabs v-model="defaultTab" vertical>
            <q-tab name="levels" icon="thumb_up" label="Niveles" />
            <q-tab name="promo" icon="local_offer" label="Promos" />
          </q-tabs>
        </template>

        <template v-slot:after>
          <q-tab-panels v-model="defaultTab" animated transition-prev="jump-up" transition-next="jump-up">
            <q-tab-panel name="levels">
              <kLevels></kLevels>
            </q-tab-panel>

            <q-tab-panel name="promo"></q-tab-panel>
          </q-tab-panels>
        </template>
      </q-splitter>
    </div>
  </q-page>
</template>

<script>
import kLevels from "./levels.vue";

export default {
  middleware: "auth",
  components: {
    kLevels
  },
  data() {
    return {
      defaultTab: "levels",
      splitterModel: 20
    };
  },
  created() {
  },
  methods: {
  
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 
</style>
