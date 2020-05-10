<template>
  <div class="q-pa-md">
    <div class="q-gutter-y-md column">
      <q-table
        ref="mainTable"
        :data="table"
        :columns="columns"
        row-key="id"
        :loading="loading"
        :filter="filter"
        :pagination.sync="pagination"
        dense
        flat
        bordered
        :visible-columns="visibleColumns"
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
            label="Nuevo Paciente"
            @click="openPatientModal($refs,'create')"
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
        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div>
              <kButton
                color="grey"
                iconname="edit"
                tooltiplabel="Editar"
                @click="editPatientModal($refs, props.row)"
              ></kButton>
              <kButton
                color="grey"
                iconname="remove_red_eye"
                tooltiplabel="Ver"
                @click="show($refs, props.row)"
              ></kButton>
              <kButton
                color="grey"
                iconname="delete"
                tooltiplabel="Eliminar"
                @click="remove(props)"
              ></kButton>
            </div>
          </q-td>
        </template>
      </q-table>
      <patientModal ref="_patient" @hide="closePatientModal"></patientModal>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import kButton from "../../components/tables/cButton.vue";
import patientModal from "./modals/mPatient.vue";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  components: {
    kButton,
    patientModal
  },
  created() {
    this.columns = patientColumns();
    this.fetchData();
  },
  data() {
    return {
      model: "patients",
      filter: "",
      loading: false,
      table: [],
      columns: [],
      pagination: {
        rowsPerPage: 10
      },
      visibleColumns: [
        "name",
        "last_name",
        "email",
        "home_address",
        "phone",
        "birthday",
        "actions"
      ],
      form: {},
      path: "getPatientlist"
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
    closePatientModal() {
      this.fetchData();
    },
    show(refs, cell) {
      this.openPatientModal(refs, "view", cell.id);
    },
    editPatientModal(refs, cell) {
      this.openPatientModal(refs, "edit", cell.id);
    },
    openPatientModal(refs, processType, itemId) {
      refs._patient.open(processType, itemId);
    },
    fetchData() {
      let vm = this;
      vm.loading = true;

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
          cancel: "NO, Cancelar"
        })
        .onOk(() => {
          axios.delete("/api/patients/" + val.row.id).then(function(response) {
            if (response.data.deleted) {
              kNotify(
                vm,
                "Se eliminó el registro satisfactoriamente",
                "positive"
              );
              vm.fetchData();
              vm.loading = false;
            }
          });
        })
        .onCancel(() => {
          vm.loading = false;
        })
        .onDismiss(() => {
          vm.loading = false;
        });
    }
  }
};

function patientColumns() {
  return [
    {
      label: "ID",
      field: "id",
      name: "id",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Nombre",
      field: "name",
      name: "name",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Apellido",
      field: "last_name",
      name: "last_name",
      sortable: true,
      filter: true
    },
    {
      label: "Email",
      field: "email",
      name: "email",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Dirección",
      field: "home_address",
      name: "home_address",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Telefono",
      field: "phone",
      name: "phone",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Acciones",
      field: "actions",
      name: "actions",
      type: "string"
    }
  ];
}
</script>
