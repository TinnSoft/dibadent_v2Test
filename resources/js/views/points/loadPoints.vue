<template>
  <q-table
    title="Actualización de puntos"
    :data="pointsControl"
    :columns="columns"
    row-key="name"
    binary-state-sort
    :loading="loading"
    dense
    separator="horizontal"
    bordered
    flat
  >
    <template v-slot:top-right>
      <q-btn
        flat
        round
        dense
        color="primary"
        :disable="loading"
        icon="content_paste"
        @click="readFromClipboard"
      />
      <q-btn flat round dense color="primary" :disable="loading" icon="save" @click="updatePoints" />
      <q-space />
    </template>

    <template v-slot:body="props">
      <q-tr :props="props">
        <q-td key="user_name" :props="props">{{ props.row.user_name }}</q-td>
        <q-td key="identification_number" :props="props">{{ props.row.identification_number }}</q-td>
        <q-td key="available_points" :props="props">{{ props.row.available_points }}</q-td>
        <q-td key="new_points" :props="props">
          {{ props.row.new_points }}
          <q-popup-edit v-model="props.row.new_points">
            <q-input type="number" v-model="props.row.new_points" dense autofocus />
          </q-popup-edit>
        </q-td>
      </q-tr>
    </template>
  </q-table>
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
      ClipboardData: [],
      PointsToSave: [],
      columns: [
        {
          name: "user_name",
          required: true,
          label: "Nombre",
          align: "left",
          field: "user_name",
          sortable: true
        },
        {
          name: "identification_number",
          required: true,
          label: "Cédula",
          align: "left",
          field: "identification_number",
          sortable: true
        },
        {
          name: "available_points",
          align: "center",
          label: "Puntos actuales",
          field: "available_points",
          sortable: true
        },
        {
          name: "new_points",
          align: "center",
          label: "Puntos Cargados",
          field: "new_points",
          sortable: true,
          classes: "bg-grey-2 ellipsis",
          headerClasses: "bg-primary text-white",
          style: "max-width: 100px"
        }
      ],
      pointsControl: [],
      pointsLevelPath: "getPointsSummaryByDoctor",
      loading: false
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    readFromClipboard() {
      this.ClipboardData = [];
      this.PointsToSave = [];
      navigator.clipboard
        .readText()
        .then(text => {
          var rows = text
            .replace(/"((?:[^"]*(?:\r\n|\n\r|\n|\r))+[^"]+)"/gm, function(
              match,
              p1
            ) {
              return p1.replace(/""/g, '"').replace(/\r\n|\n\r|\n|\r/g, " ");
            })
            .split(/\r\n|\n\r|\n|\r/g);

          rows.forEach(element => this.updateLocalTable(element));
        })
        .catch(err => {});
    },
    updateLocalTable(val) {
      var separator = "	";
      var valuesplited = val.split(separator);

      if (valuesplited.length > 1) {
        const loadedPoints = this.pointsControl.find(
          _point => _point.identification_number === valuesplited[0]
        );

        if (loadedPoints) {
          const loadedPointsIndex = this.pointsControl.findIndex(
            _point => _point.identification_number === valuesplited[0]
          );

          this.pointsControl[loadedPointsIndex].new_points = valuesplited[1];
          const _value = Number(valuesplited[1]);

          if (_value > 0) {
            this.PointsToSave.push({
              user_id: this.pointsControl[loadedPointsIndex].id,
              value: _value,
              //created_at: new Date(),
              //updated_at: new Date()
            });
          }
        }
      }
    },
    fetchData() {
      let vm = this;
      vm.loading = true;

      axios
        .get(`/api/${vm.pointsLevelPath}`)
        .then(function(response) {
          console.log(response.data);
          vm.$set(vm, "pointsControl", response.data.records);
          vm.loading = false;
        })
        .catch(function(error) {
          console.log(error.response)
          vm.loading = false;
        });
    },
    updatePoints() {
      var vm = this;
      if (vm.PointsToSave.length > 0) {
        vm.loading = true;
        axios
          .put(`/api/store_NewPoints`, vm.PointsToSave)
          .then(function(response) {
            if (response.data.created) {
               console.log(response.data);
              kNotify(
                vm,
                "La tabla de puntos se actualizó correctamente",
                "positive"
              );
            }
            vm.ClipboardData = [];
            vm.PointsToSave = [];
            vm.fetchData();
            vm.loading = false;
          })
          .catch(function(error) {
            vm.loading = false;
            kNotify(
              vm,
              "No fue posible actualizar tu tabla de puntos, intente de nuevo.",
              "negative"
            );
          });
      }
    }
  }
};
</script>