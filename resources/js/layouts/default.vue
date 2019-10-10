<template id="q-app">
<div >
 <HomeLayout v-if="authenticated" :title="title" :subtitle="subtitle" :background-color="backgroundcolor" :items="items">  
  </HomeLayout>  
  <LoginLayout v-else :title="title" :subtitle="subtitle" :background-color="backgroundcolor">
  </LoginLayout>
</div>
</template>

<script>

import LoginLayout from './_login.vue';
import HomeLayout from './_main.vue';

import { mapGetters } from "vuex";

export default {
  name: 'MainLayout',
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
        active: true
      },
      {
        icon: "star",
        title: "PUNTOS",
        type: "alone",
        path: "/points",      
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
      }
    ]
    
  }),
  components: {
    LoginLayout,HomeLayout
  },
   computed: mapGetters({
    authenticated: 'auth/user'//'auth/check'
  }),
}
</script>