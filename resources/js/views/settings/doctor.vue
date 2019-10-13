<template>
  <div class="q-pa-md">
    <div class="q-gutter-y-md column" style="max-width: 500px">
      <!-- <q-input filled v-model="form.name" label="*Nombre" dense/>
        <q-input filled v-model="form.last_name" label="Apellido" dense/>
        <q-input filled v-model="form.email" label="*Email" dense/>
        <q-input filled v-model="form.last_name" label="Fecha de nacimiento" dense/>
      <q-input filled v-model="form.last_name" label="Periodo de trabajo" dense/>-->
<!--
      <q-table
        ref="mainTable"
        :data="table"
        :columns="columns"
        row-key="id"
        :loading="loading"
        :filter="filter"
        dense
      >
        <template v-slot:top="props">
          <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
            <template v-slot:append>
              <q-icon name="search"></q-icon>
            </template>
          </q-input>
          <q-space></q-space>
          <q-btn
            flat
            dense
            color="primary"
            :disable="loading"
            label="Nuevo Doctor"
            @click="openDoctorModal($refs,'create')"
          />
          <q-btn
            flat
            round
            dense
            :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
            @click="props.toggleFullscreen"
            class="q-ml-md"
          >
            <q-tooltip>Ver en pantalla completa</q-tooltip>
          </q-btn>
        </template>

        <q-td slot="body-cell-actions" slot-scope="props" :props="props">
          <kButton
            color="grey"
            iconname="edit"
            tooltiplabel="Editar"
            @click="editDoctorModal($refs, props.row)"
          ></kButton>
          <kButton color="grey" iconname="remove_red_eye" tooltiplabel="Ver" @click="show(props)"></kButton>
          <kButton color="grey" iconname="delete" tooltiplabel="Eliminar" @click="remove(props)"></kButton>
        </q-td>
      </q-table>
      -->
    </div>
  </div>
</template>

<script>
import store from "../../store";
import kButton from "../../components/tables/cButton.vue";
import DoctorModal from "./modals/mDoctor.vue";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  components: {
    kButton,
    DoctorModal
  },
  created() {
    try {
      let vm = this;
      console.log('info de usuario: ',store.getters["auth/user"])
      vm.$set(vm.$data.form, "id", store.getters["auth/user"].id);
      vm.$set(vm.$data.form, "name", store.getters["auth/user"].name);
      vm.$set(vm.$data.form, "email", store.getters["auth/user"].email);
      vm.$set(vm.$data.form, "last_name", store.getters["auth/user"].last_name);
    } catch (e) {}
  },
  data() {
    return {
      form: {},
      path: "/api/profile/"
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
      vm.loading = true;
      console.log('path: ',vm.path)
      axios
        .get(`/api/${vm.path}`)
        .then(function(response) {
          vm.$set(vm, "table", response.data.records);
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },
    remove(val, filter) {
      let vm = this;
      vm.loading = true;

      vm.$q
        .dialog({
          title: "Tenga Cuidado!",
          message: "Está eliminando un registro importante, desea continuar?",
          ok: "SI, Eliminar!",
          cancel: "NO, Cancelar",
          color: "secondary"
        })
        .then(() => {
          axios
            .delete("/api/inventory/" + val.row.id)
            .then(function(response) {
              if (response.data.deleted) {
                kNotify(
                  vm,
                  "Se eliminó el registro satisfactoriamente",
                  "positive"
                );
                vm.fetchData();
                vm.loading = false;
              }
            })
            .catch(function(error) {
              kNotify(
                vm,
                "No fue posible eliminar el registro seleccionado, intente de nuevo.",
                "negative"
              );
              vm.loading = false;
            });
        })
        .catch(() => {
          vm.loading = false;
        });
    }
  }
};
</script>
