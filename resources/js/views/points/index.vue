<template>
  <q-page padding>
    <q-toolbar :glossy="$q.theme === 'mat'" :inverted="$q.theme === 'ios'" color="grey">
      <q-toolbar-title class="text-weight-bold text-blue">Configuración de Puntos</q-toolbar-title>
    </q-toolbar>
    <div class="q-gutter-y-md column" style="max-width: 700px">
      <q-splitter v-model="splitterModel" style="height: 700px">
        <template v-slot:before>
          <q-tabs v-model="tab" vertical>
            <q-tab name="levels" icon="thumb_up" label="Niveles" />
            <q-tab name="promo" icon="local_offer" label="Promociones" />
            <q-tab name="vigence" icon="today" label="Vigencias" />
          </q-tabs>
        </template>

        <template v-slot:after>
          <q-tab-panels v-model="tab" animated transition-prev="jump-up" transition-next="jump-up">
            <q-tab-panel name="levels">
              <q-table
                title="Configurar niveles x puntos"
                :data="table"
                :columns="columns"
                row-key="name"
                binary-state-sort
                :loading="loading"
              >
                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td key="level_name" :props="props">
                      {{ props.row.level_name }}
                    </q-td>
                    <q-td key="required_points" :props="props">
                      {{ props.row.required_points }}
                      <q-popup-edit v-model="props.row.required_points">
                        <q-input v-model="props.row.required_points" dense autofocus counter />
                      </q-popup-edit>
                    </q-td>
                    <q-td key="limit_date" :props="props">
                      {{ props.row.time_limit }}
                      <q-popup-edit
                        v-model="props.row.limit_date"
                        title="Actualizar el limite de tiempo"
                        buttons
                      >
                        <q-input type="number" v-model="props.row.time_limit" dense autofocus />
                      </q-popup-edit>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
            </q-tab-panel>

            <q-tab-panel name="promo">
            </q-tab-panel>

            <q-tab-panel name="vigence">
            </q-tab-panel>
          </q-tab-panels>
        </template>
      </q-splitter>
    </div>
  </q-page>
</template>

<script>
export default {
  middleware: "auth",
  data() {
    return {
      tab: "mails",
      splitterModel: 20,
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
          name: "limit_date",
          label: "Limite de tiempo",
          field: "limit_date",
          sortable: true,
          style: "width: 10px"
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
          console.log( response.data.records)
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
</style>
