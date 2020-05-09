<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
      <q-dialog v-model="showModal">
        <q-card style="width: 400px; max-width: 90vw;">
          <q-bar class="bg-blue text-white">
            <div>{{toolbarLabel}}</div>

            <q-space></q-space>

            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip>Cerrar</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <q-input
              filled
              v-model="password"
              type="password"
              label="Digite su nueva Contraseña"
              clearable
              dense
            ></q-input>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <div
              class="text-caption text-grey"
            >Las contraseñas poco seguras son fáciles de descifrar para otras personas y computadoras. No olvides usar contraseñas seguras.</div>
          </q-card-section>
          <q-card-section>
            <div>
              <q-btn
                @click="updatePassword"
                :loading="progress"
                rounded
                :disable="isPasswordFilled"
                v-model="progress"
                color="primary"
                class="full-width glossy"
              >Actualizar</q-btn>
            </div>
          </q-card-section>
        </q-card>
      </q-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      password: "",
      progress: false,
      showModal: false,
      toolbarLabel: "ACTUALIZAR MI CONTRASEÑA",
      urlToUpdatePW: "/api/updatePassword/"
    };
  },
  computed: {
    isPasswordFilled() {
      if (this.password.length > 3) {
        return false;
      }
      return true;
    }
  },
  methods: {
    showNotification(color, message, icon) {
      this.$q.notify({
        message: message,
        color: color,
        icon: icon
      });
    },
    open() {
      let vm = this;
      vm.showModal = true;
      vm.password = "";
    },
    updatePassword() {
      let vm = this;
      vm.progress = true;
      axios
        .post(vm.urlToUpdatePW + vm.password)
        .then(res => {
          vm.showNotification(
            "green",
            "Se ha actualizado tu contraseña",
            "sentiment_very_satisfied"
          );
          vm.progress = false;
          vm.password = "";
        })
        .catch(error => {
          vm.showNotification(
            "red",
            "Oppps! Algo salió mal, intente de nuevo",
            "sentiment_very_dissatisfied"
          );
          vm.progress = false;
        });
    }
  }
};
</script>
