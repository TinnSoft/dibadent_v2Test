<template>
  <q-page padding>
    <div class="q-pa-md" style="max-width: 400px">
      <q-list bordered padding class="rounded-borders center">
        <q-item>
          <q-item-section>
            <q-item-label header>RECUPERAR CONTRASEÑA</q-item-label>
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

              <q-btn
                @click="send"
                :loading="progress"
                rounded
                v-model="progress"
                color="primary"
                class="full-width glossy"
              >Reenviar contraseña</q-btn>
              
              <q-separator spaced></q-separator>
              <br />
              <small>
                <router-link :to="{ name: 'login' }">Ingresar a Dibadent</router-link>
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

import { required, email } from "vuelidate/lib/validators";

export default {
  metaInfo: { titleTemplate: "Reset Password | %s" },

  data: () => ({
    middleware: "guest",
    name: "reset",
    status: null,
    progress: false,
    form: new Form({ email: "" })
  }),
  validations: {
    email: { required, email }
  },
  methods: {
    showNotification(color, message, icon) {
      this.$q.notify({
        message: message,
        color: color,
        icon: icon,
        position: "left",
        actions: [{ label: "Cancelar", color: "white" }]
      });
    },
    async send() {
      let vm = this;
      vm.progress = true;
      try {   
         const { data } = await vm.form.post("/api/password/reset_password");
         vm.form.reset();
          vm.showNotification( "green","Se ha enviado una nueva contraseña al correo ingresado", "sentiment_very_satisfied");
         vm.progress = false;
      } catch (err) {
        vm.showNotification( "red","Oppps! Algo salió mal, intente de nuevo", "sentiment_very_dissatisfied");
        vm.progress = false;
      }
    }
  }
};
</script>
