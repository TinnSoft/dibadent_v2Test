<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- <q-avatar size="100px" font-size="52px" color="grey-4" text-color="white" icon="person_outline" />-->
      <div class="row">
        <div class="col" style="max-width: 300px">
          <q-card class="my-card">
            <q-img
              src="https://d2x5ku95bkycr3.cloudfront.net/App_Themes/Common/images/profile/0_200.png"
              basic
            >
              <div class="absolute-bottom text-subtitle2 text-center">
                <q-btn flat color="white" label="Cambiar Imagen" />
              </div>
            </q-img>

            <q-list>
              <q-item clickable>
                <q-item-section>
                  <div class="text-h6 text-blue">Fernando Ardila</div>
                </q-item-section>
              </q-item>
              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="primary" name="bookmark_border" />
                </q-item-section>

                <q-item-section>
                  <q-item-label>Aún no tienes un nivel</q-item-label>
                  <q-item-label caption>Tu nivel de puntos Actual</q-item-label>
                </q-item-section>
              </q-item>

              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="primary" name="show_chart" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>1</q-item-label>
                  <q-item-label caption>Puntos Acumulados</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="primary" name="insert_emoticon" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>1</q-item-label>
                  <q-item-label caption>Puntos Redimidos ultimo mes</q-item-label>
                </q-item-section>
              </q-item>

              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="primary" name="info_outline" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>1</q-item-label>
                  <q-item-label caption>Puntos próximos a vencer</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </div>

        <div class="col">
          <q-card class="my-card">
            <q-card-section>
              <q-timeline color="primary">
                <q-timeline-entry heading body="Cargar Imagenes" />

                <q-timeline-entry subtitle="Paciente" icon="perm_identity">
                  <q-select
                    filled
                    v-model="model"
                    use-input
                    input-debounce="0"
                    label="Seleccione un Paciente"
                    :options="options"
                    @filter="filterFn"
                    dense
                    options-dense
                    style="width: 300px"
                  >
                    <template v-slot:no-option>
                      <q-item>
                        <q-item-section class="text-grey">Sin resultados</q-item-section>
                      </q-item>
                    </template>
                  </q-select>
                </q-timeline-entry>

                <q-timeline-entry subtitle="Comentarios" icon="insert_comment">
                  <div style="max-width: 300px">
                    <q-input v-model="comments" filled autogrow type="text" />
                  </div>
                </q-timeline-entry>

                <q-timeline-entry subtitle="Imagenes" icon="photo">
                  <q-tabs
                    v-model="tabSelected"
                    dense
                    class="text-grey"
                    active-color="primary"
                    indicator-color="primary"
                    align="justify"
                    narrow-indicator
                  >
                    <q-tab name="loadimage" label="Cargar" />
                    <q-tab name="images" label="Imagenes asociadas" />
                  </q-tabs>

                  <q-separator />

                  <q-tab-panels v-model="tabSelected" animated>
                    <q-tab-panel name="loadimage">
                      <q-uploader
                        url="http://localhost:4444/upload"
                        label="Cargar Imagen"
                        multiple
                        dense
                        batch
                        flat
                        accept=".jpg, image/*"
                        color="grey-5"
                        style="max-width: 300px"
                      />
                    </q-tab-panel>

                    <q-tab-panel name="images">
                      <div class="text-h6">Imagenes asociadas al paciente</div>
                    </q-tab-panel>
                  </q-tab-panels>
                </q-timeline-entry>
              </q-timeline>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
const stringOptions = ["Paciente 1", "Paciente 2", "Paciente 3"];

export default {
  middleware: "auth",
  components: {},
  data() {
    return {
      tabSelected: "loadimage",
      model: null,
      options: stringOptions,
      defaultTab: "levels",
      splitterModel: 20,
      comments: ""
    };
  },
  created() {},
  methods: {
    filterFn(val, update) {
      if (val === "") {
        update(() => {
          this.options = stringOptions;
        });
        return;
      }
      update(() => {
        const needle = val.toLowerCase();
        this.options = stringOptions.filter(
          v => v.toLowerCase().indexOf(needle) > -1
        );
      });
    }
  }
};
</script>
<style lang="sass">
.my-menu-link
  background: #E3ECFB 

.my-card 
  width: 100%
  max-width: 95%
</style>
