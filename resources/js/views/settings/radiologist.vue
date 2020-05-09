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
        :grid="$q.screen.xs"
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
            label="Nuevo Radiologo"
            @click="openRadiologistModal($refs,'create')"
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
            @click="editRadiologistModal($refs, props.row)"
          ></kButton>
          <kButton
            color="grey"
            iconname="remove_red_eye"
            tooltiplabel="Ver"
            @click="show($refs, props.row)"
          ></kButton>
          <kButton color="grey" iconname="delete" tooltiplabel="Eliminar" @click="remove(props)"></kButton>
        </q-td>
      </q-table>
      <radiologistModal ref="_radiologist" @hide="closeRadiologistModal"></radiologistModal>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import kButton from "../../components/tables/cButton.vue";
import radiologistModal from "./modals/mRadiologist.vue";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  components: {
    kButton,
    radiologistModal
  },
  created() {
    this.columns = radiologistColumns();
    this.fetchData();
  },
  data() {
    return {
      model: "users",
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
      path: "getRadiologistlist"
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
    closeRadiologistModal() {
      this.fetchData();
    },
    show(refs, cell) {
      this.openRadiologistModal(refs, "view", cell.id);
    },
    editRadiologistModal(refs, cell) {
      this.openRadiologistModal(refs, "edit", cell.id);
    },
    openRadiologistModal(refs, processType, itemId) {
      refs._radiologist.open(processType, itemId);
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
          axios.delete("/api/users/" + val.row.id).then(function(response) {
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

function radiologistColumns() {
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
