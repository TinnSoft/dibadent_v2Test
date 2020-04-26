<template>
  <q-table
    title="Actualización de puntos"
    :data="table"
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
      <!--<q-btn flat dense color="primary" :disable="loading" label="Pegar" @click="addLevel" />-->
      <q-btn flat round dense color="primary" :disable="loading" icon="content_paste" @click="addLevel" />
      <q-space />
    </template>

    <template v-slot:body="props">
      <q-tr :props="props">
        <q-td key="level_name" :props="props">
          {{ props.row.level_name }}
          <q-popup-edit v-model="props.row.level_name" @hide="updateLevel(props.row)">
            <q-input v-model="props.row.level_name" dense autofocus />
          </q-popup-edit>
        </q-td>
        <q-td key="required_points" :props="props">
          {{ props.row.required_points }}
          <q-popup-edit v-model="props.row.required_points" @hide="updateLevel(props.row)">
            <q-input type="number" v-model="props.row.required_points" dense autofocus />
          </q-popup-edit>
        </q-td>
        <q-td key="loaded_points" :props="props">
          {{ props.row.required_points }}
          <q-popup-edit v-model="props.row.required_points" @hide="updateLevel(props.row)">
            <q-input type="number" v-model="props.row.required_points" dense autofocus />
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
      columns: [
        {
          name: "level_name",
          required: true,
          label: "Cédula",
          align: "left",
          field: "level_name",
          sortable: true
        },
        {
          name: "required_points",
          align: "center",
          label: "Puntos actuales",
          field: "required_points",
          sortable: true
        },
        {
          name: "loaded_points",
          align: "center",
          label: "Puntos cargados",
          field: "required_points",
          sortable: true,
          classes: "bg-grey-2 ellipsis",
          headerClasses: "bg-primary text-white",
          style: "max-width: 100px"
        }
      ],
      table: [],
      pointsLevelPath: "getPointsLevelslist",
      loading: false
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      let vm = this;
      vm.loading = true;

      axios
        .get(`/api/${vm.pointsLevelPath}`)
        .then(function(response) {
          vm.$set(vm, "table", response.data.records);
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    },
    addLevel() {
      let vm = this;
      vm.loading = true;

      axios
        .post(`/api/points_levels`)
        .then(function(response) {
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
      vm.fetchData();
    },
    removeLevel(_row) {
      let vm = this;
      vm.loading = true;

      axios
        .delete("/api/points_levels/" + _row.id)
        .then(function(response) {
          if (response.data.deleted) {
            kNotify(
              vm,
              "Se eliminó el registro satisfactoriamente",
              "positive"
            );
            vm.fetchData();
            vm.loading = false;
          }
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
    },
    updateLevel(_row) {
      var vm = this;
      vm.errors = null;
      vm.loading = true;
      var requestLevel = {
        id: _row.id,
        points_levels: _row.points_levels,
        required_points: _row.required_points,
        level_name: _row.level_name,
        limit_months: _row.limit_months
      };

      axios
        .put(`/api/points_levels/${_row.id}`, requestLevel)
        .then(function(response) {
          if (response.data.updated) {
            kNotify(
              vm,
              "Se actualizó el registro satisfactoriamente",
              "positive"
            );
          }
          vm.fetchData();
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