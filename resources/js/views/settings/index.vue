<template>
  <q-page padding>
    <q-toolbar :glossy="$q.theme === 'mat'" :inverted="$q.theme === 'ios'" color="grey">
      <q-btn flat round dense icon="menu" @click="layoutStore.left = !layoutStore.left" />
      <q-toolbar-title class="text-weight-bold text-blue">CONFIGURACIÓN</q-toolbar-title>
    </q-toolbar>
    <q-layout view="lhh LpR lff" container style="height: 600px" class="no-border">
      <q-drawer
        side="left"
        v-model="layoutStore.left"
        :overlay="layoutStore.leftOverlay"
        :behavior="layoutStore.leftBehavior"
        :breakpoint="layoutStore.leftBreakpoint"
        :content-class="$q.theme === 'mat' ? 'bg-white' : null"
      >
        <q-scroll-area class="fit">
          <q-list padding class="text-primary no-border">
            <q-item
              clickable
              :active="link === 'company'"
              @click="link = 'company'"
              active-class="my-menu-link"
              v-ripple
              to="/settings/company"
            >
              <q-item-section avatar top>
                <q-avatar icon="domain" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label class="text-black" lines="1">Mi Empresa</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="primary" />
              </q-item-section>
            </q-item>
            <q-item
              clickable
              :active="link === 'profile'"
              @click="link = 'profile'"
              active-class="my-menu-link"
              v-ripple
              to="/settings/profile"
            >
              <q-item-section avatar top>
                <q-avatar icon="person_outline" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label class="text-black" lines="1">Mi Perfil</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="primary" />
              </q-item-section>
            </q-item>
            <q-item
              clickable
              :active="link === 'doctor'"
              @click="link = 'doctor'"
              active-class="my-menu-link"
              v-ripple
              to="/settings/doctor"
            >
              <q-item-section avatar top>
                <q-avatar icon="enhanced_encryption" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label class="text-black" lines="1">Medicos</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="primary" />
              </q-item-section>
            </q-item>
            <q-item
              clickable
              :active="link === 'radiologist'"
              @click="link = 'radiologist'"
              active-class="my-menu-link"
              v-ripple
              to="/settings/radiologist"
            >
              <q-item-section avatar top>
                <q-avatar icon="camera_roll" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label class="text-black" lines="1">Radiólogos</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="primary" />
              </q-item-section>
            </q-item>
            <q-item
              clickable
              :active="link === 'patient'"
              @click="link = 'patient'"
              active-class="my-menu-link"
              v-ripple
              to="/settings/patient"
            >
              <q-item-section avatar top>
                <q-avatar icon="accessible" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label class="text-black" lines="1">Pacientes</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="primary" />
              </q-item-section>
            </q-item>
            <q-item
              clickable
              :active="link === 'product'"
              @click="link = 'product'"
              active-class="my-menu-link"
              v-ripple
              to="/settings/product"
            >
              <q-item-section avatar top>
                <q-avatar icon="accessible" color="primary" text-color="white" />
              </q-item-section>

              <q-item-section>
                <q-item-label class="text-black" lines="1">Productos</q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-icon name="chevron_right" color="primary" />
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
