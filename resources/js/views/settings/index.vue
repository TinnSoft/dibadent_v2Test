<template>
  <q-page padding>
    <q-toolbar :glossy="$q.theme === 'mat'" :inverted="$q.theme === 'ios'" color="grey">
      <q-btn flat round dense icon="menu" @click="layoutStore.left = !layoutStore.left" />
      <q-toolbar-title class="text-weight-bold text-blue">Configuración</q-toolbar-title>
    </q-toolbar>
    <q-layout view="lhh LpR lff" container style="height: 500px" class="shadow-2 rounded-borders">
      <q-drawer
        side="left"
        v-model="layoutStore.left"
        :overlay="layoutStore.leftOverlay"
        :behavior="layoutStore.leftBehavior"
        :breakpoint="layoutStore.leftBreakpoint"
        :content-class="$q.theme === 'mat' ? 'bg-white' : null"
      >
        <q-scroll-area class="fit">
          <q-list padding class="text-primary">
            <q-item clickable :active="link === 'company'" @click="link = 'company'" active-class="my-menu-link" v-ripple to="/settings/company">
              <q-item-section avatar top>
                <q-avatar icon="domain" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">Mi Empresa</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="green" />
              </q-item-section>
            </q-item>
            <q-item clickable :active="link === 'profile'" @click="link = 'profile'" active-class="my-menu-link" v-ripple to="/settings/profile">
              <q-item-section avatar top>
                <q-avatar icon="person_outline" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">Mi Perfil</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="green" />
              </q-item-section>
            </q-item>
            <q-item clickable :active="link === 'doctor'" @click="link = 'doctor'" active-class="my-menu-link" v-ripple to="/settings/doctor">
              <q-item-section avatar top>
                <q-avatar icon="enhanced_encryption" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">Medicos</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="green" />
              </q-item-section>
            </q-item>
            <q-item clickable :active="link === 'radiology'" @click="link = 'radiology'" active-class="my-menu-link" v-ripple to="/settings/radiology">
              <q-item-section avatar top>
                <q-avatar icon="camera_roll" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">Permisos de radiologo</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="green" />
              </q-item-section>
            </q-item>
            <q-item clickable :active="link === 'patient'" @click="link = 'patient'" active-class="my-menu-link" v-ripple to="/settings/2">
              <q-item-section avatar top>
                <q-avatar icon="accessible" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">Pacientes</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="green" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-scroll-area>
      </q-drawer>
      <q-page-container>
        <router-view />
      </q-page-container>
    </q-layout>
  </q-page>
</template>

<script>
export default {
  middleware: "auth",
  data() {
    return {
      link: "profile",
      layoutStore: {
        header: true,
        reveal: false,
        left: true,
        leftScroll: true,
        rightScroll: true,
        leftOverlay: false,
        leftBehavior: "default",
        leftBreakpoint: 996,
        rightBreakpoint: 1200
      }
    };
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 
</style>
