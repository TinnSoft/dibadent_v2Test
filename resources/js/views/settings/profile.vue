<template>
  <div class="q-pa-md">
    <div class="q-gutter-y-md column" style="max-width: 500px">
      <q-input clearable filled v-model="form.name" label="*Nombre" dense />
      <q-input clearable filled v-model="form.last_name" label="Apellido" dense />
      <q-input clearable filled v-model="form.email" type="email" label="*Email" dense />
      <q-input clearable filled v-model="form.home_address" label="Dirección de residencia" dense />
      <q-input clearable filled v-model="form.phone" type="tel" label="Telefono" dense />

      <q-input
        clearable
        filled
        v-model="form.birthday"
        mask="date"
        :rules="['date']"
        label="Fecha de Cumpleaños"
        dense
      >
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
              <q-date v-model="form.birthday" @input="() => $refs.qDateProxy.hide()" />
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>

      <br />
      <q-btn icon="save" glossy label="GUARDAR" color="primary" @click="update"></q-btn>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import kNotify from "../../components/messages/Notify.js";

export default {
  created() {
    this.fetchData();
  },
  data() {
    return {
      isPwd: true,
      form: {},
      path: "users/getCompanyValues/",
      pathToUpdate: "/api/users/"
    };
  },
  computed: {
    canSave() {
      if (this.form.name) {
        return true;
      }
      return false;
    }
  },
  methods: {
    fetchData() {
      let vm = this;
      vm.isProcessing = true;
      axios
        .get(`/api/${vm.path}`)
        .then(function(response) {
          vm.$set(vm, "form", response.data.form);
          vm.isProcessing = false;
        })
        .catch(function(error) {
          vm.isProcessing = false;
        });
    },
    update() {
      let vm = this;
      axios
        .put(vm.pathToUpdate + vm.form.id, vm.form)
        .then(function(response) {
          if (response.data.updated) {
            kNotify(
              vm,
              "El registro se actualizó satisfactoriamente",
              "positive"
            );
          } else {
          }
        })
        .catch(function(error) {
          kNotify(vm, "No se ha podido actualizar el registro", "negative");
        });
    }
  }
};
</script>
