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
              <q-item-label caption>Bienvenido Doctor</q-item-label>
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
                    <q-item-label class="text-h6">{{pointsSummary.level}}</q-item-label>
                    <q-item-label caption>Tu nivel de puntos Actual</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section avatar>
                    <q-icon color="primary" name="show_chart" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{pointsSummary.acumulatedPoints}}</q-item-label>
                    <q-item-label caption>Puntos Acumulados</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-grow col-xs-grow col-sm-grow row_class">
          <q-card flat>
            <q-card-section class="bg-grey-1 text-blue">
              <q-list>
                <q-item>
                  <q-item-section avatar>
                    <q-icon color="primary" name="insert_emoticon" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{pointsSummary.redeemedPoints}}</q-item-label>
                    <q-item-label caption>Puntos Redimidos ultimo año</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section avatar>
                    <q-icon color="primary" name="info_outline" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{pointsSummary.pointsNextToBeat}}</q-item-label>
                    <q-item-label caption>Puntos próximos a vencer</q-item-label>
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
                      @input="getListOfImages(form.patient)"
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
                :readonly="checkIfExistPatient"
                auto-upload
                :url="_patientID"
                accept=".jpg, image/*"
                color="primary"
                multiple
                batch
                @uploaded="uloadedFinished"
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
                    style="width: 180px"
                    ratio="1"
                    spinner-color="white"
                    class="rounded-borders"
                  >
                    <div class="absolute-bottom text-center text-body2">
                      <q-btn
                        flat
                        round
                        color="white"
                        @click="showImageModal($refs, image)"
                        icon="zoom_in"
                      ></q-btn>
                      <q-btn
                        flat
                        round
                        color="white"
                        icon="insert_comment"
                        @click="showCommentsModal($refs, image)"
                      ></q-btn>
                      <q-btn flat round color="white" icon="delete" @click="deleteImage(image,i)"></q-btn>
                    </div>
                    <template v-slot:error>
                      <div
                        class="absolute-full flex flex-center bg-negative text-white"
                      >Error de carga</div>
                    </template>
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
          <showImageModal ref="_showImage"></showImageModal>
          <showCommentsModal ref="_showComments"></showCommentsModal>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import store from "../../store";
import patientModal from "../settings/modals/mPatient.vue";
import showImageModal from "./modals/mShowImage.vue";
import kNotify from "../../components/messages/Notify.js";
import showCommentsModal from "../images/mComments.vue";

export default {
  middleware: "auth",
  components: { patientModal, showImageModal, showCommentsModal },
  data() {
    return {
      form: {},
      pointsSummary: {
        level: "",
        acumulatedPoints: "",
        redeemedPoints: "",
        pointsNextToBeat: ""
      },
      patientList: [],
      listOfImages: [],
      pathDashboardData: "getDoctorDashboardData",
      urlToUploadImages: "/api/uploadFile/",
      urlToUploadAvatar: "/api/uploadAvatar/",
      patientID: null,
      avatarUrl: null,
      loading: false
    };
  },
  created() {
    this.fetchData(this.pathDashboardData);
    this.avatarUrl = this.$store.getters["auth/user"].avatar;
  },
  computed: {
    _patientID() {
      return this.urlToUploadImages + this.patientID;
    },
    checkIfExistPatient() {
      if (!this.patientID) {
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
    showCommentsModal(refs, attributes) {
      refs._showComments.open(attributes);
    },
    uloadedFinished(val) {
      this.getListOfImages(this.patientID);
      this.fetchData(this.pathDashboardData);
    },

    showImageModal(refs, attributes) {
      refs._showImage.open(attributes);
    },
    showPatientModal(refs) {
      if (this.form.patient) {
        this.openModal(refs._patient, "view", this.form.patient);
      }
    },
    openModal(modal, processType, itemId) {
      modal.open(processType, itemId);
    },
    getListOfImages(val) {
      let vm = this;
      vm.patientID = val;
      if (val) {
        vm.fetchData("getImagesByPatient/" + val);
      }
      vm.loading = false;
    },
    async fetchData(path) {
      let vm = this;
      vm.loading = true;
      axios
        .get(`/api/${path}`)
        .then(function(response) {
          if (response.data.pointsSummary) {
            vm.$set(vm, "pointsSummary", response.data.pointsSummary);
          }
          if (response.data.patientList) {
            vm.$set(vm, "patientList", response.data.patientList);
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
      this.$refs[`imageInput${e.target.dataset.index}`].click();
    },
    changeImage(e) {
      let formData = new FormData();
      formData.append("image", e.target.files[0], e.target.files[0].name);
      formData.append("_method", "PUT");
      axios
        .post("/api/images/" + e.target.dataset.image, formData)
        .then(res => {
          this.getListOfImages(this.patientID);
        })
        .catch(error => {});
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
          vm.getListOfImages(vm.patientID);
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
