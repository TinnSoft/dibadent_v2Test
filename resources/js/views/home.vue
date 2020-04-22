<template>
  <q-page padding>
    <AdminDashboard v-if="profile==1" />
    <DoctorDashboard v-if="profile==3" />
    <RadiologistDashboard v-if="profile==2" />
  </q-page>
</template>

<script>
import AdminDashboard from "./admin/dashboard.vue";
import DoctorDashboard from "./doctors/dashboard.vue";
import RadiologistDashboard from "./radiologist/dashboard.vue";
import store from "../store";

export default {
  name: "home",
  middleware: "auth",
  components: { AdminDashboard, DoctorDashboard,RadiologistDashboard },
  data: function() {
    return {};
  },
  metaInfo() {
    return { title: this.$t("home") };
  },
  created() {},
  computed: {
    profile() {
      if (this.$store.getters["auth/user"]) {
        return this.$store.getters["auth/user"].profile_id;
      } else {
        return null;
      }
    }
  },
  methods: {}
};
</script>
