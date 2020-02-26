<template>
  <q-table
    title="Configurar niveles"
    :data="table"
    :columns="columns"
    row-key="name"
    binary-state-sort
    :loading="loading"
  >
    <template v-slot:top-right>
      <q-btn
        flat
        dense
        color="primary"
        :disable="loading"
        label="Agregar otro Nivel"
        @click="addLevel"
      />
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
        <q-td key="limit_months" :props="props">
          {{ props.row.limit_months }}
          <q-popup-edit
            v-model="props.row.limit_months"
            title="Actualizar vigencia"
            buttons
            @hide="updateLevel(props.row)"
          >
            <q-input
              clearable
              hide-bottom-space
              filled
              type="number"
              v-model="props.row.limit_months"
              dense
              autofocus
            ></q-input>
          </q-popup-edit>
        </q-td>
        <q-td key="actions" :props="props">
          <kButton
            color="grey"
            iconname="delete"
            tooltiplabel="Eliminar"
            @click="removeLevel(props.row)"
          ></kButton>
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
          label: "Nivel",
          align: "left",
          field: "level_name",
          sortable: true
        },
        {
          name: "required_points",
          align: "center",
          label: "Puntos Requeridos",
          field: "required_points",
          sortable: true
        },
        {
          name: "limit_months",
          label: "Meses de vigencia",
          field: "limit_months",
          sortable: true,
          style: "width: 10px"
        },
        {
          label: "Acciones",
          field: "actions",
          name: "actions",
          type: "string"
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