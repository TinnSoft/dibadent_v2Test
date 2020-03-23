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
          <q-menu>
            <div class="row no-wrap q-pa-md">
              <q-list bordered class="rounded-borders" style="max-width: 600px">
                <q-item-label header>Notificaciones</q-item-label>
                <q-item>
                  <q-item-section top>
                    <q-item-label lines="1">
                      <span class="text-grey-8">- GitHub repository</span>
                    </q-item-label>
                  </q-item-section>
                  <q-item-section top side>
                    <div class="text-grey-8 q-gutter-xs">
                      <q-btn class="gt-xs" size="12px" flat dense round icon="done"></q-btn>
                    </div>
                  </q-item-section>
                </q-item>

                <q-separator spaced></q-separator>

                <q-item>
                  <q-item-section top>
                    <q-item-label lines="1">
                      <span class="text-grey-8">- GitHub repository</span>
                    </q-item-label>
                  </q-item-section>
                  <q-item-section top side>
                    <div class="text-grey-8 q-gutter-xs">
                      <q-btn class="gt-xs" size="12px" flat dense round icon="done"></q-btn>
                    </div>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </q-menu>
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
          <div v-for="item in items" v-bind:key="item.id">
            <q-item
              v-if="profile==item.profileId"
              dense
              item
              :to="item.path"
              :key="item.id"
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
      scrolling: true,
    };
  },
  computed: {
    email() {
      return this.$store.getters["auth/user"].email;
    },
    profile() {
      return this.$store.getters["auth/user"].profile_id;
    }
  },
  created() {
    let profileName = this.$store.getters["auth/user"].profile.description;

    console.log(profileName);
  },
  methods: {
    getNotifications(){

    },
    async logout() {
      await this.$store.dispatch("auth/logout");
      this.$router.push({ name: "login" });
    }
  }
};
</script>