<template>
  <!--<q-layout view="hHh LpR lFf">-->
  <q-layout view="hHh lpR fFf">
    <q-header reveal elevated class="bg-primary text-white glossy">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="left = !left" />

        <q-toolbar-title>{{title}}</q-toolbar-title>

        <q-btn round dense flat icon="notifications">
          <q-badge color="red" text-color="white" floating>2</q-badge>
          <q-tooltip>Notificaciones</q-tooltip>
        </q-btn>
        <q-btn
          flat
          dense
          v-if="!$q.platform.within.iframe"
          icon="exit_to_app"
          class="q-mr-sm"
          label="Salir"
          @click.native="logout"
        ></q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer
      side="left"
      v-model="left"
      :overlay="leftOverlay"
      :behavior="leftBehavior"
      :breakpoint="leftBreakpoint"
      :mini="miniState"
      @mouseover="miniState = false"
      @mouseout="miniState = true"
      show-if-above
      content-class="bg-grey-1"
      :width="240"
    >
      <q-scroll-area class="fit">
        <q-list padding style="max-width: 350px">
          <div v-for="item in items" v-bind:key="item.title">
            <q-item
              dense
              item
              :to="item.path"
              :key="item.title"
              clickable
              v-ripple
              active-class="text-primary text-bold"
            >
              <q-item-section avatar>
                <q-icon color="grey" :name="item.icon"></q-icon>
              </q-item-section>
              <q-item-section>{{item.title}}</q-item-section>
            </q-item>
          </div>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script type="text/javascript">
import store from "../store";

export default {
  props: ["title", "backgroundColor", "subtitle", "items"],
  data() {
    return {
      miniState: true,
      header: true,
      headerReveal: true,
      left: true,
      leftOverlay: false,
      leftBehavior: "default",
      leftBreakpoint: 992,
      scrolling: true
    };
  },
  computed: {
    email() {
      return this.$store.getters["auth/user"].email;
    }
  },
  created(){
    console.log('created: ',this.$store.getters["auth/user"].profile)
  },
  methods: {
    /*  _subString(val) {
      return val.substring(0, 1);
    },*/

    async logout() {
      await this.$store.dispatch("auth/logout");
      this.$router.push({ name: "login" });
    }
  }
};
</script>