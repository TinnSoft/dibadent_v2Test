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
        icon: "assessment",
        title: "PUNTOS",
        type: "alone",
        path: "/2",      
      },
      {
        icon: "build",
        title: "CONFIGURACIÓN",
        path: "/settings",
        type: "alone"
      },
      {
        icon: "pie_chart",
        title: "REPORTES",
        path: "/config6",
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