<template>
  <div class="q-pa-md">
    <div class="q-gutter-y-md column" style="max-width: 500px">
      <q-input filled v-model="form.name" label="*Nombre" dense />
      <q-input filled v-model="form.address" label="Dirección" dense />
      <q-input filled v-model="form.corporate_email" label="Email" dense />
      <q-input filled v-model="form.phone" label="Telefono" dense />
      <br />
      <q-btn :loading="isProcessing" icon="save" glossy label="GUARDAR" type="submit" color="primary" @click="update"></q-btn>
      <q-inner-loading :visible="isProcessing">
        <q-spinner-mat size="50px" color="teal-4" />Espere por favor...
      </q-inner-loading>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  created() {
    this.fetchData();
  },
  data() {
    return {
      form: {},
      pathToUpdate: "/api/company/",
      path: `company/getCompanyValues`,  
    };
  },
  computed: {    
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
          kNotify(vm, "No se logró actualizar el registro, revise nuevamente..", "negative");
        });
    }
  }
};
</script>
