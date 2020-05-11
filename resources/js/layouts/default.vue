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
    subtitle: "Sistema de gestion de Dibadent",
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
        icon: "chat",
        title: "CHAT",
        path: "/chat",
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
      {
        icon: "chat",
        title: "CHAT",
        type: "alone",
        path: "/chat_doctor",
        profileId: 3,
        id: 7
      },
      {
        icon: "home",
        title: "INICIO",
        path: "/",
        type: "alone",
        active: true,
        profileId: 2,
        id: 8
      },
      {
        icon: "shopping_cart",
        title: "CUPONES",
        type: "alone",
        path: "/products-redemption",
        profileId: 2,
        id: 9
      },
      {
        icon: "build",
        title: "ADMINISTRACIÓN",
        path: "/settings",
        type: "alone",
        profileId: 2,
        id: 10
      },
      {
        icon: "build",
        title: "ADMINISTRACIÓN",
        path: "/settings",
        type: "alone",
        profileId: 3,
        id: 11
      }
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