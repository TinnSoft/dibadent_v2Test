<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
      <q-dialog v-model="openInventoryForm" @hide="handleHide">
        <q-card style="width: 400px; max-width: 90vw;">
          <q-bar class="bg-blue text-white">
            <q-icon name="assignment"></q-icon>
            <div>{{toolbarLabel}}</div>

            <q-space></q-space>

            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip>Cerrar</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <div class="doc-container">
              <div class="row">
                <div class="col-12 ">
                  <div class="q-gutter-sm">
                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      clearable
                      :readonly="isReadOnly"
                      :error="checkIfFieldHasError(errors,'description')"
                      v-model="form.description"
                      label="*Descripción"
                    />

                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      clearable
                      :readonly="isReadOnly"
                      v-model="form.required_points"
                      label="Puntos requeridos"
                    />
                   
                  </div>
                </div>
              </div>
            </div>
          </q-card-section>

          <q-card-actions v-if="kindOfProcess!='view'" align="right" class="text-primary">
            <q-btn
              rpunded
              :loading="loading"
              color="primary"
              @click.native="submit()"
              icon="save"
              label="Guardar"
            >
              <span slot="loading">
                <q-spinner-hourglass class="on-left" />
              </span>
            </q-btn>
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </div>
</template>

<script>
import kNotify from "../../../components/messages/Notify.js";

export default {
  data() {
    return {
      isReadOnly: false,
      openInventoryForm: false,
      spinnerText: "Cargando...",
      errors: null,
      themeColor: "secondary",
      editIdAssociate: null,
      isEditActive: false,
      loading: false,
      kindOfProcess: "create",
      error: false,
      toolbarLabel: "NUEVO PRODUCTO",
      model: "products",
      form: {},
      base: {},
      pathFetchData: "/api/products/create",
    };
  },
  components: {},
  methods: {
    handleClick(row) {
      this.form.category_id = row.id;
    },
    handleHide(newVal) {
      this.$emit("hide", newVal);
    },
    checkIfFieldHasError(error, field) {
      try {
        if (error.errors[field]) {
          return true;
        }
      } catch (err) {}

      return false;
    },
    fetchData() {
      var vm = this;
      vm.spinnerText = "Cargando...";
      vm.loading = true;
      axios
        .get(vm.pathFetchData)
        .then(function(response) {
          vm.$set(vm.$data, "form", response.data.form);
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },

    //kindOfProcess= create/edit
    //customerId= (opcional) id del producto cuando se edita
    open(kindOfProcess, customerId) {
      let vm = this;
      vm.isReadOnly = false;
      vm.isEditActive = false;
      vm.kindOfProcess = kindOfProcess;
      vm.category_id = null;
      if (kindOfProcess === "edit") {
        vm.pathFetchData = `/api/${vm.model}/${customerId}/${kindOfProcess}`;
        vm.toolbarLabel = "EDITAR PRODUCTO";
      } else if (kindOfProcess === "view") {
        vm.isReadOnly = true;
        vm.pathFetchData = `/api/${vm.model}/${customerId}/edit`;
        vm.toolbarLabel = "INFORMACIÓN DEL PRODUCTO";
      } else {
        vm.pathFetchData = `/api/${vm.model}/${kindOfProcess}`;
        vm.toolbarLabel = "NUEVO PRODUCTO";
      }

      vm.fetchData();
      vm.openInventoryForm = true;
    },

    submit() {
      this.$set(this.$data.form, "_process", "PRODUCTO");
      if (this.kindOfProcess === "edit") {
        this.spinnerText = "Actualizando...";
        this.update();
      } else {
        this.spinnerText = "Guardando...";
        this.create();
      }
    },
    create() {
      let vm = this;
      vm.$set(vm.$data, "errors", null);
      vm.loading = true;

      axios
        .post(`/api/${vm.model}`, vm.form)
        .then(function(response) {
          if (response.data.created) {
            kNotify(vm, "El registro se creó satisfactoriamente", "positive");
          }
          vm.loading = false;
          vm.open("create", 0);
        })
        .catch(function(error) {
          vm.$set(vm.$data, "errors", error.response.data);
          vm.loading = false;
          let messageError =
            "Ooops! No fue posible guardar el registro actual, intente de nuevo.";
          if (error.response.data.emailAlreadyExists) {
            messageError = error.response.data.emailAlreadyExists;
          }
          kNotify(vm, messageError, "negative");
        });
    },

    update() {
      var vm = this;
      vm.errors = null;
      vm.loading = true;

      axios
        .put(`/api/${vm.model}/${vm.form.id}`, vm.form)
        .then(function(response) {
          if (response.data.updated) {
            kNotify(
              vm,
              "Se actualizó el registro satisfactoriamente",
              "positive"
            );
          }
          vm.loading = false;
        })
        .catch(function(error) {
          vm.errors = error.response.data;
          vm.loading = false;
          kNotify(
            vm,
            "No fue posible actualizar el registro actual, intente de nuevo.",
            "negative"
          );
        });
    }
  }
};
</script>
