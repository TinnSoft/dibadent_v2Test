<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="row">
        <div class="col">
          <kCard
            class="my-card"
            title="Radiografías Generadas Último mes"
            background-color="primary"
            icon-name
            :total="qty_of_radiography_generated_lastMonth"
            subtitle="Total entradas"
          />
        </div>
        <div class="col">
          <kCard
            class="my-card"
            title="Radiografías Generadas Último año"
            background-color="orange"
            icon-name
            :total="qty_of_radiography_generated_lastYear"
            subtitle="Total gastos"
          />
        </div>
      </div>
      <br />
      <div class="row">
        <div class="col">
          <q-card class="my-card">
            <q-card-section>
              <div
                class="text-subtitle2"
              >Medicos que mas generaron radiografias durante el ultimo mes</div>
              <dashboardChart 
                :chart-data="datacollection"
                :options="barOptions"
                :dataOriginal="datacollection.datasets[0].data"
              />
            </q-card-section>
          </q-card>
        </div>
      </div>
      <br />
      <div class="row">
        <div class="col">
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
        <div class="col">
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
  data: function() {
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
      datacollection: {},
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
      }
    };
  },
  metaInfo() {
    return { title: this.$t("home") };
  },
  created() {
    this.columns_redeemedPoints = columns_redeemedPoints();
    this.columns_doctorMovements = columns_doctorMovements();
    this.fillOptions();
    this.fetchData();
  },
  computed: {
    qty_of_radiography_generated_lastMonth() {
      try {
        if (this.filter === "d") {
          return this.form.income.day;
        } else if (this.filter === "w") {
          return this.form.income.week;
        } else if (this.filter === "m") {
          return this.form.income.month;
        } else if (this.filter === "y") {
          return this.form.income.year;
        }
      } catch (e) {
        return 0;
      }
    },
    qty_of_radiography_generated_lastYear() {
      try {
        if (this.filter === "d") {
          return this.form.outcome.day;
        } else if (this.filter === "w") {
          return this.form.outcome.week;
        } else if (this.filter === "m") {
          return this.form.outcome.month;
        } else if (this.filter === "y") {
          return this.form.outcome.year;
        }
      } catch (e) {
        return 0;
      }
    }
  },
  methods: {
    fillOptions() {
      this.datacollection = {
        labels: this.YearLabels,
        datasets: [
        {
          label: '',
          backgroundColor: '#2870E8',
          data: [40,20,15,45,36]
        }
      ]
      };
    },
    filterPeriod(val) {
      let baseData = this.form.graph_data;
      let collection = this.datacollection;
      this.filter = val;

      if (val == "d") {
        this.filterBylabel = "Hoy";
        collection.labels = this.WeekLabels;
        collection.datasets[0].data = baseData.weekData_income;
        collection.datasets[1].data = baseData.weekData_outcome;
      } else if (val == "w") {
        this.filterBylabel = "Ultima Semana";
        collection.labels = this.WeekLabels;
        collection.datasets[0].data = baseData.weekData_income;
        collection.datasets[1].data = baseData.weekData_outcome;
      } else if (val == "m") {
        this.filterBylabel = "Ultimo Mes";
        collection.labels = baseData.labels_current_month;
        collection.datasets[0].data = baseData.data_by_day_current_month_in;
        collection.datasets[1].data = baseData.data_by_day_current_month_out;
      } else if (val == "y") {
        this.filterBylabel = "Ultimo Año";
        collection.labels = this.YearLabels;
        collection.datasets[0].data = baseData.DataBymont_peryear_in;
        collection.datasets[1].data = baseData.DataBymont_peryear_out;
      }
    },
    fetchData() {
      let vm = this;

      vm.isProcessing = true;
      axios
        .get(`/api/${vm.path}`)
        .then(function(response) {
          vm.$set(vm.$data, "form", response.data.form);
          vm.datacollection.datasets[0].data =
            vm.form.graph_data.weekData_income;
          vm.datacollection.datasets[1].data =
            vm.form.graph_data.weekData_outcome;
          vm.datacollection.labels = vm.WeekLabels;
          vm.isProcessing = false;

          if (vm.qty_of_radiography_generated_lastMonth === 0 && vm.qty_of_radiography_generated_lastYear === 0) {
            vm.visible = true;
          }
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
      label: "Doctor",
      field: "name",
      name: "name",
      sortable: true,
      filter: true,
      type: "string"
    },
    {
      label: "Descripción del movimiento",
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