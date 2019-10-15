<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
      <q-dialog v-model="openInventoryForm" @hide="handleHide">
        <q-card style="width: 700px; max-width: 90vw;">
          <q-bar class="bg-blue text-white">
            <q-icon name="mail"></q-icon>
            <div>{{toolbarLabel}}</div>

            <q-space></q-space>

            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip>Cerrar</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <div class="doc-container">
              <div class="row">
                <div class="col-12 col-md-6">
                  <div class="q-gutter-sm">
                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      clearable
                      :error="checkIfFieldHasError(errors,'name')"
                      v-model="form.name"
                      label="*Nombre"
                    />

                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      clearable
                      v-model="form.last_name"
                      label="Apellido"
                    />

                    <q-input
                      clearable
                      hide-bottom-space
                      filled
                      v-model="form.birthday"
                      mask="date"
                      :rules="['date']"
                      label="Fecha de Cumpleaños"
                      dense
                    >
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy
                            ref="qDateProxy"
                            transition-show="scale"
                            transition-hide="scale"
                          >
                            <q-date v-model="form.birthday" @input="() => $refs.qDateProxy.hide()" />
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>

                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      :error="checkIfFieldHasError(errors,'email')"
                      clearable
                      type="email"
                      v-model="form.email"
                      label="*Email"
                    />

                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      clearable
                      v-model="form.home_address"
                      label="Dirección"
                    />

                    <q-input
                      filled
                      hide-bottom-space
                      dense
                      clearable
                      v-model="form.phone"
                      label="Telefono"
                    />
                  </div>
                </div>
              </div>
            </div>
          </q-card-section>

          <q-card-section v-if="kindOfProcess=='create'">
            <kBlockQuote
              textToShow="<strong>La Contraseña</strong> se enviará automáticamente al correo 
                         ingresado en este formulario"
              customClass="doc-note doc-note--tip"
            ></kBlockQuote>
          </q-card-section>
          <q-card-section v-if="kindOfProcess=='edit'">
            <q-banner inline-actions class="bg-grey-3">
              <template v-slot:avatar>
                <q-icon name="email" color="primary" />
              </template>
              Enviar correo reseteando contraseña.
              <template v-slot:action>
                <q-btn flat color="primary" label="Enviar" />
              </template>
            </q-banner>
          </q-card-section>
          <q-card-actions align="right" class="text-primary">
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
      openInventoryForm: false,
      spinnerText: "Cargando...",
      errors: null,
      themeColor: "secondary",
      editIdAssociate: null,
      isEditActive: false,
      loading: false,
      kindOfProcess: "create",
      error: false,
      toolbarLabel: "NUEVO DOCTOR",
      model: "users",
      form: {},
      base: {},
      pathFetchData: "/api/users/create",
      pathCatehory: "getCategoryIncome"
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
      vm.isEditActive = false;
      vm.kindOfProcess = kindOfProcess;
      vm.category_id = null;
      if (kindOfProcess === "edit") {
        vm.pathFetchData = `/api/${vm.model}/${customerId}/${kindOfProcess}`;
        vm.toolbarLabel = "EDITAR DOCTOR";
      } else {
        vm.pathFetchData = `/api/${vm.model}/${kindOfProcess}`;
        vm.toolbarLabel = "NUEVO DOCTOR";
      }

      vm.fetchData();
      vm.openInventoryForm = true;
    },

    submit() {
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
