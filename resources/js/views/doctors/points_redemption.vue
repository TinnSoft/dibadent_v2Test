<template>
  <div class="q-pa-md q-pa-xs q-pa-sm items-start q-gutter-md">
    <div class="row">
      <div class="col-md-grow col-xs-grow col-sm-grow row_class rounded-borders">
        <q-card flat>
          <q-card-section class="q-pt-xs">
            <div class="text-overline">RESUMEN DE TUS PUNTOS</div>
          </q-card-section>

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
                  <q-icon color="green" name="show_chart" />
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-h6 text-green">{{pointsSummary.acumulatedPoints}}</q-item-label>
                  <q-item-label caption>Puntos Disponibles</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>
                  <q-icon color="blue-grey-2" name="date_range" />
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-h6 text-orange">{{pointsSummary.pointsNextToBeat}}</q-item-label>
                  <q-item-label caption>Puntos próximos a vencer</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>
                  <q-icon color="blue-grey-2" name="bookmark_border" />
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-h6 text-blue-grey">{{pointsSummary.level_next}}</q-item-label>
                  <q-item-label caption>Siguiente Nivel</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>
                  <q-icon color="blue-grey-2" name="show_chart" />
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-h6 text-blue-grey">{{pointsSummary.level_next_points}}</q-item-label>
                  <q-item-label caption>Puntos para el siguiente nivel</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>
    <div class="row">
      <div class="col-md-grow col-xs-grow col-sm-grow row_class rounded-borders">
        <q-card flat>
          <q-card-section class="q-pt-xs">
            <div class="text-overline">REDIME TUS PUNTOS AQUÍ</div>
          </q-card-section>

          <q-card-section class="bg-grey-1 text-blue">
            <q-table
              :data="productsDB"
              :columns="columns"
              row-key="name"
              binary-state-sort
              :loading="loading"
              dense
              :grid="$q.screen.xs"
            >
              <template v-slot:body="props">
                <q-tr :props="props">
                  <q-td key="description" :props="props">
                    {{ props.row.description }}                   
                  </q-td>
                  <q-td key="required_points" :props="props">
                    {{ props.row.required_points }}                   
                  </q-td>

                  <q-td key="actions" :props="props">
                    <q-btn
                      size="sm"
                      color="blue"
                      round
                      dense
                      @click="redempt(props.row)"
                      icon="redeem"
                    />
                  </q-td>
                </q-tr>
              </template>
            </q-table>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>
<script>
import kButton from "../../components/tables/cButton.vue";
import kNotify from "../../components/messages/Notify.js";

export default {
  middleware: "auth",
  components: {
    kButton,
    kNotify
  },
  data() {
    return {
      columns: [
        {
          name: "description",
          required: true,
          label: "PRODUCTO",
          align: "left",
          field: "description",
          sortable: true
        },
        {
          name: "required_points",
          align: "center",
          label: "PUNTOS REQUERIDOS",
          field: "required_points",
          sortable: true
        },
        {
          label: "ACCIONES",
          field: "actions",
          name: "actions",
          type: "string"
        }
      ],
      productsDB: [],
      pointsLevelPath: "getPointsLevelslist",
      loading: false,
      pathDashboardData: "getDoctorDashboardData",
      pointsSummary: {
        level: "",
        level_next: "",
        level_next_points: "",
        acumulatedPoints: "",
        redeemedPoints: "",
        pointsNextToBeat: ""
      }
    };
  },
  created() {
    this.fetchData(this.pathDashboardData);
  },
  methods: {
    async fetchData(path) {
      let vm = this;
      vm.loading = true;
      axios
        .get(`/api/${path}`)
        .then(function(response) {
          console.log(response.data);
          if (response.data.pointsSummary) {
            vm.$set(vm, "pointsSummary", response.data.pointsSummary);
          }
          if (response.data.products) {
            vm.$set(vm, "productsDB", response.data.products);
          }
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },

    redempt() {
      let vm = this;
      // vm.loading = true;

      /*axios
        .post(`/api/points_levels`)
        .then(function(response) {
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
      vm.fetchData();*/
    }
  }
};
</script>
<style lang="sass">
.my-card 
  width: 100%
  max-width: 100%
  min-width: 300px

.row_class 
  padding: 7px 10px
  margin-top: 1rem

</style>
