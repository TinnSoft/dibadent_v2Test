<template id="q-app">
  <div>
    <HomeLayout
      v-if="authenticated"
      :title="title"
      :subtitle="subtitle"
      :background-color="backgroundcolor"
      :items="items"
    ></HomeLayout>
    <LoginLayout v-else :title="title" :subtitle="subtitle" :background-color="backgroundcolor"></LoginLayout>
  </div>
</template>

<script>
import LoginLayout from "./_login.vue";
import HomeLayout from "./_main.vue";
import store from "~/store";
import { mapGetters } from "vuex";

export default {
  name: "MainLayout",
  data: () => ({
    title: window.config.appName,
    subtitle: "Sistema de gestion de radiografías en la nube",
    backgroundcolor: "bg-primary glossy",
    items: [
      {
        icon: "home",
        title: "INICIO",
        path: "/",
        type: "alone",
        active: true,
        profileId: 1,
        id: 1
      },
      {
        icon: "star",
        title: "PUNTOS",
        type: "alone",
        path: "/points",
        profileId: 1,
        id: 2
      },
      {
        icon: "insert_chart",
        title: "REPORTES",
        path: "/reports",
        type: "alone",
        profileId: 1,
        id: 3
      },
      {
        icon: "build",
        title: "ADMINISTRACIÓN",
        path: "/settings",
        type: "alone",
        profileId: 1,
        id: 4
      },      
      {
        icon: "home",
        title: "INICIO",
        path: "/",
        type: "alone",
        active: true,
        profileId: 3,
        id: 5
      },
      {
        icon: "star",
        title: "REDIMIR PUNTOS",
        type: "alone",
        path: "/points-redemption",
        profileId: 3,
        id: 6
      },
    ]
  }),
  created() {},
  components: {
    LoginLayout,
    HomeLayout
  },
  computed: mapGetters({
    authenticated: "auth/user" //'auth/check'
  })
};
</script>