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

          <q-tabs
            v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator
          >
            <q-tab name="redemTab" icon="star_border"></q-tab>
            <q-tab name="historyTab" icon="history"></q-tab>
          </q-tabs>

          <q-separator></q-separator>

          <q-tab-panels v-model="tab" animated>
            <q-tab-panel name="redemTab">
              <q-table
                :data="productsDB"
                :columns="columns"
                row-key="name"
                binary-state-sort
                :loading="loading"
                dense
              >
                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td key="description" :props="props">{{ props.row.description }}</q-td>
                    <q-td key="required_points" :props="props">{{ props.row.required_points }}</q-td>

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
            </q-tab-panel>

            <q-tab-panel name="historyTab">
              <q-table
                :data="pointsSummary.historyPointsRedemed"
                :columns="columnsHistoryPointsRedemed"
                row-key="code"
                binary-state-sort
                :loading="loading"
                dense
                :grid="$q.screen.xs"
              >
                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td key="description" :props="props">{{ props.row.description }}</q-td>
                    <q-td key="points_redeemed" :props="props">{{ props.row.points_redeemed }}</q-td>
                    <q-td key="code" :props="props">{{ props.row.code }}</q-td>
                    <q-td key="created_at" :props="props">{{ props.row.created_at }}</q-td>
                  </q-tr>
                </template>
              </q-table>
            </q-tab-panel>
          </q-tab-panels>
        </q-card>
      </div>
    </div>
    <q-dialog v-model="showRedemedPointConfirmationSuccess" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-blue">
            <q-icon size="xl" name="tag_faces"></q-icon>
            <div class="text-h6">Felicidades</div>
          </div>
        </q-card-section>
        <q-card-section>
          <span class="q-ml-sm">
            Se ha generado el codigo de canje
            <span class="text-weight-bold">{{redemptionCode}}</span>,
            el cual podrás hacer efectivo con el radiólogo.
          </span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="OK" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
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
      tab: "redemTab",
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
      columnsHistoryPointsRedemed: [
        {
          name: "description",
          required: true,
          label: "PRODUCTO",
          align: "left",
          field: "description",
          sortable: true
        },
        {
          name: "points_redeemed",
          align: "center",
          label: "PUNTOS REDIMIDOS",
          field: "points_redeemed",
          sortable: true
        },
        {
          name: "code",
          align: "left",
          label: "CODIGO",
          field: "code",
          sortable: true
        },
        {
          name: "created_at",
          align: "left",
          label: "FECHA",
          field: "created_at",
          sortable: true
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
        pointsNextToBeat: "",
        historyPointsRedemed: []
      },
      redemptionCode: null,
      showRedemedPointConfirmationSuccess: false
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
    redempt(value) {
      let vm = this;

      if (value.required_points > vm.pointsSummary.acumulatedPoints) {
        vm.alert_noEnoughPointstoDeal();
      }

      if (value.required_points < vm.pointsSummary.acumulatedPoints) {
        vm.alert_redemptPointsConfirmation(value);
      }
    },
    alert_noEnoughPointstoDeal() {
      let vm = this;
      vm.$q
        .dialog({
          title: "Opps, lo lamentamos",
          message:
            "No cuentas con suficientes puntos para realizar el canje, pero no te desanimes, continua cargando más radiografías y pronto lograrás tu meta."
        })
        .onOk(() => {})
        .onCancel(() => {})
        .onDismiss(() => {});
    },
    alert_redemptPointsConfirmation(value) {
      let vm = this;
      vm.$q
        .dialog({
          title: "Confirmar canje de puntos!",
          message:
            "Vas a redimir " +
            value.required_points +
            " puntos, desea continuar?",
          ok: "SI, Estoy seguro!",
          cancel: "NO, Cancelar"
        })
        .onOk(() => {
          axios.post("/api/redemptPoint/" + value.id).then(function(response) {
            if (response.data.created) {
              if (response.data.redemptionCode) {
                vm.$set(vm, "redemptionCode", response.data.redemptionCode);
                vm.$set(vm, "showRedemedPointConfirmationSuccess", true);
              }
              kNotify(vm, "Puntos redimidos satisfactoriamente", "positive");
              vm.fetchData(vm.pathDashboardData);
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
