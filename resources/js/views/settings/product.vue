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
            label="Nuevo Producto"
            @click="openProductModal($refs,'create')"
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
            @click="editProductModal($refs, props.row)"
          ></kButton>
          <kButton color="grey" iconname="remove_red_eye" tooltiplabel="Ver" @click="show($refs, props.row)"></kButton>
          <kButton color="grey" iconname="delete" tooltiplabel="Eliminar" @click="remove(props)"></kButton>
        </q-td>
      </q-table>
      <productModal ref="_product" @hide="closeProductModal"></productModal>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import kButton from "../../components/tables/cButton.vue";
import productModal from "./modals/mProduct.vue";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  components: {
    kButton,
    productModal
  },
  created() {
    this.columns = productColumns();
    this.fetchData();
  },
  data() {
    return {
      model: "products",
      filter: "",
      loading: false,
      table: [],
      columns: [],
      pagination: {
        rowsPerPage: 10
      },      
      visibleColumns:['description','required_points','actions'],
      form: {},
      path: "getProductList"
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
     closeProductModal() {
      this.fetchData();
    },
    show(refs, cell) {
      //this.$router.push(`/${this.model}/${cell.row.public_id}`);
      this.openProductModal(refs, "view", cell.id);
    },
    editProductModal(refs, cell) {
      this.openProductModal(refs, "edit", cell.id);
    },
    openProductModal(refs, processType, itemId) {
      refs._product.open(processType, itemId);
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

       vm.$q.dialog({
        title: "Tenga Cuidado!",
          message: "Está eliminando un registro importante, desea continuar?",
          ok: "SI, Eliminar!",
          cancel: "NO, Cancelar"
      }).onOk(() => {
          axios
            .delete("/api/products/" + val.row.id)
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
      }).onCancel(() => {
         vm.loading = false;
      }).onDismiss(() => {
        vm.loading = false;
      })

    }
  }
};

function productColumns() {
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
      label: "Descripción",
      field: "description",
      name: "description",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Puntos Requeridos",
      field: "required_points",
      name: "required_points",
      sortable: true,
      filter: true
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
