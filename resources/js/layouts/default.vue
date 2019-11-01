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
    items: []
  }),
  created() {
    console.log("Default: ", store.getters["auth/user"]);

    var itemsADMIN = [
      {
        icon: "home",
        title: "INICIO",
        path: "/",
        type: "alone",
        active: true
      },
      {
        icon: "star",
        title: "PUNTOS",
        type: "alone",
        path: "/points"
      },
      {
        icon: "insert_chart",
        title: "REPORTES",
        path: "/reports",
        type: "alone"
      },
      {
        icon: "build",
        title: "ADMINISTRACIÓN",
        path: "/settings",
        type: "alone"
      },
       {
        icon: "contacts",
        title: "DOCTORES",
        path: "/doctor_home",
        type: "alone"
      }
    ];

    var itemsDOCTOR = [
      {
        icon: "home",
        title: "INICIO",
        path: "/",
        type: "alone",
        active: true
      },
      {
        icon: "star",
        title: "PUNTOS",
        type: "alone",
        path: "/points"
      }
    ];
    if (store.getters["auth/check"]) {
      if (store.getters["auth/user"].profile.description == "ADMIN") {
        this.items = itemsADMIN;
      } else {
        this.items = itemsDOCTOR;
      }
    }
  },
  components: {
    LoginLayout,
    HomeLayout
  },
  computed: mapGetters({
    authenticated: "auth/user" //'auth/check'
  })
};
</script>