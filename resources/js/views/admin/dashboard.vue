<template>
  <q-page padding>
    <div class="q-pa-md items-start q-gutter-md">
      <div class="row">
        <div class="col-md-grow col-xs-grow col-sm-grow row_class">
          <kCard
            class="my-card"
            title="IMAGENES CARGADAS MES ULTIMO"
            icon-name
            :total="images_generated_lastMonth"
          />
        </div>
        <div class="col-md-grow col-xs-grow col-sm-grow row_class">
          <kCard
            class="my-card"
            title="IMAGENES CARGADAS EL ULTIMO AÑO"
            icon-name
            :total="images_generated_lastYear"
          />
        </div>
      </div>
      <div class="row">
        <div class="col-md-8 col-sm-12 col-xs-12">
          <div class="col-md-grow col-xs-grow col-sm-grow items-start row_class">
            <q-card bordered flat class="my-card">
              <q-card-section>
                <q-toolbar>
                  <q-btn flat dense size="16px" round color="primary" icon="filter_list">
                    <q-menu fit transition-show="scale" transition-hide="scale">
                      <q-list style="min-width: 100px">
                        <q-item clickable v-close-popup @click="filterPeriod('d')">
                          <q-item-section>HOY</q-item-section>
                        </q-item>
                        <q-separator />
                        <q-item clickable v-close-popup @click="filterPeriod('w')">
                          <q-item-section>ULTIMA SEMANA</q-item-section>
                        </q-item>
                        <q-separator />
                        <q-item clickable v-close-popup @click="filterPeriod('y')">
                          <q-item-section>ULTIMO AÑO</q-item-section>
                        </q-item>
                      </q-list>
                    </q-menu>
                  </q-btn>
                  <q-toolbar-title class="text-caption text-grey">
                    TOP DE RADIOGRAFIAS ASIGNADAS A DOCTORES
                    <span
                      class="text-weight-bold text-primary"
                    >{{filterBylabel}}</span>
                  </q-toolbar-title>
                </q-toolbar>

                <dashboardChart :chart-data="datacollection" :options="barOptions" />
              </q-card-section>
            </q-card>
          </div>
        </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="col-md-grow col-xs-grow col-sm-grow row_class">
            <q-card bordered flat class="my-card">
              <q-card-section class="q-pt-xs">
                <div class="text-h5 q-mt-sm q-mb-xs">Puntos redimidos</div>
                <div class="text-caption text-grey">Top 5 de doctores que han redimido puntos</div>
              </q-card-section>
              <q-card-section>
                <dashboardPie :chart-data="PieChartdata" :options="optionsPie" />
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-grow col-xs-grow col-sm-grow row_class">
          <q-table
            class="my-card"
            title="Movimientos realizados por tus doctores"
            dense
            flat
            bordered
            :data="data_doctorMovements"
            :columns="columns_doctorMovements"
            row-key="name"
            virtual-scroll
            :pagination.sync="pagination"
            :rows-per-page-options="[0]"
          >
            <template v-slot:top-right="props">
              <q-input dense debounce="300" v-model="filter_doctorMovements" placeholder="Buscar">
                <template v-slot:append>
                  <q-icon name="search"></q-icon>
                </template>
              </q-input>
              <q-space></q-space>
              <q-btn
                flat
                round
                dense
                :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                class="q-ml-md"
              >
                <q-tooltip>Ver en pantalla completa</q-tooltip>
              </q-btn>
            </template>
          </q-table>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import dashboardChart from "../../components/chart/Bar.js";
import dashboardPie from "../../components/chart/Pie.js";
import store from "../../store";

export default {
  middleware: "auth",
  components: {
    dashboardChart,
    dashboardPie
  },
  data() {
    return {
      pagination: {
        rowsPerPage: 10
      },
      PieChartdata: {
        labels: null,
        datasets: [
          {
            backgroundColor: [],
            data: null
          }
        ]
      },
      optionsPie: {
        responsive: true,
        maintainAspectRatio: false
      },
      columns_doctorMovements: [],
      data_doctorMovements: [],
      filter_doctorMovements: [],
      filterBylabel: "",
      model: 30,
      min: 0,
      max: 50,
      visible: false,
      withouthMovementsMsg: "Aún no tienes movimientos creados.",
      isProcessing: false,
      path: "getDashboardInfo",
      form: {},
      datacollection: null,
      datacollection_data: [],
      datacollection_labels: [],
      YearLabels: [],
      WeekLabels: [],
      barOptions: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true
              },
              stacked: true
            }
          ],
          xAxes: [
            {
              stacked: true
            }
          ]
        }
      },
      backgroundColors: [],
      images_generated_lastMonth: 0,
      images_generated_lastYear: 0,
      images_ByDoctor_today_qty: [],
      images_ByDoctor_today_labels: [],
      lastSevenDaysData: []
    };
  },
  metaInfo() {
    return { title: this.$t("home") };
  },
  created() {
    this.columns_redeemedPoints = columns_redeemedPoints();
    this.columns_doctorMovements = columns_doctorMovements();
    this.fetchData();
    //console.log('store', this.$store)
  },
  computed: {
    filter() {
      return this.$store.getters["admin/filter"];
    }
  },
  methods: {
    fillOptions() {
      let vm = this;

      vm.datacollection = {
        labels: vm.datacollection_labels,
        datasets: vm.datacollection_data
      };
    },
    filterPeriod(val) {
      if (val) {
        this.$store.dispatch("admin/setGraphFilter", val)
      }

      if (this.filter == "d") {
        this.filterBylabel = "HOY";
        this.datacollection_labels = this.images_ByDoctor_today_labels;
        this.datacollection_data = [
          {
            label: "",
            backgroundColor: this.backgroundColors,
            data: this.images_ByDoctor_today_qty
          }
        ];
      } else if (this.filter == "w") {
        this.filterBylabel = `ULTIMA SEMANA`;
        this.datacollection_labels = this.WeekLabels;
        this.datacollection_data = this.lastSevenDaysData;
      } else if (this.filter == "y") {
        this.filterBylabel = "ULTIMO AÑO";
        this.datacollection_labels = this.YearLabels;
      }
      this.fillOptions();
    },
    fetchData() {
      let vm = this;
      vm.isProcessing = true;
      axios
        .get(`/api/${vm.path}`)
        .then(function(response) {
          vm.$set(
            vm.$data,
            "images_generated_lastMonth",
            response.data.images_sum.images_lastMonth
          );
          vm.$set(
            vm.$data,
            "images_generated_lastYear",
            response.data.images_sum.images_lastYear
          );

          vm.$set(
            vm.$data,
            "images_ByDoctor_today_qty",
            response.data.images_ByDoctor.today_ImagesByDoctor_qty
          );

          vm.$set(
            vm.$data,
            "images_ByDoctor_today_labels",
            response.data.images_ByDoctor.today_ImagesByDoctor_labels
          );

          vm.$set(
            vm.$data,
            "data_doctorMovements",
            response.data.tracking_Doctors
          );

          vm.$set(
            vm.$data,
            "lastSevenDaysData",
            response.data.weekly_doctorsData
          );

          vm.$set(
            vm.$data,
            "WeekLabels",
            response.data.images_ByDoctor.week_ImagesByDoctor_labels
          );

          vm.$set(vm.$data, "backgroundColors", response.data.backgroundColors);

          vm.$set(
            vm.$data.PieChartdata.datasets[0],
            "backgroundColor",
            response.data.backgroundColors
          );

          vm.PieChartdata.labels = response.data.topRedemedPoints.labels;
          vm.PieChartdata.datasets[0].data =
            response.data.topRedemedPoints.data;

          vm.filterPeriod();
          vm.isProcessing = false;
        })
        .catch(function(error) {
          vm.isProcessing = false;
        });
    }
  }
};

function columns_doctorMovements() {
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
      label: "Descripción del movimiento",
      format: val => `${val}`,
      align: "left",
      field: "detail",
      name: "detail",
      sortable: true,
      filter: true
    },
    {
      label: "Fecha",
      field: "created_at",
      name: "created_at",
      sortable: true,
      filter: true,
      type: "string"
    }
  ];
}

function columns_redeemedPoints() {
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
      label: "Doctor",
      field: "name",
      name: "name",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Puntos Redimidos",
      field: "last_name",
      name: "last_name",
      sortable: true,
      filter: true
    },
    {
      label: "Fecha",
      field: "email",
      name: "email",
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
<style lang="stylus">
.my-card {
  width: 100%;
  max-width: 100%;
  min-width: 300px;
}

.row_class {
  padding: 10px 15px;
  margin-top: 1rem;
}
</style>