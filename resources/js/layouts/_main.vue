<template>
  <!--<q-layout view="hHh LpR lFf">-->
  <q-layout view="hHh lpR fFf">
    <q-header reveal elevated class="bg-primary text-white glossy">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="left = !left" />

        <q-toolbar-title>{{title}}</q-toolbar-title>

        <q-btn round dense flat icon="notifications">
          <q-badge
            v-if="newNotifications>0"
            color="red"
            text-color="white"
            floating
          >{{newNotifications}}</q-badge>
          <q-tooltip>Notificaciones</q-tooltip>
          <q-menu>
            <q-toolbar class="bg-grey-3">
              <q-toolbar-title>Notificaciones</q-toolbar-title>
            </q-toolbar>
            <template v-if="notificationLog">
              <q-list bordered class="rounded-borders" style="max-width: 600px">
                <div v-for="(_notification,index) in notificationLog" v-bind:key="_notification.id">
                  <q-item dense clickable>
                    <q-item-section top>
                      <q-item-label caption>{{_notification.detail}}</q-item-label>
                    </q-item-section>
                    <q-item-section top side>
                      <div class="text-grey-8 q-gutter-xs">                        
                        <q-btn
                          size="sm"
                          color="blue"
                          dense
                          round
                          icon="check"
                          @click="eraseNotification(_notification.id, index)"
                        >
                          <q-tooltip>Marcar como leido</q-tooltip>
                        </q-btn>
                      </div>
                    </q-item-section>
                  </q-item>
                  <q-separator spaced inset />
                </div>
              </q-list>
            </template>
            <template v-else>
              <q-list bordered class="rounded-borders" style="max-width: 600px">
                <q-item>
                  <q-item-section avatar>
                    <q-icon color="primary" name="notifications_none" />
                  </q-item-section>
                  <q-item-section class="text-orange">No tienes notificaciones disponibles</q-item-section>
                </q-item>
              </q-list>
            </template>
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
      notificationLog: null,
      newNotifications: 0,
      urlToUpdateNotification: "/api/markNotificationAsRead/"
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
    this.getNotification();
  },
  methods: {
    async logout() {
      await this.$store.dispatch("auth/logout");
      this.$router.push({ name: "login" });
    },
    async eraseNotification(notificationID, index) {
      console.log("borrar notificación", notificationID, index);
      this.notificationLog.splice(index, 1);
      this.markNotificationAsRead(notificationID);
      this.$set(this, "newNotifications", this.newNotifications - 1);
    },
    markNotificationAsRead(notificationID) {
      axios
        .post(this.urlToUpdateNotification + notificationID)
        .then(res => {
          console.log(res);
        })
        .catch(error => {
          console.log(error);
        });
    },
    async getNotification() {
      let profileName = this.$store.getters["auth/user"].profile.description;
      let path = "getNotificationList";
      let vm = this;
      axios
        .get(`/api/${path}/${profileName}`)
        .then(function(response) {
          if (response.data.notifications) {
            vm.$set(vm, "notificationLog", response.data.notifications);
            vm.$set(vm, "newNotifications", vm.notificationLog.length);
            console.log(response.data, vm.newNotifications);
          }
        })
        .catch(function(error) {});
    }
  }
};
</script>