<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
      <q-dialog v-model="showImageByDoctor" persistent @hide="handleHide">
        <q-card style="width: 800px; max-width: 90vw;">
          <q-bar class="bg-blue text-white">
            <q-icon name="mail"></q-icon>
            <div>{{toolbarLabel}}</div>

            <q-space></q-space>

            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip>Cerrar</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <v-zoomer>
              <!-- <q-img :src="imageSource" spinner-color="white" >
                <template v-slot:error>
                  <div
                    class="absolute-full flex flex-center bg-negative text-white"
                  >No se pudo cargar la imagen</div>
                </template>
              </q-img>-->
              <img
               :src="imageSource"
                style="object-fit: contain; width: 100%; height: 100%;"
              />
            </v-zoomer>
          </q-card-section>
        </q-card>
      </q-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showImageByDoctor: false,
      toolbarLabel: "IMAGEN",
      imageAttributes: [],
      imageSource: null
    };
  },
  components: {},
  methods: {
    open(imageAttributes) {
      let vm = this;
      vm.imageSource = imageAttributes.file_name;
      vm.imageAttributes = imageAttributes;
      vm.showImageByDoctor = true;
    },
    handleHide(newVal) {
      this.$emit("hide", this.imageAttributes);
    }
  }
};
</script>
