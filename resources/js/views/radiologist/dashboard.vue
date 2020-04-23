<template>
  <div>
    <div class="q-pa-md q-pa-xs q-pa-sm items-start q-gutter-md">
      <div class="row">
        <div class="col-auto row_class">
          <q-card flat>
            <q-card-section class="text-blue">
              <q-img
                :src="avatarUrl"
                style="width: 180px"
                ratio="1"
                spinner-color="white"
                class="rounded-borders"
              >
                <div class="absolute-bottom text-center text-body2">
                  <q-btn flat round color="white" icon="update" @click="$refs.avatarInput.click()"></q-btn>
                </div>
              </q-img>
              <input type="file" ref="avatarInput" style="display: none" @change="changeAvatar" />

              <div class="text-h5">{{user_name | capitalize}}</div>
              <q-item-label caption>Bienvenido Radiologo</q-item-label>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-grow col-xs-grow col-sm-grow row_class">
          <q-card flat>
            <q-card-section class="bg-grey-1 text-blue">
              <q-list>
                <q-item>
                  <q-item-section avatar>
                    <q-icon color="primary" name="bookmark_border" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">20</q-item-label>
                    <q-item-label caption>Imagenes Cargadas hasta el momento</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <div class="row">
        <div class="col-3 col-xs-grow row_class rounded-borders" style="min-width: 300px">
          <q-list flat class="bg-grey-1">
            <q-item class="text-blue">
              <q-item-section>
                <q-timeline color="primary">
                  <q-timeline-entry heading>CARGA DE IMAGENES</q-timeline-entry>
                  <q-timeline-entry subtitle="Paciente" icon="perm_identity">
                    <kSelectFilter
                      v-model="form.patient"
                      :options="patientList"
                      :loading="loading"
                      filled
                      dense
                      outlined
                      self-filter
                      clearable
                      use-input
                      fill-input
                      hide-selected
                      emit-value
                      map-options
                      input-debounce="0"
                      label="Seleccione un Paciente"
                      options-dense
                      hide-bottom-space
                      @input="getProcedures(form.patient)"
                    />
                    <q-btn
                      round
                      dense
                      flat
                      icon="info_outline"
                      color="grey-5"
                      @click="showPatientModal($refs)"
                    />
                  </q-timeline-entry>
                  <q-timeline-entry
                    subtitle="Doctor"
                    icon="perm_identity"
                    v-if="doctor_name"
                    color="grey"
                  >
                    <q-input v-model="doctor_name" stack-label dense readonly></q-input>
                  </q-timeline-entry>
                  <q-timeline-entry subtitle="TAG" icon="device_hub">
                    <kSelectFilter
                      ref="_procedureSelect"
                      v-model="form.medicalProcedure"
                      :options="medicalProcedures"
                      :loading="loading"
                      filled
                      dense
                      outlined
                      self-filter
                      clearable
                      use-input
                      fill-input
                      hide-selected
                      emit-value
                      map-options
                      input-debounce="0"
                      label="Seleccione o agregue un tag"
                      options-dense
                      hide-bottom-space
                      @input="getListOfImages(form.medicalProcedure)"
                    />
                    <template v-if="form.patient">
                      <q-btn
                        round
                        dense
                        flat
                        icon="add"
                        color="grey-5"
                        @click="CreateProcedureModal($refs)"
                      />
                    </template>
                    <q-btn
                      round
                      dense
                      flat
                      icon="info_outline"
                      color="grey-5"
                      @click="showProcedureModal($refs)"
                    />
                  </q-timeline-entry>
                </q-timeline>
              </q-item-section>
            </q-item>
            <q-separator spaced />
            <q-item>
              <q-uploader
                class="width: 100%"
                label="Cargar Imagen"
                dense
                flat
                :readonly="checkIfExistProcedure"
                auto-upload
                :url="_medicalProcedureId"
                accept=".jpg, image/*"
                color="primary"
                @uploaded="uloadedFinished"
                multiple
                batch
              />
            </q-item>
          </q-list>
        </div>
        <div class="col-9 col-xs-grow row_class rounded-borders">
          <q-layout container style="height: 700px">
            <q-page-container>
              <q-page>
                <div class="row justify-center q-gutter-sm">
                  <q-img
                    v-for="(image, i) in listOfImages"
                    :key="i"
                    :src="image.file_name"
                    style="width: 150px"
                    ratio="1"
                    spinner-color="white"
                    class="rounded-borders"
                    transition="slide-right"
                  >
                    <div class="absolute-bottom text-center text-body2">
                      <q-btn
                        flat
                        round
                        color="white"
                        icon="zoom_in"
                        @click="showImageModal($refs, image)"
                      ></q-btn>
                      <q-btn flat round color="white" icon="delete" @click="deleteImage(image)"></q-btn>
                    </div>
                  </q-img>
                </div>
                <q-inner-loading :showing="loading">
                  <q-spinner-gears size="50px" color="primary" />
                </q-inner-loading>
              </q-page>
              <q-page-scroller position="bottom">
                <q-btn fab icon="keyboard_arrow_up" color="red" />
              </q-page-scroller>
            </q-page-container>
          </q-layout>
          <patientModal ref="_patient"></patientModal>
          <procedureModal ref="_procedure" @hide="closeProcedureModal"></procedureModal>
          <showImageModal ref="_showImage" @hide="closeImageModal"></showImageModal>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import patientModal from "../settings/modals/mPatient.vue";
import procedureModal from "./modals/mProcedure.vue";
import showImageModal from "./modals/mShowImage.vue";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  components: { patientModal, procedureModal, showImageModal },
  data() {
    return {
      form: {},
      doctor_name: null,
      patientList: [],
      medicalProcedures: [],
      listOfImages: [],
      pathPatientList: "getPatientsAndDoctors",
      urlToUploadImages: "/api/uploadFile/",
      urlToUploadAvatar: "/api/uploadAvatar/",
      model: "images",
      medicalProcedureId: null,
      avatarUrl: null
    };
  },
  created() {
    this.fetchData(this.pathPatientList);
    this.avatarUrl = this.$store.getters["auth/user"].avatar;
  },
  computed: {
    _medicalProcedureId() {
      return this.urlToUploadImages + this.medicalProcedureId;
    },
    checkIfExistProcedure() {
      if (!this.medicalProcedureId) {
        return true;
      } else {
        return false;
      }
    },
    user_name() {
      return (
        this.$store.getters["auth/user"].name +
        " " +
        this.$store.getters["auth/user"].last_name
      );
    }
  },
  filters: {
    capitalize: function(value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    }
  },
  methods: {
    uloadedFinished(val) {
      this.getListOfImages(this.medicalProcedureId);
      this.fetchData(this.pathDashboardData);
    },
    closeProcedureModal() {
      this.getProcedures();
    },
    closeImageModal(val) {
      var vm = this;
      vm.loading = true;
      axios
        .put(`/api/${vm.model}/${val.id}`, val)
        .then(function(response) {
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },
    showProcedureModal(refs) {
      if (this.form.medicalProcedure) {
        this.openModal(refs._procedure, "view", this.form.medicalProcedure);
      }
    },
    CreateProcedureModal(refs) {
      this.openModal(refs._procedure, "create", this.form.patient);
    },
    showPatientModal(refs) {
      if (this.form.patient) {
        this.openModal(refs._patient, "view", this.form.patient);
      }
    },
    showImageModal(refs, attributes) {
      refs._showImage.open(attributes);
    },
    openModal(modal, processType, itemId) {
      modal.open(processType, itemId);
    },
    getProcedures(val) {
      let vm = this;

      const _patient = vm.patientList.find(data => data.value === val);
      if (_patient) {
        vm.doctor_name = _patient.doctor_name;
        console.log(
          "getProceduresByPatientAndDoctor/" + val + "/" + _patient.doctor_id
        );
        vm.fetchData(
          "getProceduresByPatientAndDoctor/" + val + "/" + _patient.doctor_id
        );
      } else {
        vm.doctor_name = null;
      }

      //console.log(vm.doctor_name);
      /*vm.medicalProcedureId = null;
      vm.$set(vm, "medicalProcedures", []);
      vm.$set(vm.form, "medicalProcedure", null);
      vm.$refs._procedureSelect.options = [];
      vm.$refs._procedureSelect.model = null;*/
    },
    getListOfImages(val) {
      let vm = this;
      vm.medicalProcedureId = val;
      if (val) {
        vm.fetchData("getImagesByProcedure/" + val);
      }
      vm.loading = false;
    },
    async fetchData(path) {
      let vm = this;
      vm.loading = true;
      axios
        .get(`/api/${path}`)
        .then(function(response) {
          if (response.data.patients) {
            vm.$set(vm, "patientList", response.data.patients);
          }
          if (response.data.procedures) {
            vm.$set(vm, "medicalProcedures", response.data.procedures);
          }

          if (response.data.images) {
            vm.$set(vm, "listOfImages", response.data.images);
          }

          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },
    changeAvatar(e) {
      let formData = new FormData();
      formData.append("avatar", e.target.files[0], e.target.files[0].name);
      axios
        .post(
          this.urlToUploadAvatar + this.$store.getters["auth/user"].id,
          formData
        )
        .then(res => {
          this.avatarUrl = res.data.avatar;
        })
        .catch(error => {
          this.avatarUrl = this.$store.getters["auth/user"].avatar;
        });
    },
    showInputFile(e) {
      // this.$refs[`myRow${index}`]
      console.log(this.$refs);
      this.$refs[`imageInput${e.target.dataset.index}`].click();
    },
    changeImage(e) {
      let formData = new FormData();
      formData.append("image", e.target.files[0], e.target.files[0].name);
      formData.append("_method", "PUT");
      axios
        .post("/api/images/" + e.target.dataset.image, formData)
        .then(res => {
          this.getListOfImages(this.medicalProcedureId);
        })
        .catch(error => {
          console.log(error);
        });
    },
    deleteImage(value, index) {
      let vm = this;
      vm.$q
        .dialog({
          title: "Tenga Cuidado!",
          message: "Estás seguro de eliminar la imagen seleccionada?",
          ok: "SI, Eliminar!",
          cancel: "NO, Cancelar",
          color: "secondary"
        })
        .onOk(() => {
          vm.submitDeleteImage(value.id, index);
        })
        .onCancel(() => {});
    },
    submitDeleteImage(id, index) {
      let vm = this;
      vm.loading = true;
      axios
        .delete(`api/images/${id}`)
        .then(function(response) {
          if (response.data.deleted) {
            kNotify(vm, "Imagen eliminada..", "positive");
            vm.listOfImages.splice(index, 1);
            vm.loading = false;
          }
        })
        .catch(function(error) {
          vm.getListOfImages(vm.medicalProcedureId);
          vm.loading = false;
          kNotify(
            vm,
            "No fue posible eliminar la imagen, intente de nuevo",
            "negative"
          );
        });
    }
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 

.my-cardImages
  width: 100%
  max-width: 250px

.transition-item
  height: 250px
  width: 250px

.my-card 
  width: 100%
  max-width: 100%
  min-width: 300px


.row_class 
  padding: 7px 10px
  margin-top: 1rem

</style>
