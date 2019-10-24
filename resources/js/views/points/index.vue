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
                :data="data"
                :columns="columns"
                row-key="name"
                binary-state-sort
              >
                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td key="required_points" :props="props">
                      {{ props.row.required_points }}
                      <q-popup-edit v-model="props.row.required_points">
                        <q-input v-model="props.row.required_points" dense autofocus counter />
                      </q-popup-edit>
                    </q-td>
                    <q-td key="time_limit" :props="props">
                      {{ props.row.time_limit }}
                      <q-popup-edit
                        v-model="props.row.time_limit"
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
              <div class="text-h4 q-mb-md">Alarms</div>
              <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quis praesentium cumque magnam odio iure quidem, quod illum numquam possimus obcaecati commodi minima assumenda consectetur culpa fuga nulla ullam. In, libero.</p>
              <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quis praesentium cumque magnam odio iure quidem, quod illum numquam possimus obcaecati commodi minima assumenda consectetur culpa fuga nulla ullam. In, libero.</p>
            </q-tab-panel>

            <q-tab-panel name="vigence">
              <div class="text-h4 q-mb-md">Movies</div>
              <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quis praesentium cumque magnam odio iure quidem, quod illum numquam possimus obcaecati commodi minima assumenda consectetur culpa fuga nulla ullam. In, libero.</p>
              <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quis praesentium cumque magnam odio iure quidem, quod illum numquam possimus obcaecati commodi minima assumenda consectetur culpa fuga nulla ullam. In, libero.</p>
              <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quis praesentium cumque magnam odio iure quidem, quod illum numquam possimus obcaecati commodi minima assumenda consectetur culpa fuga nulla ullam. In, libero.</p>
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
          name: "level",
          required: true,
          label: "Nivel",
          align: "left",
          field: "level",
          sortable: true
        },
        {
          name: "required_points",
          align: "center",
          label: "Calories",
          field: "required_points",
          sortable: true
        },
        {
          name: "time_limit",
          label: "Limite de tiempo",
          field: "time_limit",
          sortable: true,
          style: "width: 10px"
        }
      ],
      data: []
    };
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 
</style>
