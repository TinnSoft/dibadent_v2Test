<template>
  <!-- <q-avatar size="100px" font-size="52px" color="grey-4" text-color="white" icon="person_outline" style="max-width: 300px" />-->
  <div class="q-pa-md row items-start q-gutter-md">
    <div class="col" style="max-width: 300px">
      <q-card class="my-card">
        <q-img src="https://image.flaticon.com/icons/png/128/149/149071.png" basic>
          <div class="absolute-bottom text-subtitle2 text-center">
            <q-btn flat color="white" label="Cambiar Imagen" />
          </div>
        </q-img>

        <q-list>
          <q-item clickable>
            <q-item-section>
              <div class="text-h6 text-blue">{{user_name | capitalize}}</div>
            </q-item-section>
          </q-item>
          <q-item clickable>
            <q-item-section avatar>
              <q-icon color="primary" name="bookmark_border" />
            </q-item-section>

            <q-item-section>
              <q-item-label>{{pointsSummary.level}}</q-item-label>
              <q-item-label caption>Tu nivel de puntos Actual</q-item-label>
            </q-item-section>
          </q-item>

          <q-item clickable>
            <q-item-section avatar>
              <q-icon color="primary" name="show_chart" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{pointsSummary.acumulatedPoints}}</q-item-label>
              <q-item-label caption>Puntos Acumulados</q-item-label>
            </q-item-section>
          </q-item>
          <q-item clickable>
            <q-item-section avatar>
              <q-icon color="primary" name="insert_emoticon" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{pointsSummary.redeemedPoints}}</q-item-label>
              <q-item-label caption>Puntos Redimidos ultimo año</q-item-label>
            </q-item-section>
          </q-item>

          <q-item clickable>
            <q-item-section avatar>
              <q-icon color="primary" name="info_outline" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{pointsSummary.pointsNextToBeat}}</q-item-label>
              <q-item-label caption>Puntos próximos a vencer</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </div>
    <div class="col-md-grow">
      <q-card class="my-card">
        <q-card-section>
          <q-timeline color="primary">
            <q-timeline-entry heading body="Cargar Imagenes" />

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
                style="width: 300px"
                @input="getProcedures(form.patient)"
              />
              <q-btn round dense flat icon="info_outline" color="grey-5" />
            </q-timeline-entry>
            <q-timeline-entry subtitle="Procedimiento" icon="device_hub">
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
                label="Seleccione un Procedimiento"
                options-dense
                hide-bottom-space
                style="width: 300px"
                @input="getListOfImages(form.medicalProcedure)"
              />
              <q-btn round dense flat icon="add" color="grey-5" />
              <q-btn round dense flat icon="info_outline" color="grey-5" />
            </q-timeline-entry>

            <q-timeline-entry subtitle="Comentarios" icon="insert_comment">
              <div style="max-width: 300px">
                <q-input v-model="comments" filled autogrow type="text" />
              </div>
            </q-timeline-entry>

            <q-timeline-entry subtitle="Imagenes" icon="photo">
              <q-tabs
                v-model="tabSelected"
                dense
                class="text-grey"
                active-color="primary"
                indicator-color="primary"
                align="justify"
                narrow-indicator
              >
                <q-tab name="loadimage" label="Cargar" />
                <q-tab name="images" label="Imagenes asociadas" />
              </q-tabs>

              <q-separator />

              <q-tab-panels v-model="tabSelected" animated>
                <q-tab-panel name="loadimage">
                  <q-uploader
                    label="Cargar Imagen"
                    dense
                    flat
                    multiple
                    :readonly="checkIfExistProcedure"
                    auto-upload
                    :url="_medicalProcedureId"
                    accept=".jpg, image/*"
                    color="grey-5"
                    style="max-width: 300px"
                    @failed="showerror"
                  />

                </q-tab-panel>

                <q-tab-panel name="images">
                  <div class="text-h6">Imagenes asociadas al paciente</div>

                  <q-carousel swipeable animated v-model="slide" thumbnails infinite>
                    <q-carousel-slide
                      :name="1"
                      img-src="../../../../public/storage/_Images/_radiology/KodwZFqueBu6NC4IMcfmIjY4Pnjk4IWt30j7fhxz.jpeg"
                    />
                    <q-carousel-slide :name="2" img-src="https://cdn.quasar.dev/img/parallax1.jpg" />
                    <q-carousel-slide :name="3" img-src="https://cdn.quasar.dev/img/parallax2.jpg" />
                    <q-carousel-slide :name="4" img-src="https://cdn.quasar.dev/img/quasar.jpg" />
                  </q-carousel>
                </q-tab-panel>
              </q-tab-panels>
            </q-timeline-entry>
          </q-timeline>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script>
import store from "../../store";
export default {
  middleware: "auth",
  components: {},
  data() {
    return {
      basePath: "D:/proyectos/Radiology",
      slide: 1,
      tabSelected: "loadimage",
      form: {},
      pointsSummary: {
        level: "",
        acumulatedPoints: "",
        redeemedPoints: "",
        pointsNextToBeat: ""
      },
      patientList: [],
      medicalProcedures: [],
      defaultTab: "levels",
      splitterModel: 20,
      comments: "",
      listOfImages: [],
      pathDashboardData: "getDoctorDashboardData",
      urlToUploadImages: "/api/uploadFile/",
      medicalProcedureId: null
    };
  },
  created() {
    this.fetchData(this.pathDashboardData);
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
    showerror(err) {
      console.log(err);
    },
    getProcedures(val) {
      let vm = this;
      vm.medicalProcedureId = null;
      vm.$set(vm, "medicalProcedures", []);
      vm.$set(vm.form, "medicalProcedure", null);
      vm.$refs._procedureSelect.options = [];
      vm.$refs._procedureSelect.model = null;

      if (val) {
        vm.fetchData("getProceduresByPatientAndDoctor/" + val);
      }
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
          if (response.data.patientList) {
            vm.$set(vm, "patientList", response.data.patientList);
          }
          if (response.data.pointsSummary) {
            vm.$set(vm, "pointsSummary", response.data.pointsSummary);
          }
          if (response.data.procedures) {
            vm.$set(vm, "medicalProcedures", response.data.procedures);
          }
          if (response.data.images) {
            vm.$set(vm, "listOfImages", response.data.images);
            console.log(response.data.images);
          }

          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    }
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 

.my-card 
  width: 100%
  max-width: 95%
</style>
