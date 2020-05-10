<template>
  <div class="q-pa-md">
    <q-card flat style="width: 100%">
      <q-card-section class="q-pt-none">
        <div class="text-caption text-grey">
          Aquí puedes visualizar y gestionar los productos a redimir solicitados por cada doctor,
          recuerde que puede aceptar o cancelar las solicitudes que aparecen abajo.
        </div>
      </q-card-section>
      <q-card-section>
        <q-table
          title="Validación de cupones para redimir"
          :data="redemptionHistory"
          :columns="columnsHistoryPointsRedemed"
          row-key="code"
          binary-state-sort
          :loading="loading"
          dense
          flat
          bordered
          :filter="filterTable"
          virtual-scroll
          :pagination.sync="pagination"
          :rows-per-page-options="[0]"
        >
          <template v-slot:top="props">
            <q-input dense debounce="300" v-model="filterTable" placeholder="Buscar">
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
              @click="props.toggleFullscreen"
              class="q-ml-md"
            >
              <q-tooltip>Ver en pantalla completa</q-tooltip>
            </q-btn>
          </template>
          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td key="description" :props="props">{{ props.row.description }}</q-td>
              <q-td key="points_redeemed" :props="props">{{ props.row.points_redeemed }}</q-td>
              <q-td key="doctor_name" :props="props">{{ props.row.doctor_name }}</q-td>
              <q-td key="code" :props="props">
                <q-badge color="teal">{{ props.row.code }}</q-badge>
              </q-td>
              <q-td key="created_at" :props="props">{{ props.row.created_at }}</q-td>
              <q-td key="state" :props="props">
                <q-badge :color="props.row.state==1?'green':'orange'">{{ status(props.row.state) }}</q-badge>
              </q-td>
              <q-td key="updated_at" :props="props">{{ props.row.updated_at }}</q-td>
              <q-td key="actions" :props="props">
                <q-btn
                  flat
                  :color="props.row.state==0?'green':'grey-4'"
                  :disable="props.row.state==1? true:false"
                  icon="check"
                  round
                  dense
                  @click="confirmProduct(props.row)"
                >
                  <q-tooltip>Confirmar</q-tooltip>
                </q-btn>

                <q-btn
                  flat
                  :color="props.row.state==0?'grey':'grey-4'"
                  :disable="props.row.state==1? true:false"
                  icon="close"
                  round
                  dense
                  @click="rejectProduct(props.row)"
                >
                  <q-tooltip>Reachazar</q-tooltip>
                </q-btn>
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
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
      pagination: {
        rowsPerPage: 15
      },
      filterTable: '',
      columnsHistoryPointsRedemed: [
        {
          name: "description",
          required: true,
          label: "PRODUCTO",
          align: "left",
          field: "description",
          sortable: true,
          headerClasses: "bg-primary text-white"
        },
        {
          name: "points_redeemed",
          align: "center",
          label: "PUNTOS REDIMIDOS",
          field: "points_redeemed",
          sortable: true,
          headerClasses: "bg-grey text-white"
        },
        {
          name: "doctor_name",
          align: "left",
          label: "DOCTOR",
          field: "doctor_name",
          sortable: true,
          headerClasses: "bg-grey text-white"
        },
        {
          name: "code",
          align: "left",
          label: "CUPON",
          field: "code",
          sortable: true,
          headerClasses: "bg-grey text-white"
        },
        {
          name: "created_at",
          align: "left",
          label: "FECHA RADICADO",
          field: "created_at",
          sortable: true,
          headerClasses: "bg-grey text-white"
        },
        {
          name: "state",
          align: "center",
          label: "ESTADO",
          field: "state",
          sortable: true
        },
        {
          name: "updated_at",
          align: "left",
          label: "FECHA ACTUALIZADO",
          field: "updated_at",
          sortable: true
        },
        {
          name: "actions",
          align: "left",
          label: "ACCIONES",
          field: "actions",
          sortable: true
        }
      ],
      productsDB: [],
      pointsLevelPath: "getProductRedemptionHistory",
      loading: false,
      redemptionHistory: []
    };
  },
  created() {
    this.fetchData(this.pointsLevelPath);
  },
  methods: {
    confirmProduct(val) {
      let vm = this;
      vm.loading = true;
      vm.$q
        .dialog({
          title: "Antes de continuar!",
          message:
            "Usted está confirmando el cupón <strong>" +
            val.code +
            "</strong>, lo cual implica que debe otorgarle al paciente dicho producto, desea continuar?",
          ok: {
            label: "SI, Confirmar!",
            flat: true
          },
          cancel: {
            color: "grey",
            label: "NO",
            flat: true
          },
          html: true
        })
        .onOk(() => {
          axios.post("/api/confirmCoupon/" + val.id).then(function(response) {
            if (response.data.updated) {
              kNotify(
                vm,
                "El cupón seleccionado fue confirmado correctamente",
                "positive"
              );
               vm.loading = false;
              vm.fetchData(vm.pointsLevelPath);
             
            }
          });
        })
        .onCancel(() => {
          vm.loading = false;
        })
        .onDismiss(() => {
          vm.loading = false;
        });
    },
    rejectProduct(val) {
      let vm = this;
      vm.loading = true;
      vm.$q
        .dialog({
          title: "Tenga cuidado!",
          message:
            "Usted está descartando el cupón <strong>" +
            val.code +
            "</strong>, lo cual implica que el pedido realizado por el doctor:" +
            val.doctor_name +
            " no se hará efectivo, desea continuar?",
          ok: {
            flat: true,
            label: "SI, Rechazar!"
          },
          cancel: {
            color: "grey",
            label: "NO",
            flat: true
          },
          html: true
        })
        .onOk(() => {
          axios.post("/api/rejectCoupon/" + val.id).then(function(response) {
            if (response.data.updated) {
              kNotify(
                vm,
                "El cupón seleccionado fue rechazado correctamente",
                "positive"
              );
              vm.loading = false;
              vm.fetchData(vm.pointsLevelPath);
              
            }
          });
        })
        .onCancel(() => {
          vm.loading = false;
        })
        .onDismiss(() => {
          vm.loading = false;
        });
    },
    status(val) {
      if (val === 1) {
        return "Confirmado";
      }
      return "Pendiente";
    },
    async fetchData(path) {
      let vm = this;
      vm.loading = true;
      axios
        .get(`/api/${path}`)
        .then(function(response) {
          if (response.data.redemptionHistory) {
            vm.$set(vm, "redemptionHistory", response.data.redemptionHistory);
          }
          vm.loading = false;
        })
        .catch(function(error) {
          vm.loading = false;
        });
    }
  }
};
</script>
