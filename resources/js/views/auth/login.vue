<template>
  <q-page padding>
    <div class="q-pa-md" style="max-width: 400px">
      <q-list bordered padding class="rounded-borders center">
        <q-item>
          <q-item-section>
            <q-item-label header>INICIAR SESION</q-item-label>
            <q-form @keydown="form.errors.clear($event.target.name)">
              <q-input
                dense
                filled
                v-model="form.email"
                type="email"
                :error="form.errors.has('email')"
                label="Tu correo"
                clearable
              ></q-input>

              <q-input
                filled
                v-model="form.password"
                type="password"
                :error="form.errors.has('password')"
                label="Tu Contraseña"
                clearable
                dense
              ></q-input>

              <q-btn
                @click="login"
                :loading="progress"
                rounded
                v-model="progress"
                color="primary"
                class="full-width glossy"
              >Ingresar</q-btn>
              <q-separator spaced></q-separator>
              <br />
              <small>
                <router-link :to="{ name: 'password.reset' }">Olvidaste la contraseña?</router-link>
              </small>
            </q-form>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </q-page>
</template>

<script>
import Form from "vform";

export default {
  name: "login",
  middleware: "guest",
  components: {},
  metaInfo() {
    return { title: this.$t("login") };
  },
  data: () => ({
    form: new Form({
      email: "",
      password: "",
      remember: true
    }),
    remember: true,
    progress: false
  }),
  methods: {
    showNotification() {
      this.$q.notify({
        message: "Porfavor revise las credenciales que ingresó",
        color: "red",
        icon: "sentiment_very_dissatisfied",
        position: "left"
      });
    },
  
    async login() {
      let vm = this;
      vm.progress = true;

      try {
        // Submit the form.
        const { data } = await vm.form.post("/api/login");

        // Save the token.
        vm.$store.dispatch("auth/saveToken", {
          token: data.token,
          remember: vm.remember
        });

        // Fetch the user.
        await vm.$store.dispatch("auth/fetchUser");
        vm.$router.push({ name: "home" });
    
      } catch (err) {
        vm.progress = false;
        this.showNotification();
      }
    }
  }
};
</script>