<template>
  <q-page padding>
    <div class="q-pa-md q-gutter-md">
      <div class="row">
        <div class="col-md-grow">
          <kCard
            class="my-card"
            title="Procedimientos han sido generados el último mes"
            background-color="primary"
            icon-name
            :total="procedures_generated_lastMonth"
          />
        </div>
        <br />
        <div class="col-md-grow">
          <kCard
            class="my-card"
            title="Procedimientos han sido generados el último año"
            background-color="orange"
            icon-name
            :total="procedures_generated_lastYear"
          />
        </div>
      </div>
      <br />
      <div class="row">
        <div class="col-md-grow">
          <q-card class="my-card">
            <q-card-section>
              <q-toolbar class="bg-grey-1">
                <q-btn flat dense size="16px" round color="primary" icon="filter_list">
                  <q-menu fit transition-show="scale" transition-hide="scale">
                    <q-list style="min-width: 100px">
                      <q-item clickable v-close-popup @click="filterPeriod('d')">
                        <q-item-section>Hoy</q-item-section>
                      </q-item>
                      <q-separator />
                      <q-item clickable v-close-popup @click="filterPeriod('w')">
                        <q-item-section>Última Semana</q-item-section>
                      </q-item>
                      <q-separator />
                      <q-item clickable v-close-popup @click="filterPeriod('m')">
                        <q-item-section>Último Mes</q-item-section>
                      </q-item>
                      <q-separator />
                      <q-item clickable v-close-popup @click="filterPeriod('y')">
                        <q-item-section>Último Año</q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
                <q-toolbar-title>Top de Médicos que más generaron procedimientos</q-toolbar-title>
              </q-toolbar>

              <dashboardChart
                :chart-data="datacollection"
                :options="barOptions"
                :data-original="procedures_bydoctor_today_qty"
              />
            </q-card-section>
          </q-card>
        </div>
      </div>
      <br />
      <div class="row">
        <div class="col-md-grow">
          <q-table
            class="my-card"
            title="Top de Puntos redimidos"
            dense
            :data="data_redeemedPoints"
            :columns="columns_redeemedPoints"
            row-key="name"
          >
            <template v-slot:top-right="props">
              <q-input dense debounce="300" v-model="filter_redeemedPoints" placeholder="Buscar">
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
        <div class="col-md-grow">
          <q-table
            class="my-card"
            title="Movimientos realizados por tus doctores"
            dense
            :data="data_doctorMovements"
            :columns="columns_doctorMovements"
            row-key="name"
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

export default {
  middleware: "auth",
  components: {
    dashboardChart
  },
  data() {
    return {
      columns_redeemedPoints: [],
      data_redeemedPoints: [],
      filter_redeemedPoints: [],
      columns_doctorMovements: [],
      data_doctorMovements: [],
      filter_doctorMovements: [],
      filterBylabel: "Hoy",
      model: 30,
      min: 0,
      max: 50,
      visible: false,
      withouthMovementsMsg: "Aún no tienes movimientos creados.",
      isProcessing: false,
      filter: "d",
      path: "getDashboardInfo",
      form: {},
      datacollection: null,
      datacollection_data: [],
      datacollection_labels: [],
      YearLabels: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
      ],
      WeekLabels: [
        "Lunes",
        "Martes",
        "Miercoles",
        "Jueves",
        "Viernes",
        "Sabado",
        "Domingo"
      ],
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
      procedures_generated_lastMonth: 0,
      procedures_generated_lastYear: 0,
      procedures_bydoctor_today_qty: [],
      procedures_bydoctor_today_labels: []
    };
  },
  metaInfo() {
    return { title: this.$t("home") };
  },
  created() {
    this.filter = "d";
    this.columns_redeemedPoints = columns_redeemedPoints();
    this.columns_doctorMovements = columns_doctorMovements();
    this.fetchData();
  },
  computed: {},
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
        this.filter = val;
      }

      if (this.filter == "d") {
        this.filterBylabel = "Hoy";
        this.datacollection_labels = this.procedures_bydoctor_today_labels[0];
        this.datacollection_data = [
          {
            label: "",
            backgroundColor: "#2870E8",
            data: this.procedures_bydoctor_today_qty[0]
          }
        ];
      } else if (this.filter == "w") {
        this.filterBylabel = "Ultima Semana";
        this.datacollection_labels = this.WeekLabels;

        this.datacollection_data = [
          {
            label: "Doctor 1",
            backgroundColor: "#f87979",
            data: [40, 39, 10, 40, 39, 80, 40]
          },
          {
            label: "Doctor Two",
            backgroundColor: "#3D5B96",
            data: [40, 39, 10, 40, 39, 80, 40]
          },
          {
            label: "Doctor Three",
            backgroundColor: "#1EFFFF",
            data: [20, 10, 12, 33, 22, 4, 0]
          },
          {
            label: "Doctor 4",
            backgroundColor: "#1EFFFF",
            data: [20, 10, 12, 33, 22, 4, 0]
          },
          {
            label: "Doctor 5",
            backgroundColor: "#1EFFFF",
            data: [20, 10, 12, 33, 22, 4, 0]
          },
          {
            label: "Doctor 6",
            backgroundColor: "#1EFFFF",
            data: [20, 10, 12, 33, 22, 4, 0]
          },
          {
            label: "Doctor 7",
            backgroundColor: "#1EFFFF",
            data: [20, 10, 12, 33, 22, 4, 0]
          },
          {
            label: "Otros",
            backgroundColor: "gray",
            data: [20, 10, 12, 33, 22, 4, 0]
          }
        ];
      } else if (this.filter == "m") {
        this.filterBylabel = "Ultimo Mes";
        //this.datacollection_labels = baseData.labels_current_month;
      } else if (this.filter == "y") {
        this.filterBylabel = "Ultimo Año";
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
            "procedures_generated_lastMonth",
            response.data.procedures_sum.procedures_lastMonth
          );
          vm.$set(
            vm.$data,
            "procedures_generated_lastYear",
            response.data.procedures_sum.procedures_lastYear
          );

          vm.$set(
            vm.$data,
            "procedures_bydoctor_today_qty",
            response.data.procedures_ByDoctor.procedures_bydoctor_today_qty
          );

          vm.$set(
            vm.$data,
            "procedures_bydoctor_today_labels",
            response.data.procedures_ByDoctor.procedures_bydoctor_today_labels
          );

          vm.$set(
            vm.$data,
            "data_doctorMovements",
            response.data.tracking_Doctors
          );

          console.log(response.data);

          vm.filterPeriod();
          vm.isProcessing = false;
          /*
          if (
            vm.qty_of_radiography_generated_lastMonth === 0 &&
            vm.qty_of_radiography_generated_lastYear === 0
          ) {
            vm.visible = true;
          }
          */
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
    },
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
  max-width: 95%;
}
</style>